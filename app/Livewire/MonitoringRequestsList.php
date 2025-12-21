<?php

namespace App\Livewire;

use App\Models\MonitoringRequest;
use Livewire\Attributes\On;
use Livewire\Component;

class MonitoringRequestsList extends Component
{
    #[On('request-created')]
    public function refreshList()
    {
        // This method will be called when the 'request-created' event is dispatched
        // Livewire automatically re-renders the component
    }

    public function render()
    {
        // REMOVED: This component should NOT be used publicly
        // It was showing all active requests to everyone (SECURITY ISSUE)
        // Use dashboard (auth required) or guest-dashboard (token-based) instead

        return view('livewire.monitoring-requests-list', [
            'requests' => collect(), // Empty collection
        ]);
    }
}
