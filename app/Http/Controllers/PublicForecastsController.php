<?php

namespace App\Http\Controllers;

use App\Models\PublicMonitorLocation;
use App\Models\MonitoringRequest;
use Illuminate\Http\Request;

class PublicForecastsController extends Controller
{
    public function index()
    {
        $locations = PublicMonitorLocation::where('is_active', true)
            ->orderBy('name')
            ->get();

        $publicRequests = MonitoringRequest::where('is_public', true)
            ->whereIn('status', [
                MonitoringRequest::STATUS_ACTIVE,
                MonitoringRequest::STATUS_PENDING_VERIFICATION,
                MonitoringRequest::STATUS_COMPLETED
            ])
            ->orderByRaw("FIELD(status, 'active', 'pending_verification', 'completed')")
            ->orderBy('created_at', 'desc')
            ->get()
            ->groupBy('location');

        return view('public-forecasts', [
            'locations' => $locations,
            'publicRequests' => $publicRequests,
        ]);
    }
}
