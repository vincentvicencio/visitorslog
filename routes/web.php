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
Route::post('/visitor', [VisitorController::class, 'index'])->name('visitor.index');
Route::post('/addVisitor/save', [VisitorController::class, 'save'])->name('visitor.save');
Route::post('/addVisitor/timeout', [VisitorController::class, 'timeout'])->name('visitor.timeout');
Route::post('/addVisitor/view', [VisitorController::class, 'view'])->name('visitor.view');
Route::get('/visitors', [VisitorController::class, 'list'])->name('visitor.list');

// registered ID routes
Route::post('/IDNumber', [RegisterIDController::class, 'index'])->name('registerID.index');
Route::post('/registeredID/save', [RegisterIDController::class, 'save'])->name('registerID.save');
Route::post('/registeredID/edit', [RegisterIDController::class, 'edit'])->name('registerID.edit');
Route::post('/registeredID/delete', [RegisterIDController::class, 'delete'])->name('registerID.delete');
Route::get('/registeredIDs', [RegisterIDController::class, 'list'])->name('registerID.list');


// visitor types routes
Route::post('/visitor_type', [VisitorTypeController::class, 'index'])->name('visitorType.index');
Route::post('/visitorType/save', [VisitorTypeController::class, 'save'])->name('visitorType.save');
Route::post('/visitorType/edit', [VisitorTypeController::class, 'edit'])->name('visitorType.edit');
Route::post('/visitorType/delete', [VisitorTypeController::class, 'delete'])->name('visitorType.delete');
Route::get('/visitorTypes', [VisitorTypeController::class, 'list'])->name('visitorType.list');

