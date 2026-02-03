<?php

namespace App\Http\Controllers\TryController;


use App\Http\Controllers\RegisterIDController;
use App\Http\Controllers\VisitorTypeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VisitorController;
use App\Models\Visitor;
use App\Models\VisitorType;
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

// home route
// Route::get('/home', [App\Http\Controllers\HomeController::class, 'index'])->name('home');

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
// URL registered user
        // Route::group(['prefix' => 'registered_user'], function () {
        //     Route::get('/', [RegisteredUserController::class, 'index'])->name('registered_users');
        //     Route::post('/search_empno', [RegisteredUserController::class,'search_emp_code'])->name('registered_user.search_empno');
        //     Route::post('/search',  [RegisteredUserController::class, 'search'])->name('registered_user.search');
        //     Route::post('/list', [RegisteredUserController::class, 'list'])->name('registered_user.list');
        //     Route::post('/add_user', [RegisteredUserController::class, 'add_user'])->name('registered_user.add_user');
        //     Route::get('/clear_form', [RegisteredUserController::class, 'clear_form'])->name('registered_user.clear_form');
        //     Route::post('/list/delete', [RegisteredUserController::class, 'delete_user'])->name('registered_user.delete_user');
        //     Route::post('/get_user_types', [RegisteredUserController::class, 'getUserTypes'])->name('registered_user.get_types');
        // });

Route::get('/visitor', [VisitorController::class, 'index'])->name('visitor.index');    //checked
Route::post('/visitor', [VisitorController::class, 'index'])->name('visitor.index');    //checked
Route::post('/visitor/save', [VisitorController::class, 'saveAjax'])->name('visitor.save');  //checked
Route::post('/visitor/timeout', [VisitorController::class, 'timeoutAjax'])->name('visitor.timeout.ajax');
// Route::get('/visitor/view/{id}', function ($id) {$visitor = Visitor::where('id', $id)
//             ->latest('id')->firstOrFail();return view('homepage.view', compact('visitor'));})->name('visitor.view.page');
Route::get('/visitor/view/{id}/{type}', function ($id, $type) {

    $visitor = Visitor::where('id', $id)
        ->latest('id')
        ->firstOrFail();

    $visitorTypes = VisitorType::whereNull('deleted_at')
        ->orderBy('id', 'asc')
        ->get();

    return view('homepage.view', compact('visitor', 'visitorTypes', 'type'));

})->name('visitor.view.page');

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
Route::post('/visitortype/list', [VisitorTypeController::class, 'list'])->name('visitortype.list');


Route::group(['prefix' => 'visitortype'], function () {
    Route::post('/list', [VisitorTypeController::class, 'list'])->name('list');
});
Route::group(['prefix' => 'registerId'], function () {
    Route::post('/list', [RegisterIDController::class, 'list'])->name('list');
});
Route::group(['prefix' => 'visitorslog'], function () {
    Route::post('/list', [VisitorController::class, 'list'])->name('list');
});
Route::group(['prefix' => 'userTypes'], function () {
    Route::post('/list', [User_TypesController::class, 'list'])->name('list');
});

Route::group(['prefix' => 'userstable'], function () {
    Route::post('/list', [Registered_UsersController::class, 'list'])->name('list');
});

Route::group(['prefix' => 'reporttable'], function () {
    Route::post('/list', [TryController::class, 'list'])->name('list');
});

Route::post('/id', [TryController::class, 'show_id'])->name('id.index');
Route::get('/id', [TryController::class, 'show_id'])->name('id.index');

Route::post('/report', [TryController::class, 'show_report'])->name('report.index');
Route::get('/report', [TryController::class, 'show_report'])->name('report.index');

Route::get('/home', [HomeController::class, 'index']);
// Change 'post' to 'put'
Route::put('/update-user/{id}', [Registered_UsersController::class, 'updateUser']);
});