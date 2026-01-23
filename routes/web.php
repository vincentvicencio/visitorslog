<?php

use App\Http\Controllers\TryController;
use Illuminate\Support\Facades\Route;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// Route::post('/name na tatawaging file try', [TryController::class, 'function na tatawagin or gagamitin show']);

Route::post('/visitorlog', [TryController::class, 'show']);
Route::get('/visitorlog', [TryController::class, 'show']);


Route::post('/usertype', [TryController::class, 'show_usertype']);
Route::get('/usertype', [TryController::class, 'show_usertype']);

Route::post('/users', [TryController::class, 'show_user']);
Route::get('/users', [TryController::class, 'show_user']);


Route::post('/visitortype', [TryController::class, 'show_visitortype']);
Route::get('/visitortype', [TryController::class, 'show_visitortype']);

Route::post('/id', [TryController::class, 'show_id']);
Route::get('/id', [TryController::class, 'show_id']);

Route::post('/report', [TryController::class, 'show_report']);
Route::get('/report', [TryController::class, 'show_report']);