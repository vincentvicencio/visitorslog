<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\VisitorType;
use App\Models\RegisteredID;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


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
    //  public function list(Request $request){
     
    //     $keywords = strtolower($request->search);
    //     $limit    = $request->input('length');

    //     $rawquery = Visitor::withoutTrashed()->where(function($query) use ($keywords) {
    //                     $query->where('emp_code',         'LIKE', "%$keywords%");
    //                 });

    //     $totalRecords = $rawquery->get()->count();
        
    //     if ($request->input('draw') > 1) { 
    //         $start         = $request->input('start'); 
    //         $column        = $request->input('order.0.column');
    //         $direction     = $request->input('order.0.dir');
    //         $order         = $request->input('columns')[$column]['data']; 
    //         $temp          = $rawquery->get(); 
    //         $rawQuery      = $limit > 0 ? $rawquery->skip($start)->take($limit) : $rawquery; 
    //         $data          = $rawquery->orderBy("updated_at", "desc")->take($limit)->get();
    //         $totalFiltered = count($temp);
    //     } else { 
    //         $data          = $rawquery->orderBy("updated_at", "desc")->take($limit)->get();
    //         $totalFiltered = $totalRecords;
    //     }

    //     $newData = [];
    //     $i       = 0;
 
    //     foreach ($data as $d) { 
            
    //         $newData[$i] = [
    //             'emp_code'          => $d->emp_code, // show emp_code in first column
    //             'emp_name'          => function_exists('user_name') ? user_name($d->emp_code) : $d->emp_code,
    //             // 'user_type' => $d->user_type == 1 ? 'User' : 'Admin',
    //             'user_type' => optional($d->type)->name ?? 'N/A',
    //             'updated_date' => $d->created_at->format('F j, Y'),
    //             'action'            => '<button class="btn-edit" title="Edit"  data-id="'.$d->id.'" >  
    //                                     <svg xmlns="http://www.w3.org/2000/svg" 
    //                                         width="20" 
    //                                         height="20" 
    //                                         fill="none" 
    //                                         viewBox="0 0 24 24" 
    //                                         stroke="currentColor">
    //                                         <path stroke-linecap="round" 
    //                                             stroke-linejoin="round" 
    //                                             stroke-width="2"
    //                                             d="M11 5H6a2 2 0 00-2 2v11a2 2 0 002 2h11a2 2 0 002-2v-5m-1.414-9.414a2 2 0 112.828 2.828L11.828 15H9v-2.828l8.586-8.586z"/>
    //                                     </svg>
    //                                 </button>
                            
    //                                     <button class="btn-delete" 
    //                                             title="Delete"  
    //                                             data-id="'.$d->emp_code.'" 
    //                                             data-details="'.(function_exists('user_name') ? user_name($d->emp_code) : $d->emp_code).'" 
    //                                             id="delete_btn">
    //                                         <svg xmlns="http://www.w3.org/2000/svg" width="20" height="20" fill="none" viewBox="0 0 24 24" stroke="currentColor">
    //                                             <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M19 7l-.867 12.142A2 2 0 0116.138 21H7.862a2 2 0 01-1.995-1.858L5 7m5 4v6m4-6v6M9 7h6m2 0H7m2-3h6a1 1 0 011 1v1H8V5a1 1 0 011-1z"/>
    //                                         </svg>
    //                                     </button>' 
    //         ];

    //         $i++;
    //     } 
 
    //     return response()->json([
    //         'draw'              => intval($request->input('draw')),
    //         'recordsTotal'      => $totalRecords,
    //         'recordsFiltered'   => $totalFiltered,
    //         'data'              => $newData            
    //     ]);
    // }

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

            // Save visitor
            $visitor = new Visitor();
            $visitor->first_name   = $request->first_name;
            $visitor->middle_name  = $request->middle_name;
            $visitor->last_name    = $request->last_name;
            $visitor->phone_number = $request->contact_number;
            $visitor->visitor_type = $request->visitor_type;
            $visitor->visitor_id   = $request->id_number;
            $visitor->location     = $request->location;
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

