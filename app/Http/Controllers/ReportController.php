<?php

namespace App\Http\Controllers;

use App\Models\EmployeeLogs;
use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\VisitorType;
use App\Models\Location;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;
use App\Exports\ReportsExport;
use App\Exports\EmployeeExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        $visitorTypes = VisitorType::all();
        $companyLocations = collect(session('all_location', []))
            ->map(function ($item) {
                return [
                    'id' => (string) data_get($item, 'id'),
                    'name' => (string) data_get($item, 'name'),
                ];
            })
            ->filter(function ($item) {
                return $item['id'] !== '' && $item['name'] !== '';
            });

        $guardLocations = Location::query()
            ->get(['location_id', 'name'])
            ->map(function ($item) {
                return [
                    'id' => (string) $item->location_id,
                    'name' => (string) $item->name,
                ];
            });

        $locations = $companyLocations
            ->merge($guardLocations)
            ->unique('id')
            ->values();

        return view('pages.reports.report', compact('visitorTypes', 'locations'));
    }

    public function destroy($id)
    {
    try {
            $visitor = Visitor::findOrFail($id);

            $visitor->update([
                'deleted_at' => NOW(),
                'deleted_by' => Auth::user() -> id // Optional: track who deleted it
            ]);
        } catch (\Exception $e) {
        }
    }

    // In your UserController.php
    public function getUser($id) {
        $user = User::find($id);
        return response()->json([
            'id'          => $user -> id,
            'emp_code'    => $user -> emp_code,
            'role_id'     => $user -> user_type, // Ensure this matches your column name
            'location_id' => $user -> location_id 
        ]);
    }

    public function list(Request $request){
        // log filter operation with parameters
        $filterData = [
            'search'       => $request->input('search', ''),
            'date_from'    => $request->input('date_from', ''),
            'date_to'      => $request->input('date_to', ''),
            'location'     => $request->input('location', ''),
            'visitor_type' => $request->input('visitor_type', ''),
            'status'       => $request->input('status', ''),
        ];

        log_audit('reports', 'filtered', null, null, $filterData, 'filter');

        $keywords = strtolower($request->search);

        $limit    = $request->input('length');

        $baseQuery = Visitor::with('visitorType')
            ->withoutTrashed()
                ->when($keywords, function ($query) use ($keywords) {
                    $query -> where(function ($q) use ($keywords) {
                        $q -> where('full_name', 'LIKE', "%{$keywords}%")
                        -> orWhere('visitors_ids_number', 'LIKE', "%{$keywords}%")
                        -> orWhere('phone_number', 'LIKE', "%{$keywords}%")
                        -> orWhereHas('visitorType', function ($qt) use ($keywords) {
                            $qt -> where('name', 'LIKE', "%{$keywords}%");
                        });
                    });
                })
            // 2. NEW: Filter by Date From
            ->when($request->date_from, function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->date_from);
            })
            // 3. NEW: Filter by Date To
            ->when($request->date_to, function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->date_to);
            })
            // 4. NEW: Filter by Location
            ->when($request->location !== null && $request->location !== '', function ($query) use ($request) {
                $selectedLocation = (string) $request->location;
                $locationNames = Location::query()
                    ->where('location_id', $selectedLocation)
                    ->pluck('name')
                    ->map(function ($name) {
                        return (string) $name;
                    })
                    ->filter(function ($name) {
                        return $name !== '';
                    })
                    ->values()
                    ->all();

                $query->where(function ($locationQuery) use ($selectedLocation, $locationNames) {
                    $locationQuery->where('location', $selectedLocation);

                    if (!empty($locationNames)) {
                        $locationQuery->orWhereIn('location', $locationNames);
                    }
                });
            })
            // 4. NEW: Filter by Visitor Type Dropdown
            ->when($request->visitor_type, function ($query) use ($request) {
                $query->where('visitors_type_id', $request->visitor_type);
            })
            // 5. NEW: Filter by Status (Active/Timed Out)
            ->when($request->has('status') && $request->input('status') !== '', function ($query) use ($request) {
                   $statuses = array_map('intval', array_filter(explode(',', $request->status), function($v) { return $v !== ''; }));
                   if (!empty($statuses)) {
                       $query->whereIn('status', $statuses);
                   }
               });

        $rawquery = (clone $baseQuery);
        
        $totalRecords = (clone $rawquery)->count();
        
        if ($request->input('draw') > 1) { 
            $start         = $request->input('start'); 
            $column        = $request->input('order.0.column');
            $direction     = $request->input('order.0.dir');
            $order         = $request->input('columns')[$column]['data']; 
            $queryForPage  = (clone $rawquery)->orderBy("updated_at", "desc");
            $totalFiltered = (clone $rawquery)->count();
            $data          = $limit > 0
                ? $queryForPage->skip($start)->take($limit)->get()
                : $queryForPage->get();
       
        } else { 
       
            $queryForPage  = (clone $rawquery)->orderBy("updated_at", "desc");
            $data          = $limit > 0 ? $queryForPage->take($limit)->get() : $queryForPage->get();
     
            $totalFiltered = $totalRecords;
        }
 
        $newData = [];
        $i       = 0;
        $companyLocations = collect(session('all_location'))->keyBy(function ($item) {
            return (string) data_get($item, 'id');
        });
        $guardLocations = Location::query()->get(['id', 'name'])->keyBy(function ($item) {
            return (string) $item->id;
        });
      
        foreach ($data as $d) { 
            $locationLabel = '';
            $locationLabel = (string) $d->location;

            if (is_numeric($locationLabel)) {
                $companyLocation = $companyLocations->get($locationLabel);
                $guardLocation = $guardLocations->get($locationLabel);

                if ($companyLocation) {
                    $locationLabel = data_get($companyLocation, 'name', '');
                } elseif ($guardLocation) {
                    $locationLabel = $guardLocation->name;
                }
            }

            $status = '';

            if($d->status == 0){
                $status = 'Active';
            }else{
                $status = 'Timed Out';
            }

            $time_in    = Carbon::parse($d->time_in)->format('h:i A');

            $time_out   = $d->time_out ? Carbon::parse($d->time_out)->format('h:i A') : '-';

            $createdby = $d->created_by ? user_name($d->created_by) : '-';
            $updatedby = $d->updated_by ? user_name($d->updated_by) : '-';
                    
            if ($status === 'Timed Out') {
                $statuslayout = '<div class="status-cell"><div class="status text-danger border border-danger rounded-2"> '. $status .'</div></div>';
            }
            else{
                $statuslayout = '<div class="status-cell"><div class="status rounded-2" > '. $status .'</div></div>';
            }

            $newData[$i] = [

                'full_name' => $d->full_name,

                'location' => '<div class="text-center">' . $locationLabel . '</div>',

                'contact_number' => '<div class="text-center">' . $d->phone_number . '</div>',

                'visitor_type' =>  $d->visitorType?->name ?? '-',

                'visitor_id'   =>  $d->visitors_ids_number,

                'visit' =>  $d->created_at->format("F d, Y") .'<br>
                        '. $d->created_at->format('l'),

                'time_in' => '<div class="text-center">
                                <small> '. $time_in .'</small><br>
                            </div>',
                'time_out' => '<div class="text-center">
                                <small> '. $time_out .'</small><br>
                            </div>',

                'logged_by' => '<div class="text-center">
                                '. $createdby .'
                            </div>',

                'updated_by' => '<div class="text-center">
                                '. $updatedby .'
                            </div>',
            
                'status' => $statuslayout,

                'created_at'   => $d->created_at->format('F j, Y') . '<br>' . $d->created_at->format('l'),

                'updated_at'   => $d->updated_at->format('F j, Y') . '<br>' . $d->updated_at->format('l'),
                
            ];
            $i++;
        }
 
        return response()->json([
            'draw'              => intval($request->input('draw')),
            'recordsTotal'      => $totalRecords,
            'recordsFiltered'   => $totalFiltered,
            'data'              => $newData,
        ]);
    }

    public function empList(Request $request){
        // log filter operation with parameters
        $filterData = [
            'search'       => $request->input('search', ''),
            'date_from'    => $request->input('date_from', ''),
            'date_to'      => $request->input('date_to', ''),
            'location'     => $request->input('location', ''),
            'status'       => $request->input('status', ''),
        ];
        log_audit('reports', 'filtered', null, null, $filterData, 'filter');

        $keywords = strtolower($request->search);

        $limit    = $request->input('length');

        $selectedLocation = (string) $request->input('location', '');
        $locationNames = [];

        if ($selectedLocation !== '') {
            $companyLocation = collect(session('all_location', []))->first(function ($item) use ($selectedLocation) {
                return (string) data_get($item, 'id') === $selectedLocation;
            });

            $guardLocationNames = Location::query()
                ->where('location_id', $selectedLocation)
                ->pluck('name')
                ->map(function ($name) {
                    return (string) $name;
                })
                ->filter(function ($value) {
                    return $value !== '';
                })
                ->values()
                ->all();

            $companyLocationName = (string) data_get($companyLocation, 'name', '');
            $locationNames = array_values(array_filter(array_unique(array_merge(
                [$companyLocationName],
                $guardLocationNames
            )), function ($value) {
                return $value !== '';
            }));
        }

        $rawquery = EmployeeLogs::withoutTrashed()
                -> when($keywords, function ($query) use ($keywords) {
                    $query->where(function ($q) use ($keywords) {
                        $q->where   ('full_name', 'LIKE', "%{$keywords}%")
                          ->orWhere('emp_code', 'LIKE', "%{$keywords}%");
                    });
                })
            // 2. NEW: Filter by Date From
            ->when($request->date_from, function ($query) use ($request) {
                $query->whereDate('created_at', '>=', $request->date_from);
            })
            // 3. NEW: Filter by Date To
            ->when($request->date_to, function ($query) use ($request) {
                $query->whereDate('created_at', '<=', $request->date_to);
            })
            // 4. NEW: Filter by Location
            ->when($selectedLocation !== '', function ($query) use ($selectedLocation, $locationNames) {
                $query->where(function ($locationQuery) use ($selectedLocation, $locationNames) {
                    $locationQuery->where('location', $selectedLocation);

                    if (!empty($locationNames)) {
                        $locationQuery->orWhereIn('location', $locationNames);
                    }
                });
            })
            // 4. NEW: Filter by Status (In/Out)
            ->when($request->has('status') && $request->input('status') !== '', function ($query) use ($request) {
                $statuses = array_map('intval', array_filter(explode(',', $request->status), function ($v) {
                    return $v !== '';
                }));

                if (!empty($statuses)) {
                    $query->whereIn('status', $statuses);
                }
            });
        
        $totalRecords = $rawquery->get()->count();
        
        if ($request->input('draw') > 1) { 
            $start         = $request->input('start'); 
            $column        = $request->input('order.0.column');
            $direction     = $request->input('order.0.dir');
            $order         = $request->input('columns')[$column]['data']; 
            $temp          = $rawquery->get(); 
            $rawQuery      = $limit > 0 ? $rawquery->skip($start)->take($limit) : $rawquery; 
            $data          = $rawquery->orderby("updated_at", "desc")->take($limit)->get(); 
            $totalFiltered = count($temp);
       
        } else { 
       
            $data          = $rawquery->orderby("updated_at", "desc")->take($limit)->get();
     
            $totalFiltered = $totalRecords;
        }
 
        $newData = [];
        $i       = 0;
        $companyLocations = collect(session('all_location'))->keyBy(function ($item) {
            return (string) data_get($item, 'id');
        });
        $guardLocations = Location::query()->get(['id', 'name'])->keyBy(function ($item) {
            return (string) $item->id;
        });
      
        foreach ($data as $d) { 
            $locationLabel = '';
            $locationLabel = (string) $d->location;

            if (is_numeric($locationLabel)) {
                $companyLocation = $companyLocations->get($locationLabel);
                $guardLocation = $guardLocations->get($locationLabel);

                if ($companyLocation) {
                    $locationLabel = data_get($companyLocation, 'name', '');
                } elseif ($guardLocation) {
                    $locationLabel = $guardLocation->name;
                }
            }

            $status = '';

            if($d->status == 0){
                $status = 'In';
            }else{
                $status = 'Out';
            }

            $time    = Carbon::parse($d->time)->format('h:i A');

            $createdby = $d->created_by ? user_name($d->created_by) : '-';
                    
            if ($status === 'Out') {
                $statuslayout = '<div class="status-cell"><div class="status text-danger border border-danger rounded-2"> '. $status .'</div></div>';
            }
            else{
                $statuslayout = '<div class="status-cell"><div class="status rounded-2" > '. $status .'</div></div>';
            }

            $newData[$i] = [
                
                'emp_code' => $d->emp_code,

                'full_name' => $d->full_name,

                'location' => '<div class="text-center">' . $locationLabel . '</div>',

                'log_date' =>  '<div class="text-center">' . 
                                    $d->created_at->format("F d, Y") .'<br>
                                    '. $d->created_at->format('l')
                                 . '</div>',

                'time' => '<div class="text-center">
                                <small> '. $time .'</small><br>
                            </div>',
                'activity' => '<div class="text-center">
                                '. $d->activity .'<br>
                            </div>',

                'status'   => $statuslayout,

                'creator' => '<div class="text-center">
                                '. $createdby .'
                            </div>',
            ];
            $i++;
        }
 
        return response()->json([
            'draw'              => intval($request->input('draw')),
            'recordsTotal'      => $totalRecords,
            'recordsFiltered'   => $totalFiltered,
            'data'              => $newData

        ]);
    }

    public function exportReport(Request $request)
    {
        try{
            // Single export endpoint: switch between visitor and employee logs by selected tab.
            $type = strtolower((string) $request->input('type', 'visitor'));

            $visitorFilters = [
                'search'            => $request->input('search', ''),
                'date_from'         => $request->input('date_from', ''),
                'date_to'           => $request->input('date_to', ''),
                'location'          => $request->input('location', ''),
                'visitor_type'      => $request->input('visitor_type', ''),
                'status'            => $request->input('status', ''),
            ];

            if ($type === 'employee') {
                $employeeFilters = [
                    'search'    => $request->input('search', ''),
                    'date_from' => $request->input('date_from', ''),
                    'date_to'   => $request->input('date_to', ''),
                    'location'  => $request->input('location', ''),
                    'status'    => $request->input('status', ''),
                ];

                log_audit('employee_logs', 'exported', null, null, $employeeFilters, 'export');
                $fileName = 'Employee_Logs_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

                return Excel::download(new EmployeeExport($employeeFilters), $fileName);
            }

            log_audit('reports', 'exported', null, null, $visitorFilters, 'export');
            $fileName = 'Visitor_Report_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

            return Excel::download(new ReportsExport($visitorFilters), $fileName);
    
        }catch(\Exception $e){
            return response()->json([
                'status' => 1,
                'title' => 'Error',
                'message' => $e->getMessage()
            ]); 
        }
        
    }

    public function exportEmpLogs(Request $request)
    {
        try{
            // Employee logs export (uses EmployeeExport)
            $filters = [
                'search'    => $request->input('search', ''),
                'date_from' => $request->input('date_from', ''),
                'date_to'   => $request->input('date_to', ''),
                'location'  => $request->input('location', ''),
                'status'    => $request->input('status', ''),
            ];

            // log export action
            log_audit('employee_logs', 'exported', null, null, $filters, 'export');

            $fileName = 'Employee_Logs_' . now()->format('Y-m-d_H-i-s') . '.xlsx';

            return Excel::download(new EmployeeExport($filters), $fileName);

        }catch(\Exception $e){
            return response()->json([
                'status' => 1,
                'title' => 'Error',
                'message' => $e->getMessage()
            ]);
        }
    }

    public function delete(Request $request){
        $record  = Visitor::find($request->id);
        if ($record) {
            $oldData = $record->toArray();
            $details = $record->name;
            $record->update(['deleted_by' => Auth::user()->id]);
            $record->delete();

            log_audit('reports', 'deleted', $record->id, $oldData, null, 'delete');
        }

        $message    = 'Report Log Successfully Deleted';
            return response()->json([
            'status' => 0,
            'title' => 'Success',
            'message' => $message
        ]);
    }
}