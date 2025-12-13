<?php

use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('home');
});

Route::get('/requests/{id}', function ($id) {
    return view('request-details', ['requestId' => $id]);
})->name('requests.show');
