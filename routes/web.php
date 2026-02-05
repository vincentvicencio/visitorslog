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
    return redirect()->route('visitorslog');
});

Auth::routes();

// middleware
Route::middleware(['auth'])->group(function () {
    Route::post('/addusers',        [Registered_UsersController::class, 'addusers']);
    Route::post('/update-user/{id}',[Registered_UsersController::class, 'updateUser']);

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



    Route::post('/usertype', [PageController::class, 'show_usertype'])->name('usertype.index');
    Route::get('/usertype', [PageController::class, 'show_usertype'])->name('usertype.index');

    Route::post('/users', [PageController::class, 'show_user'])->name('users.index');
    Route::get('/users', [PageController::class, 'show_user'])->name('users.index');

    Route::post('/report', [PageController::class, 'show_report'])->name('report.index');
    Route::get('/report', [PageController::class, 'show_report'])->name('report.index');

    // VISITOR TYPE
    Route::prefix('visitortype')
        ->controller(VisitorTypeController::class)
        ->group(function () {
            Route::get('/',             'index')->name('visitortype');
            Route::post('/list',        'list')->name('visitortype.list');
            Route::post('/save',        'save')->name('visitortype.save');
            Route::post('/delete',      'delete')->name('visitortype.delete');
            Route::post('/edit',      'edit')->name('visitortype.edit');
        });

    // REGISTER VISITOR ID
    Route::prefix('registerId')
        ->controller(RegisterIDController::class)
        ->group(function () {
            Route::get('/',             'index')->name('registerId');
            Route::post('/list',        'list')->name('registerId.list');
            Route::post('/save',        'save')->name('registerId.save');
            Route::post('/delete',      'delete')->name('registerId.delete');
            Route::post('/edit',      'edit')->name('registerId.edit');
        });

    // VISITORSLOG
    Route::prefix('visitorslog')
        ->controller(VisitorController::class)
        ->group(function () {
            Route::get('/',             'index')->name('visitorslog');
            Route::get('/form',    'form')->name('visitorslog.form');
            Route::post('/list',        'list')->name('visitorslog.list');
            Route::post('/save',        'save')->name('visitorslog.save');
            Route::post('/timeout',      'timeout')->name('visitorslog.timeout');
            Route::post('/view',      'view')->name('visitorslog.view');
            Route::get('/view/{id}/{type}', function ($id, $type) {
                $visitor = Visitor::where('id', $id)->latest('id')->firstOrFail();
                $visitorTypes = VisitorType::whereNull('deleted_at')->orderBy('id', 'asc')->get();
                return view('pages.visitorslog.view', compact('visitor', 'visitorTypes', 'type'));
            })->name('view.page');
        });

    // USER TYPE
    Route::prefix('userTypes')
        ->controller(User_TypesController::class)
        ->group(function () {
            Route::get('/',             'index')->name('userTypes');
            Route::post('/list',        'list')->name('userTypes.list');
            Route::post('/save',        'save')->name('userTypes.save');
            Route::post('/delete',      'delete')->name('userTypes.delete');
            Route::post('/edit',      'edit')->name('userTypes.edit');
        });

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


    Route::put('/update-user/{id}', [Registered_UsersController::class, 'updateUser']);
});