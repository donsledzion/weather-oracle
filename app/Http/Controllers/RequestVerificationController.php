<?php

namespace App\Http\Controllers;

use App\Models\MonitoringRequest;
use Illuminate\Http\Request;

class RequestVerificationController extends Controller
{
    /**
     * Verify and activate a monitoring request
     */
    public function verify(string $token)
    {
        $request = MonitoringRequest::where('verification_token', $token)
            ->where('status', MonitoringRequest::STATUS_PENDING_VERIFICATION)
            ->first();

        if (!$request) {
            return view('verification-result', [
                'success' => false,
                'message' => 'Invalid or expired verification link.',
            ]);
        }

        // Check if expired
        if ($request->expires_at && $request->expires_at->isPast()) {
            $request->update(['status' => MonitoringRequest::STATUS_EXPIRED]);

            return view('verification-result', [
                'success' => false,
                'message' => 'This verification link has expired (2 hours limit).',
            ]);
        }

        // Activate request
        $request->update(['status' => MonitoringRequest::STATUS_ACTIVE]);

        // Redirect to guest dashboard
        return redirect()->route('guest.dashboard', ['token' => $request->dashboard_token])
            ->with('success', 'Your monitoring request has been activated!');
    }

    /**
     * Reject a monitoring request
     */
    public function reject(string $token)
    {
        $request = MonitoringRequest::where('verification_token', $token)
            ->whereIn('status', [MonitoringRequest::STATUS_PENDING_VERIFICATION, MonitoringRequest::STATUS_ACTIVE])
            ->first();

        if (!$request) {
            return view('verification-result', [
                'success' => false,
                'message' => 'Invalid link or request already processed.',
            ]);
        }

        // Mark as rejected
        $request->update(['status' => MonitoringRequest::STATUS_REJECTED]);

        return view('verification-result', [
            'success' => true,
            'message' => 'Your monitoring request has been cancelled.',
        ]);
    }
}
