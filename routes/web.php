<?php

use App\Http\Controllers\RequestVerificationController;
use App\Http\Controllers\GuestDashboardController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/requests/{id}', function ($id) {
    return view('request-details', ['requestId' => $id]);
})->name('requests.show');

// Email verification routes
Route::get('/verify/{token}', [RequestVerificationController::class, 'verify'])->name('requests.verify');
Route::get('/reject/{token}', [RequestVerificationController::class, 'reject'])->name('requests.reject');

// Guest dashboard
Route::get('/dashboard/{token}', [GuestDashboardController::class, 'show'])->name('guest.dashboard');
