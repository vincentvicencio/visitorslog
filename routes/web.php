<?php

use App\Http\Controllers\AboutController;
use App\Http\Controllers\EmployeeController;
use App\Http\Controllers\RegisterIDController;
use App\Http\Controllers\GuardLocationController;
use App\Http\Controllers\VisitorTypeController;
use App\Models\ValidIdType;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\VisitorController;
use App\Models\Visitor;
use App\Models\VisitorType;
use App\Models\EmployeeLogs;
use App\Models\Location;
use App\Http\Controllers\ReportController;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\User_TypesController;
use App\Http\Controllers\Registered_UsersController;
use App\Http\Controllers\IDTypeController;

Route::get('/', function () {
    return redirect()->route('visitorslog');
});

Auth::routes();

// middleware
Route::middleware(['auth', 'single.session'])->group(function () {

    Route::get('/guard/location', [GuardLocationController::class, 'show'])->name('guard.location.show');
    Route::post('/guard/location', [GuardLocationController::class, 'store'])->name('guard.location.store');

    // VISITORSLOG
    Route::prefix('visitorslog')
        ->controller(VisitorController::class)
        ->group(function () {
            Route::get('/',                 'index')->name('visitorslog');
            Route::get('/form',             'form')->name('visitorslog.form');
            Route::get('/id-suggestions',  'idSuggestions')->name('visitorslog.idSuggestions');
            Route::get('/view/{id}/{type}', function ($id, $type) {
                $visitor = Visitor::where('id', $id)->latest('id')->firstOrFail();
                $visitorTypes = VisitorType::whereNull('deleted_at')->orderBy('id', 'asc')->get();
                $validIdTypes = ValidIdType::whereNull('deleted_at')->orderBy('id', 'asc')->get();
                return view('pages.visitorslog.view', compact('visitor', 'visitorTypes', 'type', 'validIdTypes'));
            })->name('view.page');
            Route::post('/list',            'list')->name('visitorslog.list');
            Route::post('/employee-list',            'list')->name('visitorslog.list');
            Route::post('/save',            'save')->name('visitorslog.save');
            Route::post('/timeout',         'timeout')->name('visitorslog.timeout');
            Route::post('/view',            'view')->name('visitorslog.view');
            Route::any('{any}', function () {
                return redirect()->route('visitorslog.form');
            })->where('any', '.*');
        });
    
        Route::prefix('employeeslog')
        ->controller(EmployeeController::class)
        ->group(function () {
            Route::get('/form',             'form')->name('employeeslog.form');
            Route::get('/search-employees', 'searchEmployees')->name('employeeslog.searchEmployees');
            Route::get('/view/{id}/{type}', function ($id, $type) {
                $visitor = EmployeeLogs::where('id', $id)->latest('id')->firstOrFail();

                $locationLabel = (string) ($visitor->location ?? '');
                if (is_numeric($locationLabel)) {
                    $allLocations = collect(session('all_location', []));
                    $match = $allLocations->firstWhere('id', $locationLabel);
                    if ($match && !empty($match['name'])) {
                        $locationLabel = (string) $match['name'];
                    } else {
                        $guardLocation = Location::query()->find($locationLabel);
                        if ($guardLocation) {
                            $locationLabel = (string) $guardLocation->name;
                        }
                    }
                }

                $statusLabel = (int) $visitor->status === 1 ? 'Timed Out' : 'Active';

                return view('pages.employeeslog.view', compact('visitor', 'type', 'locationLabel', 'statusLabel'));
            })->name('viewEmp.page');
            Route::post('/list',            'list')->name('employeeslog.list');
            Route::post('/save',            'save')->name('employeeslog.save');
            Route::post('/timeout',         'timeout')->name('employeeslog.timeout');
            Route::post('/view',            'view')->name('employeeslog.view');
        });
        
    Route::middleware(['auth', 'user_type:1'])->group(function () {
        // ADMIN ONLY ROUTES HERE
         // USER TYPE
        Route::prefix('userTypes')
            ->controller(User_TypesController::class)
            ->group(function () {
                Route::get('/',             'index')->name('userTypes');
                Route::post('/list',        'list')->name('userTypes.list');
                Route::post('/save',        'save')->name('userTypes.save');
                Route::post('/search',      'search')->name('userTypes.search');
                Route::post('/delete',      'delete')->name('userTypes.delete');
            });

        // USER
        Route::prefix('registerUser')
            ->controller(Registered_UsersController::class)
            ->group(function () {
                Route::get('/',                   'index')->name('registerUser');
                Route::get('/search-employees',   'searchEmployees')->name('registerUser.searchEmployees');
                Route::get('/get-user/{id}',      'getUser')->name('registerUser.getUser');
                Route::post('/save',              'save')->name('registerUser.save');
                Route::post('/search',            'search')->name('registerUser.search');
                Route::post('/delete',            'delete')->name('registerUser.delete');
                Route::put('/update-user/{id}',   'updateUser')->name('registerUser.updateUser');
                Route::post('/list',              'list')->name('registerUser.list');
                Route::post('/getlocation',       'location')->name('locations.lookup');
                Route::post('/get-user-type',     'getUserTypes')->name('getUserTypes');
            });

        // REPORT
        Route::prefix('reports')
            ->controller(ReportController::class)
            ->group(function () {
                Route::get('/',                 'index')->name('reports');
                Route::get('/export',           'exportReport')->name('reports.export');
                Route::post('/list',            'list')->name('reports.list');
                Route::post('/emp/list',        'empList')->name('reports.emp.list');
                Route::post('/delete',          'delete')->name('reports.delete');
            });
        // VISITOR TYPE
        Route::prefix('visitortype')
            ->controller(VisitorTypeController::class)
            ->group(function () {
                Route::get('/',             'index')->name('visitortype');
                Route::post('/list',        'list')->name('visitortype.list');
                Route::post('/save',        'save')->name('visitortype.save');
                Route::post('/search',      'search')->name('visitortype.search');
                Route::post('/delete',      'delete')->name('visitortype.delete');
            });

        // REGISTER VISITOR ID
        Route::prefix('registerId')
            ->controller(RegisterIDController::class)
            ->group(function () {
                Route::get('/',             'index')->name('registerId');
                Route::post('/list',        'list')->name('registerId.list');
                Route::post('/save',        'save')->name('registerId.save');
                Route::post('/delete',      'delete')->name('registerId.delete');
                Route::post('/search',      'search')->name('registerId.search');
            });

        // ID TYPE
        Route::prefix('IDtype')
            ->controller(IDTypeController::class)
            ->group(function () {
                Route::get('/',             'idTypeIndex')->name('idtype');
                Route::post('/list',        'idTypeList')->name('idtype.list');
                Route::post('/save',        'idTypeSave')->name('idtype.save');
                Route::post('/delete',      'idTypeDelete')->name('idtype.delete');
                Route::post('/search',      'idTypeSearch')->name('idtype.search');
            });

        // ABOUT
        Route::prefix('about')
            ->controller(AboutController::class)
            ->group(function () {
                Route::get('/',             'index')->name('about');
            });
    });
});