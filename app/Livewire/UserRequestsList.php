<?php

namespace App\Livewire;

use App\Models\MonitoringRequest;
use Livewire\Component;

class UserRequestsList extends Component
{
    protected $listeners = ['request-created' => '$refresh'];

    public function deleteRequest($id)
    {
        $request = MonitoringRequest::where('id', $id)
            ->where('user_id', auth()->id())
            ->first();

        if ($request) {
            $request->delete();
            session()->flash('message', __('app.request_deleted'));
        }
    }

    public function render()
    {
        $requests = MonitoringRequest::where('user_id', auth()->id())
            ->orderByRaw("FIELD(status, 'active', 'pending_verification', 'completed', 'expired', 'rejected')")
            ->orderBy('created_at', 'desc')
            ->get();

        return view('livewire.user-requests-list', [
            'requests' => $requests,
        ]);
    }
}
