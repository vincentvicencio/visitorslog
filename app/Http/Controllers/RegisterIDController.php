<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegisteredID;
use App\Models\VisitorType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class RegisterIDController extends Controller
{
    public function index()
    {   
        $visitorTypes = VisitorType::where('deleted_at', null)
                   ->orderBy('id', 'desc')
                   ->get();
        return view('pages.registerID.id', compact('visitorTypes'));
    }
    public function save(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name'              => 'required',
                'visitorType'       => 'required|exists:visitor_types,id',
            ],
            [
                'name.required'           => 'Visitor ID is Required',
                'name.regex'              => 'Visitor ID must contain only numbers',
                'name.max'                => 'Visitor ID must not exceed 4 digits',
                'name.min'                => 'Visitor ID must be at least 4 digits',
                'visitorType.required'    => 'Visitor Type is Required',
            ]
        );

        if($validator->fails()){
            return response()->json(['status' => 1,'errors' => $validator->errors()]);
        }

        $record_id      = $request->record_id;
        $emp_code       = Auth::user()->id;
        $name           = trim(string: $request->name);

        $duplicateQuery = RegisteredID::withoutTrashed()
                            ->whereRaw('LOWER(id_number) = ?', [strtolower($name)]);

        if($record_id > 0){
            $duplicateQuery->where('id', '!=', $record_id);
        }
        
        if($duplicateQuery->exists()){
            return response()->json([
                'status'    => 1,
                'message'   => 'Visitor ID Already Exists'
            ]);

        }

        $data       = [
            'id_number'       => $request->name,
            'visitor_type'    => $request->visitorType,
        ];

        if ($record_id > 0) {
            $status     =  RegisteredID::findorFail($record_id);
            $oldData    = $status->getOriginal();

            $status     = $status->update(['updated_by' => $emp_code] + $data);
            $message    = 'Visitor ID Successfully Updated';
        } else {
            $status     = RegisteredID::create(['created_by' => $emp_code] + $data);
            $message    = 'Visitor ID Successfully Created';
        }
        return response()->json([
            'status'    => 0,
            'message'   => $message
        ]);

    }

    public function list(Request $request){
     
        $keywords = strtolower($request->search);
        $limit    = $request->input('length');

        $rawquery = RegisteredID::with('visitorType')
                    ->whereNull('deleted_at')
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
            $exists     = $d->visitorsLogs()->exists();

            if (!$exists) {
                $action = '<div class="dropdown">
<<<<<<< HEAD
                                <button class="dropdown-item btn-edit" data-id="'. $d->id .'"> Edit</button>
                                <button class="text-danger dropdown-item btn-delete" data-id="'. $d->id .'" data-details="'. $d->id_number. '"> Delete</button>
=======
                                <button 
                                    class="btn btn-sm btn-primary dropdown-toggle"
                                    type="button"
                                    data-bs-toggle="dropdown"
                                    aria-expanded="false">
                                    Action
                                </button>
                                <ul class="dropdown-menu">
                                    <li><a class="dropdown-item btn-edit" data-id="'. $d->id .'"><i class="bi bi-pencil-square me-2"></i> Edit</a></li>
                                    <li><a class=" text-danger dropdown-item btn-delete" data-id="'. $d->id .'" data-details="'. $d->id_number. '"><i class="bi bi-trash me-2"></i> Delete</a></li></li>
                                    <li>
                                        <a class="dropdown-item btn-edit" data-id="'. $d->id .'">
                                        <i class="bi bi-pencil-square me-2"></i> Edit</a>
                                    </li>
                                    <li>
                                        <a class="dropdown-item btn-delete" data-id="'. $d->id .'" data-details="'. $d->id_number. '">
                                        <i class="bi bi-trash me-2"></i> Delete</a>
                                    </li>
                                </ul>
>>>>>>> 75c44668e6d6ef9ac1cfee5591451d7eb505707e
                            </div>';
            } else {
            $action = '<span class="badge bg-success">Currently Used</span>';

            }


            $newData[$i] = [
                'visitor_type' => $d->visitorType?->name ?? '-',

                'id_number'    => $d->id_number,

                'created_by' => $d->created_by ? user_name($d->created_by) : '-',
                'updated_by' => $d->updated_by ? user_name($d->updated_by) : '-',

                'created_at' => $d->created_at ? ($d->created_at->format('F j, Y') . '<br>' . $d->created_at->format('l')) : '-',

                'updated_at' => $d->updated_at ? ($d->updated_at->format('F j, Y') . '<br>' . $d->updated_at->format('l')) : '-',
                'created_by'   => user_name($d->created_by) ?? '-',
                'updated_by'   => user_name($d->updated_by) ?? '-',

                'created_at'   => $d->created_at->format('F j, Y') . '<br>' . $d->created_at->format('l'),

                'updated_at'   => $d->updated_at->format('F j, Y') . '<br>' . $d->updated_at->format('l'),

                'action'       => $action
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

    public function search(Request $request){
        $record = RegisteredID::find($request->id);
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
        $record  = RegisteredID::find($request->id);
        $details = $record->id_number;
        $record  -> update(['deleted_by' => Auth::user() -> id]);
        $record  -> delete();

        $message    = 'Registered ID Successfully Deleted';
            return response()->json([
                'status'    => 0,
                'message'   => $message
            ]);
    }

    public function destroy($id) 
    {
        try {
            $role = RegisteredID::findOrFail($id);
            $role->update(['deleted_by' => Auth::user()->id]);
            $role->delete();
            
        } catch (\Exception $e) {
            return response() -> json(['message' => 'An error occurred while deleting the Registered ID.'], 500);
        }
    }   
    public function edit($id)
    {
        $role = RegisteredID::findOrFail($id); 
        return response() -> json($role);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'RegisteredID' => 'required|string|max:255|unique:registered_ids,name,' . $id,
        ]);

        $role = RegisteredID::findOrFail($id);
        $role->update([
            'name'       => $request->RegisteredID,
            'updated_by' => Auth::user() -> id,
        ]);
    }

}
