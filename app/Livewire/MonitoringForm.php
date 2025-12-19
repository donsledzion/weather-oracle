<?php

namespace App\Livewire;

use App\Mail\RequestVerificationEmail;
use App\Models\ForecastSnapshot;
use App\Models\MonitoringRequest;
use App\Models\WeatherProvider;
use App\Services\WeatherProviderFactory;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Str;
use Livewire\Attributes\Validate;
use Livewire\Component;

class MonitoringForm extends Component
{
    public $location = '';
    public $targetDate = '';
    public $email = '';

    public function mount()
    {
        // Auto-fill email for logged in users
        if (auth()->check()) {
            $this->email = auth()->user()->email;
        }
    }

    protected function rules()
    {
        return [
            'location' => 'required|string|min:2',
            'targetDate' => 'required|date|after:today',
            'email' => auth()->check() ? 'nullable' : 'required|email',
        ];
    }

    public function submit()
    {
        $this->validate();

        $user = auth()->user();

        // Use user's email if logged in
        if ($user) {
            $this->email = $user->email;
        }

        // Check limits
        if ($user) {
            // Logged in user: check 20 active limit
            if (MonitoringRequest::activeCountForUser($user->id) >= 20) {
                session()->flash('error', __('app.user_limit_reached'));
                return;
            }
        } else {
            // Guest: check 5 active + pending limit
            if (MonitoringRequest::activeAndPendingCountForEmail($this->email) >= 5) {
                session()->flash('error', __('app.guest_limit_reached'));
                return;
            }
        }

        // Logged in users: create active request immediately (no verification)
        if ($user) {
            $monitoringRequest = MonitoringRequest::create([
                'user_id' => $user->id,
                'location' => $this->location,
                'target_date' => $this->targetDate,
                'email' => $this->email,
                'status' => MonitoringRequest::STATUS_ACTIVE,
            ]);

            // Immediately fetch initial forecasts
            $this->fetchInitialForecasts($monitoringRequest);

            session()->flash('message', __('app.request_created_success'));
        } else {
            // Guest: create pending request with email verification
            $verificationToken = Str::random(64);
            $dashboardToken = $this->getDashboardTokenForEmail($this->email);

            $monitoringRequest = MonitoringRequest::create([
                'location' => $this->location,
                'target_date' => $this->targetDate,
                'email' => $this->email,
                'status' => MonitoringRequest::STATUS_PENDING_VERIFICATION,
                'verification_token' => $verificationToken,
                'dashboard_token' => $dashboardToken,
                'expires_at' => now()->addHours(2),
            ]);

            // Send verification email
            $verifyUrl = route('requests.verify', $verificationToken);
            $rejectUrl = route('requests.reject', $verificationToken);
            $dashboardUrl = route('guest.dashboard', $dashboardToken);

            Mail::to($this->email)->send(new RequestVerificationEmail(
                $monitoringRequest,
                $verifyUrl,
                $rejectUrl,
                $dashboardUrl
            ));

            session()->flash('message', __('app.request_created_verify_email'));
        }

        // Clear form and validation errors
        $this->reset(['location', 'targetDate']);
        if (!$user) {
            $this->reset('email');
        }
        $this->resetValidation();

        // Dispatch event to refresh the list
        $this->dispatch('request-created');
    }

    /**
     * Fetch initial forecasts for newly created request
     */
    protected function fetchInitialForecasts(MonitoringRequest $request): void
    {
        $activeProviders = WeatherProvider::where('is_active', true)->get();

        foreach ($activeProviders as $provider) {
            try {
                $weatherService = WeatherProviderFactory::make($provider);

                $forecastData = $weatherService->getForecast(
                    $request->location,
                    $request->target_date->format('Y-m-d')
                );

                // Check if forecast date matches target date (±1 day tolerance)
                $forecastDate = new \DateTime($forecastData['forecast_date']);
                $targetDate = new \DateTime($request->target_date->format('Y-m-d'));
                $daysDiff = abs($forecastDate->diff($targetDate)->days);

                // Only save snapshot if forecast is for the target date
                if ($daysDiff <= 1) {
                    ForecastSnapshot::create([
                        'monitoring_request_id' => $request->id,
                        'weather_provider_id' => $provider->id,
                        'forecast_data' => $forecastData,
                        'fetched_at' => now(),
                    ]);
                }
            } catch (\Exception $e) {
                // Silently fail - scheduler will retry later
                \Log::warning("Failed to fetch initial forecast from {$provider->name}: {$e->getMessage()}");
            }
        }
    }

    /**
     * Get or create dashboard token for email (reuse existing token if available)
     */
    protected function getDashboardTokenForEmail(string $email): string
    {
        // Try to find existing dashboard token for this email
        $existingRequest = MonitoringRequest::where('email', $email)
            ->whereNotNull('dashboard_token')
            ->first();

        if ($existingRequest) {
            return $existingRequest->dashboard_token;
        }

        // Generate new token
        return Str::random(64);
    }

    public function render()
    {
        return view('livewire.monitoring-form');
    }
}
