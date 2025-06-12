<?php

use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;
// Route::get('/', function () {
//     return view('welcome');
// });

Route::get('/dashboard', function () {
    return response()->file(public_path('dashboard.html'));
});

