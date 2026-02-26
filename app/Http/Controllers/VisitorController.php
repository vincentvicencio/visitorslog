<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\VisitorType;
use App\Models\RegisteredID;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;

class VisitorController extends Controller
{
    public function index()
    {
        $visitors = Visitor::where(function ($query) {
            $query->where('status', 0)->orWhereNull('status');
            })
                -> whereNull('time_out')
                -> orderBy('id', 'desc')
                -> get();

        $visitorTypes = VisitorType::where('deleted_at', null)
                -> orderBy('id', 'desc')
                -> get();

        $empMap = collect(session('all_emp'))->keyBy('emp_code');

        // if (session('from_form')) {
        //     session()->forget('from_form');
        //     return view('pages.visitorslog.form', compact('visitorTypes', "visitors"));
        // }else{
            return view('pages.visitorslog.visitorlog', compact('visitors', 'visitorTypes', 'empMap'));
        // }
    }
    public function form()
    {
        // session(['from_form' => true]);
        $visitorTypes = VisitorType::where('deleted_at', null)
                -> orderBy('id', 'asc')
                -> get();
        $visitors     = Visitor::where('status', 0)
                -> orderBy('id', 'asc')
                -> get();
        return view('pages.visitorslog.form', compact('visitorTypes', "visitors"));
    }

    public function list(Request $request){
        $keywords = strtolower($request->search);
        $limit    = $request->input('length');


        $rawquery = Visitor::with('visitorType')
                -> withoutTrashed()
                ->where(function ($query) {

                    $user = Auth::user();
                    $userLocations = array_map('intval', (array) $user->location);

                    // First filter by location
                    $query->whereIn('location', $userLocations);

                    // Then filter active / not timed out
                    $query->where(function ($q) {
                        $q->where('status', 0)
                        ->where('time_out', null);
                    });

                    // Then restrict to creator if NOT admin
                    if (Auth::user()->user_type != 1) {
                        $query->where('created_by', Auth::user()->id);
                    }
                })

                -> when($keywords, function ($query) use ($keywords) {
                    $query->where(function ($q) use ($keywords) {
                        $q->where   ('full_name', 'LIKE', "%{$keywords}%")
                        ->orWhere   ('visitor_id', 'LIKE', "%{$keywords}%")
                        ->orWhere   ('phone_number', 'LIKE', "%{$keywords}%")
                        ->orWhereHas('visitorType', function ($qt) use ($keywords) {
                            $qt->where('name', 'LIKE', "%{$keywords}%");
                        });
                    });
                });
        
        $totalRecords = $rawquery->get()->count();
        
        if ($request->input('draw') > 1) { 
            $start         = $request ->input('start'); 
            $column        = $request ->input('order.0.column');
            $direction     = $request ->input('order.0.dir');
            $order         = $request ->input('columns')[$column]['data']; 
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

            $status = '';

            if($d->status == 0){
                $status = 'Active';
            }else{
                $status = 'Timed Out';
            }

            $time_in = Carbon::parse($d->time_in)->format('h:i A');

            $time_out = $d->time_out ? Carbon::parse($d->time_out)->format('h:i A') : '-';

            $createdby = $d->created_by ? user_name($d->created_by) : '-';
            $updatedby = $d->updated_by ? user_name($d->updated_by) : '-';
            

            $newData[$i] = [
                

                'full_name' => $d->full_name,

                'location' => '<div class="text-center">' . $locationLabel . '</div>',

                'contact_number' => '<div class="text-center">' . $d->phone_number . '</div>',

                'visitor_type' => '<div class="text-center">' . ($d->visitorType?->name ?? '-') . '</div>',

                'visitor_id'   => '<div class="text-center">' . $d->visitor_id . '</div>',

                'visit' =>  '<div class="text-center">' . 
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
                
                'status'       => '<div class="status-cell"><div class="status rounded-2"> '. $status .'</div></div>',

                'created_at'   => '<div class="text-center">' . $d->created_at->format('F j, Y') . '<br>' . $d->created_at->format('l') . '</div>',

                'updated_at'   => $d->updated_at->format('F j, Y') . '<br>' . $d->updated_at->format('l'),

                'action' => '<div class="dropdown">
                                        <button 
                                            class="dropdown-item"
                                            id="viewBtn"
                                            data-id="'. $d->id .'"
                                            data-type="visitorslog">
                                            View
                                        </button>
                                        <button 
                                            type="button"
                                            class="dropdown-item text-danger"
                                            id="timeoutBtn"
                                            data-id="'. $d->id .'">
                                            Timeout
                                        </button>
                            </div>',
            ];
            $i++;
                                            // <i class="bi bi-clock-history"></i>
                                            // <i class="bi bi-eye"></i>
        }
 
        return response()->json([
            'draw'              => intval($request->input('draw')),
            'recordsTotal'      => $totalRecords,
            'recordsFiltered'   => $totalFiltered,
            'data'              => $newData

        ]);
    }

