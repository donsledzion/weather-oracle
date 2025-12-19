<?php

namespace App\Http\Controllers;

use App\Models\MonitoringRequest;
use App\Models\NotificationPreference;
use Illuminate\Http\Request;

class NotificationPreferencesController extends Controller
{
    /**
     * Show notification preferences panel (token-based, no auth required)
     */
    public function show(string $token)
    {
        $preferences = NotificationPreference::getByToken($token);

        if (!$preferences) {
            abort(404, 'Notification preferences not found');
        }

        // Get all monitoring requests for this email/user
        $requests = $preferences->user_id
            ? MonitoringRequest::where('user_id', $preferences->user_id)->get()
            : MonitoringRequest::where('email', $preferences->email)->get();

        return view('notification-preferences', [
            'preferences' => $preferences,
            'requests' => $requests,
            'token' => $token,
        ]);
    }

    /**
     * Update global notification preferences
     */
    public function updateGlobal(Request $request, string $token)
    {
        $preferences = NotificationPreference::getByToken($token);

        if (!$preferences) {
            abort(404);
        }

        $preferences->update([
            'first_snapshot_enabled' => $request->boolean('first_snapshot_enabled'),
            'daily_summary_enabled' => $request->boolean('daily_summary_enabled'),
            'final_summary_enabled' => $request->boolean('final_summary_enabled'),
        ]);

        return redirect()->route('notifications.show', $token)
            ->with('success', __('app.preferences_updated'));
    }

    /**
     * Toggle notifications for specific monitoring request
     */
    public function toggleRequest(Request $request, string $token, int $requestId)
    {
        $preferences = NotificationPreference::getByToken($token);

        if (!$preferences) {
            abort(404);
        }

        $monitoringRequest = MonitoringRequest::where('id', $requestId)
            ->where(function ($query) use ($preferences) {
                if ($preferences->user_id) {
                    $query->where('user_id', $preferences->user_id);
                } else {
                    $query->where('email', $preferences->email);
                }
            })
            ->firstOrFail();

        $monitoringRequest->update([
            'notifications_enabled' => !$monitoringRequest->notifications_enabled,
        ]);

        return redirect()->route('notifications.show', $token);
    }
}
