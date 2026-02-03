<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\VisitorType;
use App\Models\RegisteredID;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Auth\SessionGuar;




class VisitorController extends Controller
{
    public function index()
    {
        $visitorTypes = VisitorType::where('deleted_at', null)
                   ->orderBy('id', 'asc')
                   ->get();
        $visitors = Visitor::where('status', 0)
                   ->orderBy('id', 'asc')
                   ->get();
        return view('homepage.form', compact('visitorTypes', "visitors"));
    }

    public function list(Request $request){

        $keywords = strtolower($request->search);
        // $keywords = strtolower($request->input('search.value'));

        $limit    = $request->input('length');

        // $rawquery = Visitor::with('visitorType')
        //             ->withoutTrashed()
        //             ->where('status', 0)
        //             ->where('deleted_at', null)
        //             ->when($keywords, callback: function ($query) use ($keywords) {
        //                 $query->where('visitor_id', 'LIKE', "%{$keywords}%")
        //                     ->orWhereHas(relation: 'visitorType', function ($q) use ($keywords) {
        //                         $q->where('name', 'LIKE', "%{$keywords}%");
        //                     });
        //             });

        // $rawquery = Visitor::with('visitorType')
        //             ->withoutTrashed()
        //             ->where('status', 0)
        //             ->when($keywords, function ($query) use ($keywords) {
        //                 $query->where('visitor_id', 'LIKE', "%{$keywords}%")
        //                     ->orWhereHas('visitorType', function ($qt) use ($keywords) {
        //                         $qt->where('name', 'LIKE', "%{$keywords}%");
        //                     });

                    
        //             });
        // $rawquery = Visitor::withoutTrashed()->where(function($query) use ($keywords) {
        //         $query->where('first_name', 'LIKE', "%$keywords%")
        //                 ->where('status', 0);
        //     });

        $rawquery = Visitor::with('visitorType')
                ->withoutTrashed()
                ->where('status', 0)
                ->when($keywords, function ($query) use ($keywords) {
                    $query->where(function ($q) use ($keywords) {
                        $q->where('full_name', 'LIKE', "%{$keywords}%")
                        ->orWhere('visitor_id', 'LIKE', "%{$keywords}%")
                        ->orWhere('phone_number', 'LIKE', "%{$keywords}%")
                        ->orWhereHas('visitorType', function ($qt) use ($keywords) {
                            $qt->where('name', 'LIKE', "%{$keywords}%");
                        });
                    });
                });




        
        $totalRecords = $rawquery->get()->count();
        
        if ($request->input('draw') > 1) { 
            $start         = $request->input('start'); 
            $column        = $request->input('order.0.column');
            $direction     = $request->input('order.0.dir');
            $order         = $request->input('columns')[$column]['data']; 
            $temp          = $rawquery->get(); 
            $rawQuery      = $limit > 0 ? $rawquery->skip($start)->take($limit) : $rawquery; 
            $data          = $rawQuery->orderby($order, $direction)->get(); 
            $totalFiltered = count($temp);
       
        } else { 
       
            $data          = $rawquery->orderby("id", "desc")->take($limit)->get();
     
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

    public function timeoutAjax(Request $request)
    {
        $visitor = Visitor::where('id', $request->id)->first();

        if (!$visitor) {
            return response()->json([
                'message' => 'Visitor not found'
            ], 404);
        }

        if ($visitor->status == 1) {
            return response()->json([
                'message' => 'Visitor already timed out'
            ], 400);
        }

        $visitor->update([
            'time_out' => Carbon::now(),
            'status'   => 1,
            'updated_by' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Visitor successfully timed out'
        ]);
    }
    public function view(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:visitors,id',
        ]);

        $visitor = Visitor::where('id', $request->id)
            ->where('status', 0)
            ->latest('id')
            ->first();

        if (!$visitor) {
            return response()->json([
                'message' => 'Visitor not found or inactive'
            ], 404);
        }

        // return response()->json([
        //     'redirect' => route('visitor.view.page', $visitor->id, $visitor->type)
        // ]);
        return response()->json([
            'redirect' => route('visitor.view.page', [
                'id'   => $visitor->id,
                'type' => $request->type,
            ])
        ]);

    }



    public function saveAjax(Request $request)
    {
        $request->validate([
            'first_name'   => 'required|string',
            'middle_name'  => 'nullable|string',
            'last_name'    => 'required|string',
            'visitor_type' => 'required|exists:visitor_types,id',

            'id_number' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    // Check if visitor ID exists in the registered IDs table
                    $existing = RegisteredID::where('id_number', $value)->first();
                    $timein = Visitor::where('visitor_id', $value)
                                ->whereNull('time_out')
                                ->first();
                    if ($timein) {
                        $fail('This Visitor ID is already checked in and has not timed out.');
                    }

                    if (!$existing) {
                        // ID does not exist in registered IDs
                        $fail('This Visitor ID is not registered.');
                    } elseif ($existing->visitor_type != $request->visitor_type) {
                        // ID exists but visitor type does not match
                        $fail('This Visitor ID is registered under a different visitor type.');
                    }
                },
            ],

            'image_path' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);



        try {
            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image_path')) {
                $imagePath = $request->file('image_path')->store('visitors', 'public');
            }
            $middleInitial = mb_strtoupper(mb_substr(trim($request->middle_name), 0, 1));
            // $location = collect(session('all_location'));
            // foreach ($location as $record) {
            //     $data[] = [
            //         'id'   => $record['id'], // Ensure 'id' exists in your session array
            //         'text' => $record['name']
            //     ];
            // }
            // Save visitor
            $visitor = new Visitor();
            $visitor->full_name   = $request->first_name .' '. $middleInitial .'. '. $request->last_name;
            $visitor->first_name   = $request->first_name;
            $visitor->middle_name  = $request->middle_name;
            $visitor->last_name    = $request->last_name;
            $visitor->phone_number = $request->contact_number;
            $visitor->visitor_type = $request->visitor_type;
            $visitor->visitor_id   = $request->id_number;
            $visitor->location     = Auth::user()->location_id;
            $visitor->address     = $request->address;
            $visitor->created_by   = Auth::id();
            $visitor->image_path   = $imagePath;
            $visitor->time_in      = now();
            $visitor->save();


            return response()->json([
                'message' => 'Visitor successfully added'
            ], 200);



        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error saving visitor: ' . $e->getMessage(),
            ], 500);
        }
    }
}

