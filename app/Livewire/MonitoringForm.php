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
    #[Validate('required|string|min:2')]
    public $location = '';

    #[Validate('required|date|after:today')]
    public $targetDate = '';

    #[Validate('nullable|email')]
    public $email = '';

    public function submit()
    {
        $this->validate();

        // Check if email has reached limit (5 active + pending)
        if ($this->email && MonitoringRequest::activeAndPendingCountForEmail($this->email) >= 5) {
            session()->flash('error', __('app.guest_limit_reached'));
            return;
        }

        // Generate tokens
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
        if ($this->email) {
            $verifyUrl = route('requests.verify', $verificationToken);
            $rejectUrl = route('requests.reject', $verificationToken);
            $dashboardUrl = route('guest.dashboard', $dashboardToken);

            Mail::to($this->email)->send(new RequestVerificationEmail(
                $monitoringRequest,
                $verifyUrl,
                $rejectUrl,
                $dashboardUrl
            ));
        }

        // Clear form and validation errors
        $this->reset(['location', 'targetDate', 'email']);
        $this->resetValidation();

        // Dispatch event to refresh the list
        $this->dispatch('request-created');

        // Show message
        session()->flash('message', __('app.request_created_verify_email'));
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
