<?php

use App\Http\Controllers\GuestDashboardController;
use App\Http\Controllers\ProfileController;
use App\Http\Controllers\RequestVerificationController;
use Illuminate\Support\Facades\Route;

// Public home page
Route::get('/', function () {
    return view('home');
})->name('home');

// Public request details
Route::get('/requests/{id}', function ($id) {
    return view('request-details', ['requestId' => $id]);
})->name('requests.show');

// Email verification routes (for guests)
Route::get('/verify/{token}', [RequestVerificationController::class, 'verify'])->name('requests.verify');
Route::get('/reject/{token}', [RequestVerificationController::class, 'reject'])->name('requests.reject');

// Guest dashboard (token-based, no auth required)
Route::get('/guest-dashboard/{token}', [GuestDashboardController::class, 'show'])->name('guest.dashboard');

// Authenticated user dashboard
Route::get('/dashboard', function () {
    return view('dashboard');
})->middleware(['auth', 'verified'])->name('dashboard');

// Authenticated user routes
Route::middleware('auth')->group(function () {
    Route::get('/profile', [ProfileController::class, 'edit'])->name('profile.edit');
    Route::patch('/profile', [ProfileController::class, 'update'])->name('profile.update');
    Route::delete('/profile', [ProfileController::class, 'destroy'])->name('profile.destroy');
});

require __DIR__.'/auth.php';
