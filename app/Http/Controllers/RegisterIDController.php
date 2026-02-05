<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegisteredID\visitorsLogs;
use App\Models\RegisteredID;
use App\Models\VisitorType;
use App\Models\Visitor;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class RegisterIDController extends Controller
{
    // Show the form
    public function index()
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
        return view('pages.registerid.id', compact('registeredIds', 'visitorTypes', 'visitorsLogs'));
    }


    public function list(Request $request){
     
        $keywords = strtolower($request->search);
        $limit    = $request->input('length');

        $rawquery = RegisteredID::with('visitorType')
                    ->withoutTrashed()
                    ->where('deleted_at', null)
                    ->when($keywords, function ($query) use ($keywords) {
                        $query->where('id_number', 'LIKE', "%{$keywords}%")
                            ->orWhereHas('visitorType', function ($q) use ($keywords) {
                                $q->where('name', 'LIKE', "%{$keywords}%");
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
            $data          = $rawquery->orderby("updated_at", "desc")->take($limit)->get();
            $totalFiltered = count($temp);
       
        } else { 
       
            $data          = $rawquery->orderby("updated_at", "desc")->take($limit)->get();
     
            $totalFiltered = $totalRecords;
        }
 
        $newData = [];
        $i       = 0;

        foreach ($data as $d) { 
            $exists = $d->visitorsLogs()->exists();

            if (!$exists) {
                $action = '<div class="dropdown">
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
                                            id="editBtn"
                                            data-id="'.$d->id.'"
                                            data-name="'.$d->id_number.'"
                                            data-type="'.$d->visitor_type.'">
                                            <i class="bi bi-pencil-square me-2"></i> Edit
                                        </button>
                                    </li>
                                    <li>
                                        <button 
                                            type="button"
                                            class="dropdown-item text-danger"
                                            id="deleteBtn"
                                            data-id="'.$d->id.'">
                                            <i class="bi bi-trash me-2"></i> Delete
                                        </button>
                                    </li>
                                </ul>
                            </div>';
            } else {
            $action = '<span class="badge bg-success">Currently Used</span>';

            }


            $newData[$i] = [
                'visitor_type' => $d->visitorType->name,

                'id_number' => $d->id_number,

                'created_by' => $d->getEmpName($d->created_by),

                'updated_by' => ($d->getEmpName($d->updated_by) ?? '-'),

                'created_at' => $d->created_at->format('F j, Y') . '<br>' . $d->created_at->format('l'),

                'updated_at' => $d->updated_at->format('F j, Y') . '<br>' . $d->updated_at->format('l'),

                'action' => $action
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

    public function save(Request $request)
    {
        $request->validate([
            'id_number' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    $exists = RegisteredID::where('id_number', $value)
                        ->whereNull('deleted_at')
                        ->exists();

                    if ($exists) {
                        $fail('Visitor ID already exists.');
                    }
                },
            ],
            'visitor_type' => [
                'required',
                'exists:visitor_types,id',
            ],
        ]);



        $registeredID = new RegisteredID();
        $registeredID->id_number = $request->id_number;
        $registeredID->visitor_type = $request->visitor_type;
        $registeredID->created_by = Auth::id();
        $registeredID->created_at = now();
        $registeredID->save();

        if (!$registeredID) {
            return response()->json([
                'message' => 'Visitor Id not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Visitor Id successfully registered'
        ]);
    }

    public function edit(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:registered_visitor_ids,id',
            'id_number' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    $exists = RegisteredID::whereRaw('id_number = ?', [$value])
                        ->where('id', '!=', $request->id)  // exclude current record
                        ->whereNull('deleted_at')
                        ->exists();
                    if ($exists) {
                        $fail('Visitor ID already exists.');
                    }
                },
            ],
        ]);

        $visitor = RegisteredID::find($request->id);

        $visitor->update([
            'id_number' => $request->id_number,
            'visitor_type' => $request->visitor_type,
            'updated_at' => now(),
            'updated_by' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Visitor ID successfully updated'
        ], 200);
    }



    public function delete(Request $request)
    {
        $visitor = RegisteredID::where('id', $request->id)->first();

        if (!$visitor) {
            return response()->json([
                'message' => 'Visitor Id not found'
            ], 404);
        }

        if ($visitor->deleted_at !== null) {
            return response()->json([
                'message' => 'Visitor Id already deleted'
            ], 400);
        }

        $visitor->update([
            'deleted_at' => Carbon::now(),
            'deleted_by' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Visitor Id successfully deleted'
        ]);
    }

}
