<?php

use App\Http\Controllers\RegisterIDController;
use App\Http\Controllers\VisitorTypeController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VisitorController;
use App\Models\Visitor;
use App\Models\VisitorType;
use App\Http\Controllers\ReportController;
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

    // VISITORSLOG
    Route::prefix('visitorslog')
        ->controller(VisitorController::class)
        ->group(function () {
            Route::get('/',                 'index')->name('visitorslog');
            Route::get('/form',             'form')->name('visitorslog.form');
            Route::get('/view/{id}/{type}', function ($id, $type) {
                $visitor = Visitor::where('id', $id)->latest('id')->firstOrFail();
                $visitorTypes = VisitorType::whereNull('deleted_at')->orderBy('id', 'asc')->get();
                return view('pages.visitorslog.view', compact('visitor', 'visitorTypes', 'type'));
            })->name('view.page');
            Route::post('/list',            'list')->name('visitorslog.list');
            Route::post('/save',            'save')->name('visitorslog.save');
            Route::post('/timeout',         'timeout')->name('visitorslog.timeout');
            Route::post('/view',            'view')->name('visitorslog.view');
        });
        
    // Route::middleware('user_type:1')->group( function(){
         // USER TYPE
        Route::prefix('userTypes')
            ->controller(User_TypesController::class)
            ->group(function () {
                Route::get('/',                   'index')->name('userTypes');
                Route::get('/usertype/{id}/edit', 'edit')->name('userTypes.edit');
                Route::put('/usertype/{id}',      'update')->name('userTypes.update');
                Route::post('/list',              'list')->name('userTypes.list');
                Route::post('/delete',            'delete')->name('userTypes.delete');
                Route::post('/addusertype',       'addusertype')->name('userTypes.addusertype');
                Route::delete('/usertype/{id}',   'destroy')->name('userTypes.destroy');
            });

        // USER
        Route::prefix('registerUser')
            ->controller(Registered_UsersController::class)
            ->group(function () {
                Route::get('/',                   'index')->name('registerUser');
                Route::get('/search-employees',   'searchEmployees')->name('registerUser.searchEmployees');
                Route::get('/get-user/{id}',      'getUser')->name('registerUser.getUser');
                Route::put('/update-user/{id}',  'updateUser')->name('registerUser.updateUser');
                Route::post('/list',              'list')->name('registerUser.list');
                Route::post('/addusers',          'addusers')->name('registerUser.addusers');
                Route::post('/delete-user/{id}',  'deleteUser')->name('registerUser.deleteuser');  
                Route::post('/getlocation',       'location')->name('locations.lookup');
                // Route::post('/get-user-type',     'getUserTypes')->name('getUserTypes');
            });

        // REPORT
        Route::prefix('reports')
            ->controller(ReportController::class)
            ->group(function () {
                Route::get('/',                   'index')->name('reports');
                Route::get('/export',             'exportReport')->name('reports.export');
                Route::post('/list',              'list')->name('reports.list');
                Route::delete('/delete-visitor/{id}', 'destroy')->name('reports.destroy');
            });
        // VISITOR TYPE
        Route::prefix('visitortype')
            ->controller(VisitorTypeController::class)
            ->group(function () {
                Route::get('/',             'index')->name('visitortype');
                Route::post('/list',        'list')->name('visitortype.list');
                Route::post('/save',        'save')->name('visitortype.save');
                Route::post('/delete',      'delete')->name('visitortype.delete');
                Route::post('/edit',        'edit')->name('visitortype.edit');
            });

        // REGISTER VISITOR ID
        Route::prefix('registerId')
            ->controller(RegisterIDController::class)
            ->group(function () {
                Route::get('/',             'index')->name('registerId');
                Route::post('/list',        'list')->name('registerId.list');
                Route::post('/save',        'save')->name('registerId.save');
                Route::post('/delete',      'delete')->name('registerId.delete');
                Route::post('/edit',        'edit')->name('registerId.edit');
            });
    // });
});