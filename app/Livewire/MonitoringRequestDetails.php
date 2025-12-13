<?php

namespace App\Livewire;

use App\Models\MonitoringRequest;
use Livewire\Component;

class MonitoringRequestDetails extends Component
{
    public MonitoringRequest $request;

    public function mount($requestId)
    {
        $this->request = MonitoringRequest::with(['forecastSnapshots.weatherProvider'])->findOrFail($requestId);
    }

    public function render()
    {
        return view('livewire.monitoring-request-details');
    }
}
