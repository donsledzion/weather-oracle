<?php

use App\Http\Controllers\GuestDashboardController;
use App\Http\Controllers\NotificationPreferencesController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RequestVerificationController;
use Illuminate\Support\Facades\Route;

// Public home page (landing/welcome - no monitoring data visible)
Route::get('/', function () {
    return view('welcome');
})->name('home');

// Email verification routes (for guests)
Route::get('/verify/{token}', [RequestVerificationController::class, 'verify'])->name('requests.verify');
Route::get('/reject/{token}', [RequestVerificationController::class, 'reject'])->name('requests.reject');

// Guest dashboard (token-based, no auth required)
Route::get('/guest-dashboard/{token}', [GuestDashboardController::class, 'show'])->name('guest.dashboard');

// Notification preferences (token-based, no auth required)
Route::get('/notifications/{token}', [NotificationPreferencesController::class, 'show'])->name('notifications.show');
Route::post('/notifications/{token}/global', [NotificationPreferencesController::class, 'updateGlobal'])->name('notifications.update-global');
Route::post('/notifications/{token}/toggle/{requestId}', [NotificationPreferencesController::class, 'toggleRequest'])->name('notifications.toggle-request');

// Authenticated user dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Request details - accessible by both authenticated users and guests
Route::get('/requests/{id}', function ($id) {
    $request = \App\Models\MonitoringRequest::findOrFail($id);

    // Authorization check
    if (auth()->check()) {
        // Logged in user: must own the request
        if ($request->user_id !== auth()->id()) {
            abort(403, 'Unauthorized access to this request');
        }
    } else {
        // Guest: must have valid dashboard_token in session or URL
        // We'll allow access if they came from guest-dashboard
        // This is secure because guest-dashboard already validates the token
        $allowedEmail = session('guest_email');
        if (!$allowedEmail || $request->email !== $allowedEmail) {
            abort(403, 'Unauthorized access to this request');
        }
    }

    return view('request-details', ['requestId' => $id]);
})->name('requests.show');

// Authenticated user routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
