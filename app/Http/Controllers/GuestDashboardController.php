<?php

namespace App\Http\Controllers;

use App\Models\MonitoringRequest;

class GuestDashboardController extends Controller
{
    /**
     * Show guest dashboard with all requests for this email
     */
    public function show(string $token)
    {
        // Find any request with this dashboard token to get the email
        $sampleRequest = MonitoringRequest::where('dashboard_token', $token)->first();

        if (!$sampleRequest) {
            abort(404, 'Dashboard not found');
        }

        // Get all requests for this email, ordered by status and date
        $requests = MonitoringRequest::where('email', $sampleRequest->email)
            ->orderByRaw("FIELD(status, 'pending_verification', 'active', 'completed', 'expired', 'rejected')")
            ->orderBy('created_at', 'desc')
            ->get();

        return view('guest-dashboard', [
            'requests' => $requests,
            'email' => $sampleRequest->email,
            'dashboardToken' => $token,
        ]);
    }
}
