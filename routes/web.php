<?php

use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VisitorController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::post('/form', [App\Http\Controllers\VisitorController::class, 'index'])->name('form');

Route::post('/visitor/store', [VisitorController::class, 'store'])->name('visitor.store');
