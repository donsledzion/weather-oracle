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
        $requests = MonitoringRequest::orderBy('created_at', 'desc')->get();

        return view('livewire.monitoring-requests-list', [
            'requests' => $requests,
        ]);
    }
}
