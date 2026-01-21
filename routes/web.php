<?php

use App\Http\Controllers\TryController;
use Illuminate\Support\Facades\Route;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Route::post('/name na tatawaging file try', [TryController::class, 'function na tatawagin or gagamitin show']);

Route::post('/visitorlog', [TryController::class, 'show']);
