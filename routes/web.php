<?php

use App\Http\Controllers\RegisterIDController;
use App\Http\Controllers\VisitorTypeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VisitorController;
use App\Http\Controllers\TryController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\User_TypesController;
use App\Http\Controllers\Registered_UsersController;
use App\Http\Controllers\HomeController;
use App\Models\RegisteredUser;
use Illuminate\Support\Facades\Session;

Route::get('/', function () {
    return view('welcome');
});

Auth::routes();

// home route
Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

Route::post('/addusertype', [User_TypesController::class, 'addusertype'])->name('addusertype');
Route::post('/addusers', [Registered_UsersController::class, 'addusers'])->name('addusers');

Route::get('/get-user/{id}', [Registered_UsersController::class, 'getUser']);
Route::post('/update-user/{id}', [Registered_UsersController::class, 'updateUser'])->name('updateuser');


// View route
Route::get('/usertype', [TryController::class, 'show_usertype'])->name('usertype');

// Action route
Route::post('/add-user-type', [User_TypesController::class, 'addusertype'])->name('addusertype');


Route::post('/delete-user/{id}', [Registered_UsersController::class, 'deleteUser'])->name('deleteuser');    
Route::post('/delete-usertype/{id}', [User_TypesController::class, 'deleteUsertype'])->name('deleteusertype');
Route::post('/update-usertype', [User_TypesController::class, 'updateUsertype'])->name('updateusertype');
Route::get('/get-usertype/{id}', [User_TypesController::class, 'getUsertype']); 
Route::post('/fetch-users-by-type', [Registered_UsersController::class, 'fetchUsersByType'])->name('fetchUsersByType');
Route::post('/fetch-users-by-name', [Registered_UsersController::class, 'fetchUsersByName'])->name('fetchUsersByName'); 

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
