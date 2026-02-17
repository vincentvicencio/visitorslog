<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VisitorType;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class VisitorTypeController extends Controller
{
    // Show the form
    // public function index()
    // {
    //     $visitorTypes = VisitorType::where('deleted_at', null)
    //     ->orderBy('id', 'desc')
    //     ->get();
    //     return view('pages.visitorType.visitortype', compact('visitorTypes'));
    // }

    public function index()
    {   
        return view('pages.visitorType.visitortype');
    }


    public function save(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name'              => 'required',
            ],
            [
                'name'              => 'Name is Required'
            ]
        );

        if($validator->fails()){
            return response()->json(['status' => 1,'errors' => $validator->errors()]);
        }

        $record_id      = $request->record_id;
        $emp_code       = Auth::user()->id;
        $name           = trim(string: $request->name);

        $duplicateQuery = VisitorType::withoutTrashed()
                            ->whereRaw('LOWER(name) = ?', [strtolower($name)]);

        if($record_id > 0){
            $duplicateQuery->where('id', '!=', $record_id);
        }
        
        if($duplicateQuery->exists()){
            return response()->json([
                'status'    => 1,
                'message'   => 'Name Already Exists'
            ]);

        }

        $data       = [
            'name'               => $request->name,
        ];

        if ($record_id > 0) {
            $status     =  VisitorType::findorFail($record_id);
            $oldData    = $status->getOriginal();

            $status     = $status->update(['updated_by' => $emp_code] + $data);
            $message    = 'VisitorType Successfully Updated';
        } else {
            $status     = VisitorType::create(['created_by' => $emp_code] + $data);
            $message    = 'VisitorType Successfully Created';
        }
        return response()->json([
            'status'    => 0,
            'message'   => $message
        ]);

    }


    public function list(Request $request){
     
        $keywords = strtolower($request->search);
        $limit    = $request->input('length');

        $rawquery = VisitorType::withoutTrashed()->where(function($query) use ($keywords) {
                        $query->where('name', 'LIKE', "%$keywords%")
                            ->where('deleted_at', null);
                    });
        
        $totalRecords = $rawquery->get()->count();
        
        if ($request->input('draw') > 1) { 
             $start         = $request->input('start'); 
            $column        = $request->input('order.0.column');
            $direction     = $request->input('order.0.dir');
            $order         = $request->input('columns')[$column]['data']; 
            $temp          = $rawquery->get(); 
            $rawQuery      = $limit > 0 ? $rawquery->skip($start)->take($limit) : $rawquery; 
            $data          = $rawquery->orderBy("updated_at", "desc")->take($limit)->get();
            $totalFiltered = count($temp);
       
        } else { 
       
            $data          = $rawquery->orderby("updated_at", "desc")->take($limit)->get();
     
            $totalFiltered = $totalRecords;
        }
 
        $newData = [];
        $i       = 0;

        foreach ($data as $d) { 
       
            $newData[$i] = [
                'name'          => $d->name, // show emp_code in first column
                'created_by' => user_name($d->created_by) ?? '-',
                'updated_by' => user_name($d->updated_by) ?? '-',
                'created_at' => $d->created_at->format('F j, Y'). '<br>'. $d->created_at->format('l'),
                'action'            => '<div class="dropdown">
                                            <button 
                                                class="btn btn-sm btn-primary dropdown-toggle"
                                                type="button"
                                                data-bs-toggle="dropdown"
                                                aria-expanded="false">
                                                Action
                                            </button>
                                            <ul class="dropdown-menu">
                                            <li><a class="dropdown-item btn-edit" data-id="'. $d->id .'"><i class="bi bi-pencil-square me-2"></i> Edit</a></li>
                                            <li><a class="dropdown-item btn-delete" data-id="'. $d->id .'" data-details="'. $d->name. '"><i class="bi bi-pencil-square me-2"></i> Delete</a></li></li>
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

    // public function save(Request $request)
    // {
    //     $request->validate([
    //         'visitor_type' => [
    //             'required',
    //             'string',
    //             function ($attribute, $value, $fail) {
    //                 $exists = VisitorType::whereRaw('LOWER(name) = ?', [strtolower($value)])
    //                             ->whereNull('deleted_at') // only consider non-deleted rows
    //                             ->exists();

    //                 if ($exists) {
    //                     $fail('Visitor Type already exists.');
    //                 }
    //             },
    //         ],
    //     ]);


    //     try {
    //         $id = new VisitorType();
    //         $id->name = ucfirst(strtolower($request->visitor_type)); // normalize case
    //         $id->created_by = Auth::user()->id;
    //         $id->created_at = now();
    //         $id->save();

    //         return response()->json([
    //             'message' => 'Visitor Type successfully added'
    //         ], 200);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'message' => 'Error saving Visitor Type: ' . $e->getMessage(),
    //         ]);
    //     }
    // }
    // public function edit(Request $request)
    // {
    //     $request->validate([
    //         'id' => 'required|exists:visitor_types,id',
    //         'visitor_type' => [
    //             'required',
    //             'string',
    //             function ($attribute, $value, $fail) use ($request) {
    //                 $exists = VisitorType::whereRaw('LOWER(name) = ?', [strtolower($value)])
    //                     ->where('id', '!=', $request->id)  // exclude current record
    //                     ->whereNull('deleted_at')
    //                     ->exists();
    //                 if ($exists) {
    //                     $fail('Visitor Type already exists.');
    //                 }
    //             },
    //         ],
    //     ]);

    //     $visitor = VisitorType::find($request->id);

    //     $visitor->update([
    //         'name' => ucfirst(strtolower($request->visitor_type)),
    //         'updated_at' => now(),
    //         'updated_by' => Auth::user()->id,
    //     ]);

    //     return response()->json([
    //         'message' => 'Visitor type successfully updated'
    //     ], 200);
    // }



    // public function delete(Request $request)
    // {
    //     $visitor = VisitorType::where('id', $request->id)->first();

    //     if (!$visitor) {
    //         return response()->json([
    //             'message' => 'Visitor Type not found'
    //         ], 404);
    //     }

    //     if ($visitor->deleted_at !== null) {
    //         return response()->json([
    //             'message' => 'Visitor type already deleted'
    //         ], 400);
    //     }

    //     $visitor->update([
    //         'deleted_at' => Carbon::now(),
    //         'deleted_by' => Auth::user()->id,
    //     ]);

    //     return response()->json([
    //         'message' => 'Visitor type successfully deleted'
    //     ]);
    // }


    public function search(Request $request){
        $record = VisitorType::find($request->id);
        if(!$record){
            return response()->json([
                'status'    => 1,
                'message'   => 'No Data Found'
            ]);
        }

        return response()->json([
            'status'    => 0,
            'data'      => $record
        ]);
    }


    public function delete(Request $request){
        $record  = VisitorType::find($request->id);
        $details = $record->name;
        $record->update(['deleted_by' => Auth::user()->emp_code]);
        $record->delete();

        $message    = 'Visitor Type Successfully Deleted';
            return response()->json([
                'status'    => 0,
                'message'   => $message
            ]);
    }



    public function destroy($id) 
    {
        try {
            $role = VisitorType::findOrFail($id);
            $role->update([
                'deleted_at' => now(), 
                'deleted_by' => Auth::user()->id, 
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete role.'], 500);
        }
    }

    public function edit($id)
    {
        $role = VisitorType::findOrFail($id); 
        return response()->json($role);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'visitor_type' => 'required|string|max:255|unique:visitor_types,name,' . $id,
        ]);

        $role = VisitorType::findOrFail($id);
        $role->update([
            'name' => $request->visitor_type,
            'updated_by' => Auth::user()->id,
        ]);
    }
}