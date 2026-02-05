<?php

namespace App\Http\Controllers\PageController;


use App\Http\Controllers\RegisterIDController;
use App\Http\Controllers\VisitorTypeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VisitorController;
use App\Models\Visitor;
use App\Models\VisitorType;
use App\Http\Controllers\PageController;
// use App\Http\Controllers\TryController;
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

// middleware
Route::middleware(['auth'])->group(function () {

    Route::get('/visitorlog', [PageController::class, 'show'])->name('visitorlog.index');
    Route::get('/visitorlog-admin', [HomeController::class, 'index'])->name('visitorlog');

    Route::post('/addusers', [Registered_UsersController::class, 'addusers']);
    Route::post('/update-user/{id}', [Registered_UsersController::class, 'updateUser']);

    Route::post('/addusertype', [User_TypesController::class, 'addusertype'])->name('addusertype');
    Route::post('/addusers', [Registered_UsersController::class, 'addusers'])->name('addusers');

    Route::get('/get-user/{id}', [Registered_UsersController::class, 'getUser']);

    Route::get('/usertype/{id}/edit', [User_TypesController::class, 'edit']);
    Route::put('/usertype/{id}', [User_TypesController::class, 'update']);
    Route::delete('/usertype/{id}', [User_TypesController::class, 'destroy']);

    Route::get('/usertype', [PageController::class, 'show_usertype'])->name('usertype');

    Route::post('/add-user-type', [User_TypesController::class, 'addusertype'])->name('addusertype');

    Route::delete('/delete-visitor/{id}', [PageController::class, 'destroy'])->name('visitors.destroy');
    Route::delete('/visitors/{id}', [PageController::class, 'destroy'])->name('visitors.destroy');

    Route::post('/getlocation', [Registered_UsersController::class, 'location'])->name('locations.lookup');
    Route::get('/getlocation', [Registered_UsersController::class, 'location'])->name('locations.lookup');

    Route::post('/delete-user/{id}', [Registered_UsersController::class, 'deleteUser'])->name('deleteuser');    
    Route::post('/delete-usertype/{id}', [User_TypesController::class, 'deleteUsertype'])->name('deleteusertype');
    Route::post('/update-usertype', [User_TypesController::class, 'updateUsertype'])->name('updateusertype');
    Route::get('/get-usertype/{id}', [User_TypesController::class, 'getUsertype']); 
    Route::post('/fetch-users-by-type', [Registered_UsersController::class, 'fetchUsersByType'])->name('fetchUsersByType');
    Route::post('/fetch-users-by-name', [Registered_UsersController::class, 'fetchUsersByName'])->name('fetchUsersByName'); 

    Route::get('/visitor', [VisitorController::class, 'index'])->name('visitor.index');    //checked
    Route::post('/visitor', [VisitorController::class, 'index'])->name('visitor.index');    //checked

    Route::post('/IDNumber', [RegisterIDController::class, 'index'])->name('registerID.index');  //checked

    Route::post('/visitor_type', [VisitorTypeController::class, 'index'])->name('visitorType.index');   //checked

    Route::post('/visitorlog', [PageController::class, 'show'])->name('visitorlog.index');
    Route::get('/visitorlog', [PageController::class, 'show'])->name('visitorlog.index');


    Route::post('/usertype', [PageController::class, 'show_usertype'])->name('usertype.index');
    Route::get('/usertype', [PageController::class, 'show_usertype'])->name('usertype.index');

    Route::post('/users', [PageController::class, 'show_user'])->name('users.index');
    Route::get('/users', [PageController::class, 'show_user'])->name('users.index');


    Route::post('/visitortype', [PageController::class, 'show_visitortype'])->name('visitortype.index');
    Route::get('/visitortype', [PageController::class, 'show_visitortype'])->name('visitortype.index');

    Route::post('/id', [PageController::class, 'show_id'])->name('id.index');
    Route::get('/id', [PageController::class, 'show_id'])->name('id.index');

    Route::post('/report', [PageController::class, 'show_report'])->name('report.index');
    Route::get('/report', [PageController::class, 'show_report'])->name('report.index');

    // VISITOR TYPE
    Route::group(['prefix' => 'visitortype'], function () {
        Route::post('/list', [VisitorTypeController::class, 'list'])->name('list');
        Route::post('/save', [VisitorTypeController::class, 'save'])->name('save'); 
        Route::post('/delete', [VisitorTypeController::class, 'deleteAjax'])->name('delete'); 
        Route::post('/edit', [VisitorTypeController::class, 'editAjax'])->name('edit'); 
        // Route::post('/search', [VisitorTypeController::class, 'search'])->name('search'); 
    });

    // REGISTER VISITOR ID
    Route::group(['prefix' => 'registerId'], function () {
        Route::post('/list', [RegisterIDController::class, 'list'])->name('list');
        Route::post('/save', [RegisterIDController::class, 'save'])->name('save'); 
        Route::post('/delete', [RegisterIDController::class, 'deleteAjax'])->name('delete'); 
        Route::post('/edit', [RegisterIDController::class, 'editAjax'])->name('edit'); 
    });

    // VISITORSLOG
    Route::group(['prefix' => 'visitorslog'], function () {
        Route::post('/list', [VisitorController::class, 'list'])->name('list');
        Route::post('/save', [VisitorController::class, 'saveAjax'])->name('save');
        Route::post('/timeout', [VisitorController::class, 'timeoutAjax'])->name('timeout');
        Route::get('/view/{id}/{type}', function ($id, $type) {
            $visitor = Visitor::where('id', $id)->latest('id')->firstOrFail();
            $visitorTypes = VisitorType::whereNull('deleted_at')->orderBy('id', 'asc')->get();
            return view('homepage.view', compact('visitor', 'visitorTypes', 'type'));
        })->name('view.page');
        Route::post('/view', [VisitorController::class, 'view'])->name('view'); 
    });

    // USER TYPE
    Route::group(['prefix' => 'userTypes'], function () {
        Route::post('/list', [User_TypesController::class, 'list'])->name('list');
    });

    // USER
    Route::group(['prefix' => 'userstable'], function () {
        Route::post('/list', [Registered_UsersController::class, 'list'])->name('list');
    });

    // REPORT
    Route::group(['prefix' => 'reporttable'], function () {
        Route::post('/list', [PageController::class, 'list'])->name('list');
    });

    Route::get('/home', [HomeController::class, 'index']);
    // Change 'post' to 'put'
    Route::put('/update-user/{id}', [Registered_UsersController::class, 'updateUser']);
});