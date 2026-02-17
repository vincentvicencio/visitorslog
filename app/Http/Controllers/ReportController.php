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
use App\Exports\ReportsExport;
use Maatwebsite\Excel\Facades\Excel;

class ReportController extends Controller
{
    public function index(Request $request)
    {
        // $visitorlogs = Visitor::where('status', 1)
        //             ->orderBy('id', 'asc')
        //             ->get();
        $visitorlogs = Visitor::where('status', 0)
            ->withoutTrashed()
            ->orderBy('id', 'asc')
            ->get();
        $visitorTypes = VisitorType::all();
        $allEmployeesFromSession = session('all_emp', []);

        return view('pages.reports.report', compact('visitorlogs', 'visitorTypes', 'allEmployeesFromSession'));
    }


    public function destroy($id)

    {
    try {
            $visitor = Visitor::findOrFail($id);

            // Instead of $user->delete(), we update the column
            $visitor->update([
                'deleted_at' => NOW(),
                'deleted_by' => Auth::user()->id // Optional: track who deleted it
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

        $limit    = $request->input('length');

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
                    
            if ($status === 'Timed Out') {
                $statuslayout = '<div class="status-cell"><div class="status text-danger border border-danger"> '. $status .'</div></div>';
            }
            else{
                $statuslayout = '<div class="status-cell"><div class="status" > '. $status .'</div></div>';
            }

            $newData[$i] = [
                'full_name' => '
                    <strong>' . $d->full_name . '</strong>
                    <br><small>' . $locationLabel . '</small>
                    <br><small>' . $d->phone_number . '</small>
                ',

                'visitor_type' => $d->visitorType?->name ?? '-',

                'visitor_id' => $d->visitor_id,

                'image' => $image,

                'visit' =>  $d->created_at->format("F d, Y") .'<br>
                        '. $d->created_at->format('l'),

                'time' => '<small><strong>In:</strong> '. $time_in .'</small><br>
                            <small>
                                <strong>Out:</strong>
                                '. $time_out .'
                            </small>',
                'creator' => '<small><strong>Created: </strong>'. user_name($d->created_by) ?? '-' .'</small><br>
                            <small><strong>Updated: </strong>'. user_name($d->updated_by) ?? '-' .'</small>',
                
                'status' => $statuslayout,

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

                            <ul class="dropdown-menu">
                                <li>
                                    <button 
                                        class="dropdown-item"
                                        id="viewBtn"
                                        data-id="'. $d->id .'"
                                        data-type="reports">
                                        <i class="bi bi-eye me-2"></i> View
                                    </button>
                                </li>
                                        <li><a class="dropdown-item btn-delete" data-id="'. $d->id .'" data-details="'. $d->full_name. '"><i class="bi bi-trash me-2"></i> Delete</a></li>
                            </ul>
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
        $filters = [
            'search' => $request->input('search', ''),
            'date_from' => $request->input('date_from', ''),
            'date_to' => $request->input('date_to', ''),
            'visitor_type' => $request->input('visitor_type', ''),
        ];

        $fileName = 'Visitor_Report_' . now()->format('Y-m-d_H-i-s') . '.xlsx';
        
        return Excel::download(new ReportsExport($filters), $fileName);
    }

    public function delete(Request $request){
        $record  = Visitor::find($request->id);
        $details = $record->name;
        $record->update(['deleted_by' => Auth::user()->emp_code]);
        $record->delete();

        $message    = 'Report Log Successfully Deleted';
            return response()->json([
                'status'    => 0,
                'message'   => $message
            ]);
    }
}