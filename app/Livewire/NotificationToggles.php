<?php

namespace App\Livewire;

use App\Models\NotificationPreference;
use App\Models\MonitoringRequest;
use Livewire\Component;

class NotificationToggles extends Component
{
    public string $token;
    public $preferences;
    public $requests;

    // Global preferences
    public bool $firstSnapshotEnabled;
    public bool $dailySummaryEnabled;
    public bool $finalSummaryEnabled;

    public function mount(string $token)
    {
        $this->token = $token;
        $this->loadPreferences();
    }

    public function loadPreferences()
    {
        $this->preferences = NotificationPreference::getByToken($this->token);

        if (!$this->preferences) {
            abort(404);
        }

        // Load global preferences
        $this->firstSnapshotEnabled = $this->preferences->first_snapshot_enabled;
        $this->dailySummaryEnabled = $this->preferences->daily_summary_enabled;
        $this->finalSummaryEnabled = $this->preferences->final_summary_enabled;

        // Load requests
        $this->requests = $this->preferences->user_id
            ? MonitoringRequest::where('user_id', $this->preferences->user_id)->get()
            : MonitoringRequest::where('email', $this->preferences->email)->get();
    }

    public function toggleGlobal(string $type)
    {
        match($type) {
            'first_snapshot' => $this->firstSnapshotEnabled = !$this->firstSnapshotEnabled,
            'daily_summary' => $this->dailySummaryEnabled = !$this->dailySummaryEnabled,
            'final_summary' => $this->finalSummaryEnabled = !$this->finalSummaryEnabled,
        };

        $this->preferences->update([
            'first_snapshot_enabled' => $this->firstSnapshotEnabled,
            'daily_summary_enabled' => $this->dailySummaryEnabled,
            'final_summary_enabled' => $this->finalSummaryEnabled,
        ]);

        $this->dispatch('notification-updated', message: __('app.preferences_updated'));
    }

    public function toggleRequest(int $requestId)
    {
        $request = MonitoringRequest::where('id', $requestId)
            ->where(function ($query) {
                if ($this->preferences->user_id) {
                    $query->where('user_id', $this->preferences->user_id);
                } else {
                    $query->where('email', $this->preferences->email);
                }
            })
            ->firstOrFail();

        $request->update([
            'notifications_enabled' => !$request->notifications_enabled,
        ]);

        // Refresh requests
        $this->loadPreferences();
    }

    public function render()
    {
        return view('livewire.notification-toggles');
    }
}
