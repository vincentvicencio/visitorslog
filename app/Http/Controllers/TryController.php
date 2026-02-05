<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\VisitorType;
use App\Models\UserType;
use App\Models\RegisteredUser;
use App\Models\RegisteredID;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use App\Models\User;

class TryController extends Controller
{
    public function show()
    {
        $visitors = Visitor::where('status', 0)
                   ->whereNull('time_out')
                   ->orderBy('id', 'desc')
                   ->get();
        $visitorTypes = VisitorType::where('deleted_at', null)
                   ->orderBy('id', 'desc')
                   ->get();
        $empMap = collect(session('all_emp'))->keyBy('emp_code');
        return view('pages.visitorlog', compact('visitors', 'visitorTypes', 'empMap'));
    }
public function show_usertype()
{
    $roles = \App\Models\User_types::all(); 
    $visitorTypes = VisitorType::all();
    $empMap = collect(session('all_emp'))->keyBy('emp_code');
    
    return view('pages.usertype', compact('roles', 'visitorTypes','empMap'));
}
    public function show_user(Request $request)
    {
        $registeredUsers = RegisteredUser::all();
        $roles = \App\Models\user_types::all();
        $visitorTypes = VisitorType::all();

        // $visitorlogs = Visitor::with('visitor_type')->get();
        $visitorlogs = Visitor::where('status', 0)
                   ->orderBy('id', 'asc')
                   ->get();
        $search = $request->input('search');

        $empMap = collect(session('all_emp'))->keyBy('emp_code');
        
        $registeredUsers = RegisteredUser::with('userType')
        ->whereNull('deleted_at')
        ->when($search, function ($query, $search) {
            $query->where('user_name', 'like', "%{$search}%")
                ->orWhere('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%");
        })
        ->get();

    $allEmployeesFromSession = session('all_emp', []);
    return view('pages.users', compact('roles', 'registeredUsers', 'allEmployeesFromSession', 'visitorlogs', 'visitorTypes','empMap'));
    }

    public function show_visitortype()
    {
        // Get all registered IDs, latest first
        $visitorTypes = VisitorType::where('deleted_at', null)
        ->orderBy('id', 'desc')
        ->get();
        // Pass to the view
        return view('pages.visitortype', compact('visitorTypes'));
    }
    public function show_id()
    {
        $registeredIds = RegisteredID::where('deleted_at', null)
                   ->orderBy('id', 'desc')
                   ->get();
        $visitorTypes = VisitorType::where('deleted_at', null)
                   ->orderBy('id', 'desc')
                   ->get();
        $visitorsLogs = Visitor::where('status', 0)
                   ->whereNull('time_out')
                   ->orderBy('id', 'desc')
                   ->get();
        return view('pages.id', compact('registeredIds', 'visitorTypes', 'visitorsLogs'));
    }
    public function show_report(Request $request)
{
    // $query = Visitor::with('visitor_type');

    // // Filter by Date Range
    // if ($request->filled('date_from')) {
    //     $query->whereDate('created_at', '>=', $request->date_from);
    // }
    // if ($request->filled('date_to')) {
    //     $query->whereDate('created_at', '<=', $request->date_to);
    // }

    // // Filter by Visitor Type
    // if ($request->filled('visitor_type')) {
    //     $query->where('visitor_type', $request->visitor_type);
    // }

    // // Existing Search Logic
    // if ($request->filled('searchreport')) {
    //     $search = $request->searchreport;
    //     $query->where(function($q) use ($search) {
    //         $q->where('first_name', 'like', "%$search%")
    //           ->orWhere('last_name', 'like', "%$search%");
    //     });
    // }$query->

    
    // $visitorlogs = orderBy('created_at', 'desc')->get();
    $visitorlogs = Visitor::where('status', 0)
                   ->orderBy('id', 'asc')
                   ->get();
    $visitorTypes = VisitorType::all();
    $allEmployeesFromSession = session('all_emp', []);


    

    return view('pages.report', compact('visitorlogs', 'visitorTypes', 'allEmployeesFromSession'));
}


public function destroy($id)

{
try {
        $visitor = Visitor::findOrFail($id);

        // Instead of $user->delete(), we update the column
        $visitor->update([
            'deleted_at' => NOW(),
            'deleted_by' => Auth::user()->first_name ?? 'System' // Optional: track who deleted it
        ]);

    } catch (\Exception $e) {
    }

}


// In your UserController.php
public function getUser($id) {
    $user = User::find($id);
    return response()->json([
        'id'        => $user->id,
        'emp_code'  => $user->emp_code,
        'role_id'   => $user->user_type, // Ensure this matches your column name
        'location_id' => $user->location_id 
    ]);
}

public function list(Request $request){

        $keywords = strtolower($request->search);
        // $keywords = strtolower($request->input('search.value'));

        $limit    = $request->input('length');

<<<<<<< HEAD
=======
        // Debug: Log what we're receiving
        \Log::info('Filter Request:', [
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'visitor_type' => $request->visitor_type,
            'search' => $keywords
        ]);

>>>>>>> 2a7df276c60bbda649ef6810462b1244ad5a887d
        $rawquery = Visitor::with('visitorType')
                ->withoutTrashed()
                ->when($keywords, function ($query) use ($keywords) {
                    $query->where(function ($q) use ($keywords) {
                        $q->where('full_name', 'LIKE', "%{$keywords}%")
                        ->orWhere('visitor_id', 'LIKE', "%{$keywords}%")
                        ->orWhere('phone_number', 'LIKE', "%{$keywords}%")
                        ->orWhereHas('visitorType', function ($qt) use ($keywords) {
                            $qt->where('name', 'LIKE', "%{$keywords}%");
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
                $query->where('visitor_type_id', $request->visitor_type);
            });

        // Log the count after filters
        $totalRecords = $rawquery->count();
        \Log::info('Total Filtered Records:', ['count' => $totalRecords]);
        
        if ($request->input('draw') > 1) { 
            $start         = $request->input('start'); 
            $column        = $request->input('order.0.column');
            $direction     = $request->input('order.0.dir');
            $order         = $request->input('columns')[$column]['data']; 
            
            // Map virtual columns to actual database columns
            $columnMap = [
                'personal_detail' => 'first_name',
                'visitor_type' => 'visitor_type_id',
                'visitor_id' => 'visitor_id',
                'image' => 'image_path',
                'visit' => 'created_at',
                'time' => 'created_at',
                'creator' => 'created_by',
                'status' => 'status',
                'action' => 'id'
            ];
            
            $order = $columnMap[$order] ?? 'id';
            
            // Apply ordering and pagination to the filtered query
            $data = $rawquery->orderBy('id', 'desc')
                             ->skip($start)
                             ->take($limit)
                             ->get();
            
            $totalFiltered = $totalRecords;
       
        } else { 
            // First load - no sorting from DataTable yet
            $data = $rawquery->orderBy("id", "desc")
                             ->take($limit)
                             ->get();
     
            $totalFiltered = $totalRecords;
        }
 
        $newData = [];
        $i       = 0;
 
        // foreach ($data as $d) { 
 
        //     $newData[$i] = [
        //         'id'          => $d->id,
        //         'name'        => $d->name,
        //         'description' => $d->description,
        //         // 'updated_by'  => user_name($d->updated_by),
        //         // 'updated_date'=> date('Y-m-d H:i:s', strtotime($d->updated_at)),
        //         // 'action'      => create_action($d->id, $d->name, 'Edit')
        //     ];
        //     $i++;
        // }
      
        foreach ($data as $d) { 
            $locationLabel = '';

            $location = collect(session('all_location'));
            foreach ($location as $record) {
                if($d->location == $record['id']){
                    $locationLabel = $record['name'];
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

            $time_in = Carbon::parse($d->time_in)->format('h:i A');

            $time_out = $d->time_out ? Carbon::parse($d->time_out)->format('h:i A') : '-';
                    
            $newData[$i] = [
                'full_name' => '
                    <strong>' . $d->full_name . '</strong>
                    <br><small>' . $locationLabel . '</small>
                    <br><small>' . $d->phone_number . '</small>
                ',

                'visitor_type' => $d->visitorType->name,

                'visitor_id' => $d->visitor_id,

                'image' => $image,

                'visit' =>  $d->created_at->format("F d, Y") .'<br>
                        '. $d->created_at->format('l'),

                'time' => '<small><strong>In:</strong> '. $time_in .'</small><br>
                            <small>
                                <strong>Out:</strong>
                                '. $time_out .'
                            </small>',
                'creator' => '<small><strong>Created: </strong>'. $d->getEmpName($d->created_by) .'<small><br>
                            <small><strong>Updated: </strong>'. ($d->getEmpName($d->updated_by) ?? "-") .'</small>',
                
                'status' => '<div class="status-cell"><div class="status rounded-2"> '. $status .'</div></div>',

                'created_at' => $d->created_at->format('F j, Y') . '<br>' . $d->created_at->format('l'),

                'updated_at' => $d->updated_at->format('F j, Y') . '<br>' . $d->updated_at->format('l'),

                'action' => '<div class="dropdown">
                                <button 
                                    class="btn btn-sm btn-primary dropdown-toggle"
                                    type="button"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    Action
                                </button>

<<<<<<< HEAD
                                <ul class="dropdown-menu">
                                    <li>
                                        <button 
                                            class="dropdown-item"
                                            id="viewBtn"
                                            data-id="'. $d->id .'"
                                            data-type="visitorlog">
                                            <i class="bi bi-eye me-2"></i> View
                                        </button>

                                    </li>
                                    <li>
                                        <button 
                                            type="button"
                                            class="dropdown-item text-danger"
                                            id="timeoutBtn"
                                            data-id="'. $d->id .'">
                                            <i class="bi bi-clock-history me-2"></i> Timeout
                                        </button>

                                    </li>
                                </ul>
                            </div>',
=======
                            <ul class="dropdown-menu">
                                <li>
                                    <button 
                                        class="dropdown-item"
                                        id="viewBtn"
                                        data-id="'. $d->id .'"
                                        data-type="report">
                                        <i class="bi bi-eye me-2"></i> View
                                    </button>
                                </li>
                                <li>
                                    <button type="button" class="dropdown-item text-danger delete-btn" 
                                            data-id="'. $d->id .'">
                                        <i class="bi bi-trash me-2"></i> Delete
                                    </button>
                                </li>
                            </ul>
                        </div>',
>>>>>>> 2a7df276c60bbda649ef6810462b1244ad5a887d
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


}