    public function timeout(Request $request)
    {
        $visitor = Visitor::where('id', $request->id)->first();

        if (!$visitor) {
            return response()->json([
                'status'  => 1,
                'title'   => 'Error',
                'message' => 'Visitor not found'
            ], 404);
        }

        if ($visitor->status == 1) {
            return response()->json([
                'status'  => 1,
                'title'   => 'Error',
                'message' => 'Visitor already timed out'
            ], 400);
        }

        $visitor->update([
            'time_out'   => Carbon::now(),
            'status'     => 1,
            'updated_by' => Auth::user()->id,
        ]);

        return response()->json([
            'status'     => 0,
            'title'      => 'Success',
            'message'    => 'Visitor successfully timed out'
        ]);
    }
    public function view(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:visitors,id',
        ]);

        $visitor = Visitor::where('id', $request->id)
            ->latest('id')
            ->first();

        if (!$visitor) {
            return response()->json([
                'message' => 'Visitor not found or inactive'
            ], 404);
        }
        return response()->json([
            'redirect'   => route('view.page', [
                'id'     => $visitor->id,
                'type'   => $request->type,
            ])
        ]);

    }

    public function search(Request $request)
    {
        $id   = $request->input('id'); // make sure this is numeric
        $user = Visitor::find($id);

        if ($user) {
            return response()->json([
                'success' => true,
                'data'    => $user
            ]);
        }

        return response()->json([
            'success' => false,
            'message' => 'User not found.'
        ]);
    }

    public function save(Request $request)
    {
        try {
            $request->validate([
                'first_name'        => ['required', 'string', 'max:40'],
                'middle_name'       => ['nullable', 'string', 'max:40'],
                'last_name'         => ['required', 'string', 'max:40'],
                'visitor_type'      => 'required|exists:visitor_types,id',
                'contact_number'    => ['required','min:11','max:11','starts_with:09'],            
                'image_path'        => 'nullable|image|mimes:jpg,jpeg,png|max:5120',

                'id_number'    => [
                    'required',
                    'string',
                    function ($attribute, $value, $fail) use ($request) {
                        $existing = RegisteredID::where('id_number', $value)->first();
                        $timein = Visitor::where('visitor_id', $value)
                                    ->whereNull('time_out')
                                    ->first();
                        if ($timein) {
                            $fail('This Visitor ID is already checked in and has not timed out.');
                        }

                        if (!$existing) {
                            $fail('This Visitor ID is not registered.');
                        } elseif ($existing->visitor_type != $request->visitor_type) {
                            $fail('This Visitor ID is registered under a different visitor type.');
                        }
                    },
                ],

            ], [
                'first_name.required'        => 'First Name is required',
                'last_name.required'         => 'Last Name is required',
                'visitor_type.required'      => 'Visitor Type is required',
                'contact_number.required'    => 'Contact Number is required',
                'contact_number.max'         => 'Contact Number must not exceed 11 digits',
                'contact_number.min'         => 'Contact Number must be at least 11 digits',
                'contact_number.starts_with' => 'Contact Number must start with 09',
            ]);

        } catch (\Illuminate\Validation\ValidationException $e) {
            // Error hierarchy
            $priority = ['id_number', 'visitor_type', 'first_name', 'last_name', 'contact_number', 'image_path', 'middle_name'];
            $errors = $e->errors();
            $firstError = null;
            foreach ($priority as $field) {
                if (isset($errors[$field])) {
                    $firstError = $errors[$field][0];
                    break;
                }
            }
            if (!$firstError) {
                $firstError = collect($errors)->flatten()->first();
            }
            return response()->json([
                'status' => 1,
                'message' => $firstError,
            ], 422);
        }



        try {
            $imagePath = null;
            if(!empty($request->image_path)) {
                
                $image     = $request->image_path;

                // Remove metadata (data:image/png;base64,)
                $image     = preg_replace('/^data:image\/\w+;base64,/', '', $image);

                // Decode base64
                $image     = base64_decode($image);

                // Generate filename


                // Save to public disk
                Storage::disk('public')->put($fileName, $image);

            $imagePath = $fileName;
            }else{
                if ($request->hasFile('imageInput') && $request->file('imageInput')->isValid()) {
                    $fileName  = 'visitors/' . Str::random(20) . '.png';
                    $imagePath = $request->file('imageInput')->storeAs('visitors', $fileName, 'public');
                }
            }
            

            $middleInitial = mb_strtoupper(mb_substr(trim($request->middle_name), 0, 1));

            $userLocations = (array) Auth::user()->location;

            $visitor = new Visitor();
            // clint - remove dot when there is no middle name
            if (!empty($middleInitial)) {
                $visitor->full_name = $request->first_name . ' ' . $middleInitial . '. ' . $request->last_name;
            } else {
                $visitor->full_name = $request->first_name . ' ' . $request->last_name;
            }
            // original code - kardo
            // $visitor->full_name   = $request->first_name .' '. $middleInitial .'. '. $request->last_name;
            $visitor->first_name   = $request->first_name;
            $visitor->middle_name  = $request->middle_name;
            $visitor->last_name    = $request->last_name;
            $visitor->phone_number = $request->contact_number ?? '?';
            $visitor->visitor_type = $request->visitor_type;
            $visitor->visitor_id   = $request->id_number;
            $visitor->location     = $userLocations[0] ?? null;
            $visitor->address      = $request->address;
            $visitor->created_by   = Auth::user()->id;
            $visitor->image_path   = $imagePath;
            $visitor->time_in      = now();
            $visitor->status       = 0;
            $visitor->save();


            return response()->json([
                'status' => 0,
                'title' => 'Success',
                'message' => 'Visitor successfully added'
            ], 200);
            
        } catch (\Exception $e) {
            return response()->json([
                    'status' => 1,
                    'title' => 'Invalid',
                'message' => 'Error saving visitor: ' . $e->getMessage(),
            ], 500);
        }
    }
}

