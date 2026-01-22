<?php

use App\Http\Controllers\RegisterIDController;
use App\Http\Controllers\VisitorTypeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VisitorController;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// home route
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// visitor routes
Route::get('/form', [VisitorController::class, 'index'])->name('form');
Route::post('/visitor/store', [VisitorController::class, 'store'])->name('visitor.store');

// registered ID routes
Route::post('/IDNumber', [RegisterIDController::class, 'index'])->name('registerID.index');
Route::post('/registeredID/save', [RegisterIDController::class, 'save'])->name('registerID.save');
Route::get('/registeredIDs', [RegisterIDController::class, 'list'])->name('registerID.list');


// visitor types routes
Route::post('/visitor_type', [VisitorTypeController::class, 'index'])->name('visitorType.index');
Route::post('/visitorType/save', [VisitorTypeController::class, 'save'])->name('visitorType.save');
Route::get('/visitorTypes', [VisitorTypeController::class, 'list'])->name('visitorType.list');

