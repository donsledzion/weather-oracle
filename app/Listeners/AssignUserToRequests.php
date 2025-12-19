<?php

namespace App\Listeners;

use App\Models\MonitoringRequest;
use Illuminate\Auth\Events\Verified;

class AssignUserToRequests
{
    /**
     * Handle the event - assign all requests from user's email to their account
     */
    public function handle(Verified $event): void
    {
        $user = $event->user;

        // Find all monitoring requests with this email that don't have a user_id yet
        MonitoringRequest::where('email', $user->email)
            ->whereNull('user_id')
            ->update(['user_id' => $user->id]);
    }
}
