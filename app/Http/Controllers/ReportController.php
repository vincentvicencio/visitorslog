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

        return view('pages.reports.report', compact('visitorTypes'));
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
            'visitor_type' => $request->input('visitor_type', ''),
        ];
        log_audit('reports', 'filtered', null, null, $filterData, 'filter');

        $keywords = strtolower($request->search);

        $limit    = $request->input('length');

        $rawquery = Visitor::with('visitorType')
                ->withoutTrashed()
                ->where('status', 1)
                ->when($keywords, function ($query) use ($keywords) {
                    $query -> where(function ($q) use ($keywords) {
                        $q -> where('full_name', 'LIKE', "%{$keywords}%")
                        -> orWhere('visitor_id', 'LIKE', "%{$keywords}%")
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
            // 4. NEW: Filter by Visitor Type Dropdown
            ->when($request->visitor_type, function ($query) use ($request) {
                $query->where('visitor_type', $request->visitor_type);
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

            $image = '';

            if ($d->image_path == null) {
                $image = 'No Image Provided';
            }else{
                $image ='<button 
                    class="btn-sm view-button text-white border-0 rounded-2 px-3 py-1"
                        id="viewImageBtn"
                        data-id="'. $d->id .'"
                        data-image="'. Storage::url($d->image_path) .'">
                        View
                    </button>';
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
                $statuslayout = '<div class="status-cell"><div class="status text-danger border border-danger"> '. $status .'</div></div>';
            }
            else{
                $statuslayout = '<div class="status-cell"><div class="status" > '. $status .'</div></div>';
            }

            $newData[$i] = [
                

                'full_name' => $d->full_name,

                'location' => '<div class="text-center">' . $locationLabel . '</div>',

                'contact_number' => '<div class="text-center">' . $d->phone_number . '</div>',

                'visitor_type' =>  $d->visitorType?->name ?? '-',

                'visitor_id'   =>  $d->visitor_id,

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

                'action' => '<div class="dropdown text-center">
                                    <button 
                                        class="dropdown-item"
                                        id="viewBtn"
                                        data-id="'. $d->id .'"
                                        data-type="reports">
                                        View
                                    </button>
                                    <button class="text-danger dropdown-item btn-delete" data-id="'. $d->id .'" data-details="'. $d->full_name. '">Delete</button>
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

    public function empList(Request $request){
        // log filter operation with parameters
        $filterData = [
            'search'       => $request->input('search', ''),
            'date_from'    => $request->input('date_from', ''),
            'date_to'      => $request->input('date_to', ''),
        ];
        log_audit('reports', 'filtered', null, null, $filterData, 'filter');

        $keywords = strtolower($request->search);

        $limit    = $request->input('length');

        $rawquery = EmployeeLogs::withoutTrashed()
                ->where('status', 1)
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
                $status = 'Active';
            }else{
                $status = 'Timed Out';
            }

            $time_in    = Carbon::parse($d->time_in)->format('h:i A');

            $time_out   = $d->time_out ? Carbon::parse($d->time_out)->format('h:i A') : '-';

            $createdby = $d->created_by ? user_name($d->created_by) : '-';
            $updatedby = $d->updated_by ? user_name($d->updated_by) : '-';
                    
            if ($status === 'Timed Out') {
                $statuslayout = '<div class="status-cell"><div class="status text-danger border border-danger"> '. $status .'</div></div>';
            }
            else{
                $statuslayout = '<div class="status-cell"><div class="status" > '. $status .'</div></div>';
            }

            $newData[$i] = [
                
                'emp_code' => $d->emp_code,

                'full_name' => $d->full_name,

                'location' => '<div class="text-center">' . $locationLabel . '</div>','log_date' =>  '<div class="text-center">' . 
                                    $d->created_at->format("F d, Y") .'<br>
                                    '. $d->created_at->format('l')
                                 . '</div>',

                'time_in' => '<div class="text-center">
                                <small> '. $time_in .'</small><br>
                            </div>',
                'time_out' => '<div class="text-center">
                                <small> '. $time_out .'</small><br>
                            </div>',

                'creator' => '<div class="text-center">
                                '. $createdby .'
                            </div>',

                'updated_by' => '<div class="text-center">
                                '. $updatedby .'
                            </div>',
            
                'status' => $statuslayout,

                'action' => '<div class="dropdown text-center">
                                    <button 
                                        class="dropdown-item"
                                        id="viewBtn"
                                        data-id="'. $d->id .'"
                                        data-type="reports">
                                        View
                                    </button>
                                    <button class="text-danger dropdown-item btn-delete" data-id="'. $d->id .'" data-details="'. $d->full_name. '">Delete</button>
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
                'visitor_type'      => $request->input('visitor_type', ''),
                'status'            => $request->input('status', ''),
            ];

            if ($type === 'employee') {
                $employeeFilters = [
                    'search'    => $request->input('search', ''),
                    'date_from' => $request->input('date_from', ''),
                    'date_to'   => $request->input('date_to', ''),
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