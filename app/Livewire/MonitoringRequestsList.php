<?php

namespace App\Livewire;

use App\Models\MonitoringRequest;
use Livewire\Component;

class MonitoringRequestsList extends Component
{
    public function render()
    {
        $requests = MonitoringRequest::orderBy('created_at', 'desc')->get();

        return view('livewire.monitoring-requests-list', [
            'requests' => $requests,
        ]);
    }
}
