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
Route::post('/addVisitor/save', [VisitorController::class, 'save'])->name('visitor.save');  //checked
Route::post('/visitor/timeout', [VisitorController::class, 'timeoutAjax'])->name('visitor.timeout.ajax');
Route::get('/visitor/view/{visitor_id}', function ($visitor_id) {$visitor = Visitor::where('visitor_id', $visitor_id)
            ->latest('id')->firstOrFail();return view('homepage.view', compact('visitor'));})->name('visitor.view.page');
Route::post('/visitor/view', [VisitorController::class, 'view'])->name('visitor.view');

// Route::post('/addVisitor/timeout', [VisitorController::class, 'timeout'])->name('visitor.timeout'); //checked
// Route::post('/addVisitor/view', [VisitorController::class, 'view'])->name('visitor.view'); //checked
// Route::get('/addVisitor/timeout', [VisitorController::class, 'timeout'])->name('visitor.timeout'); //checked
// Route::get('/addVisitor/view', [VisitorController::class, 'view'])->name('visitor.view'); //checked
// Route::get('/visitors', [VisitorController::class, 'list'])->name('visitor.list');  //checked

// registered ID routes
Route::post('/IDNumber', [RegisterIDController::class, 'index'])->name('registerID.index');  //checked
Route::post('/registeredID/save', [RegisterIDController::class, 'save'])->name('registerID.save');  //checked
Route::post('/registeredID/edit', [RegisterIDController::class, 'edit'])->name('registerID.edit');
Route::post('/registeredID/delete', [RegisterIDController::class, 'delete'])->name('registerID.delete');
Route::get('/registeredID/edit', [RegisterIDController::class, 'edit'])->name('registerID.edit');
Route::get('/registeredID/delete', [RegisterIDController::class, 'delete'])->name('registerID.delete');
// Route::get('/registeredIDs', [RegisterIDController::class, 'list'])->name('registerID.list'); //checked

// visitor types routes
Route::post('/visitor_type', [VisitorTypeController::class, 'index'])->name('visitorType.index');   //checked
Route::post('/visitorType/save', [VisitorTypeController::class, 'save'])->name('visitorType.save'); //checked
Route::post('/visitorType/delete', [VisitorTypeController::class, 'deleteAjax'])->name('visitorType.delete.ajax');
Route::post('/visitorType/edit', [VisitorTypeController::class, 'edit'])->name('visitorType.edit');
Route::get('/visitorType/edit', [VisitorTypeController::class, 'edit'])->name('visitorType.edit');
// Route::get('/visitorType/delete', [VisitorTypeController::class, 'delete'])->name('visitorType.delete');
// Route::post('/visitorType/delete', [VisitorTypeController::class, 'delete'])->name('visitorType.delete');
// Route::get('/visitorTypes', [VisitorTypeController::class, 'list'])->name('visitorType.list');  //checked

// Route::post('/name na tatawaging file try', [TryController::class, 'function na tatawagin or gagamitin show']);
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
