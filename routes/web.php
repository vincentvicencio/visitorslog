<?php

use App\Http\Controllers\RegisterIDController;
use App\Http\Controllers\VisitorTypeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VisitorController;
use App\Models\Visitor;
use App\Http\Controllers\TryController;
use Illuminate\Support\Facades\Auth;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// home route
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

// visitor routes
Route::post('/visitor', [VisitorController::class, 'index'])->name('visitor.index');    //checked
Route::post('/visitor/save', [VisitorController::class, 'save'])->name('visitor.save');  //checked
Route::post('/visitor/timeout', [VisitorController::class, 'timeoutAjax'])->name('visitor.timeout.ajax');
Route::get('/visitor/view/{id}', function ($id) {$visitor = Visitor::where('id', $id)
            ->latest('id')->firstOrFail();return view('homepage.view', compact('visitor'));})->name('visitor.view.page');
Route::post('/visitor/view', [VisitorController::class, 'view'])->name('visitor.view'); 


// registered ID routes
Route::post('/IDNumber', [RegisterIDController::class, 'index'])->name('registerID.index');  //checked
Route::post('/registeredID/save', [RegisterIDController::class, 'save'])->name('registerID.save');  //checked
Route::post('/registeredID/delete', [RegisterIDController::class, 'deleteAjax'])->name('registerID.delete.ajax'); //checked
Route::post('/registeredID/edit', [RegisterIDController::class, 'editAjax'])->name('registerID.edit.ajax'); //checked

// visitor types routes
Route::post('/visitor_type', [VisitorTypeController::class, 'index'])->name('visitorType.index');   //checked
Route::post('/visitorType/save', [VisitorTypeController::class, 'save'])->name('visitorType.save'); //checked
Route::post('/visitorType/delete', [VisitorTypeController::class, 'deleteAjax'])->name('visitorType.delete.ajax'); //checked
Route::post('/visitorType/edit', [VisitorTypeController::class, 'editAjax'])->name('visitorType.edit.ajax'); //checked

Route::post('/visitorlog', [TryController::class, 'show'])->name('visitorlog.index');
Route::get('/visitorlog', [TryController::class, 'show'])->name('visitorlog.index');


Route::post('/usertype', [TryController::class, 'show_usertype'])->name('usertype.index');
Route::get('/usertype', [TryController::class, 'show_usertype'])->name('usertype.index');

Route::post('/users', [TryController::class, 'show_user'])->name('users.index');
Route::get('/users', [TryController::class, 'show_user'])->name('users.index');


Route::post('/visitortype', [TryController::class, 'show_visitortype'])->name('visitortype.index');
Route::get('/visitortype', [TryController::class, 'show_visitortype'])->name('visitortype.index');

Route::post('/id', [TryController::class, 'show_id'])->name('id.index');
Route::get('/id', [TryController::class, 'show_id'])->name('id.index');

Route::post('/report', [TryController::class, 'show_report'])->name('report.index');
Route::get('/report', [TryController::class, 'show_report'])->name('report.index');
