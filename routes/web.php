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
Route::post('/visitor', [VisitorController::class, 'index'])->name('visitor.index');    //checked
Route::post('/addVisitor/save', [VisitorController::class, 'save'])->name('visitor.save');  //checked
Route::post('/addVisitor/timeout', [VisitorController::class, 'timeout'])->name('visitor.timeout'); //checked
Route::post('/addVisitor/view', [VisitorController::class, 'view'])->name('visitor.view');
Route::get('/visitors', [VisitorController::class, 'list'])->name('visitor.list');  //checked

// registered ID routes
Route::post('/IDNumber', [RegisterIDController::class, 'index'])->name('registerID.index');  //checked
Route::post('/registeredID/save', [RegisterIDController::class, 'save'])->name('registerID.save');  //checked
Route::post('/registeredID/edit', [RegisterIDController::class, 'edit'])->name('registerID.edit');
Route::post('/registeredID/delete', [RegisterIDController::class, 'delete'])->name('registerID.delete');
Route::get('/registeredIDs', [RegisterIDController::class, 'list'])->name('registerID.list'); //checked


// visitor types routes
Route::post('/visitor_type', [VisitorTypeController::class, 'index'])->name('visitorType.index');   //checked
Route::post('/visitorType/save', [VisitorTypeController::class, 'save'])->name('visitorType.save'); //checked
Route::post('/visitorType/edit', [VisitorTypeController::class, 'edit'])->name('visitorType.edit');
Route::post('/visitorType/delete', [VisitorTypeController::class, 'delete'])->name('visitorType.delete');
Route::get('/visitorTypes', [VisitorTypeController::class, 'list'])->name('visitorType.list');  //checked

