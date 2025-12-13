<?php

namespace App\Livewire;

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

        // TODO: Create monitoring request
        session()->flash('message', 'Monitoring request created successfully!');

        $this->reset();
    }

    public function render()
    {
        return view('livewire.monitoring-form');
    }
}
