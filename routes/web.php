<?php

use App\Http\Controllers\RegisterIDController;
use App\Http\Controllers\VisitorTypeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VisitorController;
use App\Models\Visitor;
use App\Http\Controllers\TryController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\User_TypesController;
use App\Http\Controllers\Registered_UsersController;
use App\Http\Controllers\HomeController;
use App\Models\RegisteredUser;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {
    return redirect()->route('visitorlog.index');
});

Auth::routes();
Route::middleware(['auth'])->group(function () {

Route::get('/visitorlog', [TryController::class, 'show'])->name('visitorlog.index');
Route::get('/visitorlog-admin', [HomeController::class, 'index'])->name('visitorlog');

Route::post('/addusers', [Registered_UsersController::class, 'addusers']);
Route::post('/update-user/{id}', [Registered_UsersController::class, 'updateUser']);

Route::post('/addusertype', [User_TypesController::class, 'addusertype'])->name('addusertype');
Route::post('/addusers', [Registered_UsersController::class, 'addusers'])->name('addusers');

Route::get('/get-user/{id}', [Registered_UsersController::class, 'getUser']);

Route::get('/usertype/{id}/edit', [User_TypesController::class, 'edit']);
Route::put('/usertype/{id}', [User_TypesController::class, 'update']);
Route::delete('/usertype/{id}', [User_TypesController::class, 'destroy']);

Route::get('/usertype', [TryController::class, 'show_usertype'])->name('usertype');

Route::post('/add-user-type', [User_TypesController::class, 'addusertype'])->name('addusertype');

Route::delete('/delete-visitor/{id}', [TryController::class, 'destroy'])->name('visitors.destroy');
Route::delete('/visitors/{id}', [TryController::class, 'destroy'])->name('visitors.destroy');

Route::post('/getlocation', [Registered_UsersController::class, 'location'])->name('locations.lookup');
Route::get('/getlocation', [Registered_UsersController::class, 'location'])->name('locations.lookup');

Route::post('/delete-user/{id}', [Registered_UsersController::class, 'deleteUser'])->name('deleteuser');    
Route::post('/delete-usertype/{id}', [User_TypesController::class, 'deleteUsertype'])->name('deleteusertype');
Route::post('/update-usertype', [User_TypesController::class, 'updateUsertype'])->name('updateusertype');
Route::get('/get-usertype/{id}', [User_TypesController::class, 'getUsertype']); 
Route::post('/fetch-users-by-type', [Registered_UsersController::class, 'fetchUsersByType'])->name('fetchUsersByType');
Route::post('/fetch-users-by-name', [Registered_UsersController::class, 'fetchUsersByName'])->name('fetchUsersByName'); 

Route::post('/visitor', [VisitorController::class, 'index'])->name('visitor.index');    //checked
Route::post('/visitor/save', [VisitorController::class, 'save'])->name('visitor.save');  //checked
Route::post('/visitor/timeout', [VisitorController::class, 'timeoutAjax'])->name('visitor.timeout.ajax');
Route::get('/visitor/view/{id}', function ($id) {$visitor = Visitor::where('id', $id)
            ->latest('id')->firstOrFail();return view('homepage.view', compact('visitor'));})->name('visitor.view.page');
Route::post('/visitor/view', [VisitorController::class, 'view'])->name('visitor.view'); 

Route::post('/IDNumber', [RegisterIDController::class, 'index'])->name('registerID.index');  //checked
Route::post('/registeredID/save', [RegisterIDController::class, 'save'])->name('registerID.save');  //checked
Route::post('/registeredID/delete', [RegisterIDController::class, 'deleteAjax'])->name('registerID.delete.ajax'); //checked
Route::post('/registeredID/edit', [RegisterIDController::class, 'editAjax'])->name('registerID.edit.ajax'); //checked

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

Route::get('/home', [HomeController::class, 'index']);

Route::get('/dashboard', [DashboardController::class, 'index']);
});