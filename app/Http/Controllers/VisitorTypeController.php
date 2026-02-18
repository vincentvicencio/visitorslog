<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VisitorType;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;


class VisitorTypeController extends Controller
{
    public function index()
    {   
        return view('pages.visitorType.visitortype');
    }

    public function save(Request $request)
    {
        $validator = Validator::make(
            $request->all(),
            [
                'name'              => ['required', 'string', 'max:40','regex:/^[a-zA-Z\s]+$/'],
            ],
            [
                'name.required'     => 'Visitor Type is Required',
                'name.regex'        => 'Visitor Type must contain letters only'
            ]
        );

        if($validator->fails()){
            return response()->json(['status' => 1, 'title' => 'Invalid', 'errors' => $validator->errors()]);
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
                'title'     => 'Invalid',
                'message'   => 'Visitor Type Already Exists'
            ]);

        }

        $data           = [
            'name'           => $request->name,
        ];

        if ($record_id > 0) {
            $status     =  VisitorType::findorFail($record_id);
            $oldData    = $status->getOriginal();

            $status     = $status->update(['updated_by' => $emp_code] + $data);
            $message    = 'Visitor Type Successfully Updated';
        } else {
            $status     = VisitorType::create(['created_by' => $emp_code] + $data);
            $message    = 'Visitor Type Successfully Created';
        }
        return response()->json([
            'status'    => 0,
            'title'     => 'Success',
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
                'created_by' => $d->created_by ? user_name($d->created_by) : '-',
                'updated_by' => $d->updated_by ? user_name($d->updated_by) : '-',
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
                                            <li><a class="text-danger dropdown-item btn-delete" data-id="'. $d->id .'" data-details="'. $d->name. '"><i class="bi bi-trash me-2"></i> Delete</a></li></li>
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

    public function search(Request $request){
        $record = VisitorType::find($request->id);
        if(!$record){
            return response()->json([
                'status'     => 1,
                'title'      => 'Error',
                'message'    => 'No Data Found'
            ]);
        }

        return response()->json([
            'status'     => 0,
            'title'      => 'Success',
            'data'       => $record
        ]);
    }

    public function delete(Request $request){
        $record  = VisitorType::find($request->id);
        $details = $record->name;
        $record->update(['deleted_by' => Auth::user()->id]);
        $record->delete();

        $message = 'Visitor Type Successfully Deleted';
            return response()->json([
                'status'     => 0,
                'title'      => 'Success',
                'message'    => $message
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
            return response()->json(['status' => 1, 'title' => 'Error', 'message' => 'Failed to delete visitor type.'], 500);
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