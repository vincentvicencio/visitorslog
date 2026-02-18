<?php

namespace App\Http\Controllers;
use Illuminate\Http\Request;
use App\Models\User_types;
use Illuminate\Support\Facades\Auth;
use Validator;

class User_TypesController extends Controller
{
    public function index()
    {   
        return view('pages.userType.usertype');
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

        $duplicateQuery = User_types::withoutTrashed()
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
            $status     =  User_types::findorFail($record_id);
            $oldData    = $status->getOriginal();

            $status     = $status->update(['updated_by' => $emp_code] + $data);
            $message    = 'UserType Successfully Updated';
        } else {
            $status     = User_types::create(['created_by' => $emp_code] + $data);
            $message    = 'UserType Status Successfully Created';
        }
        return response()->json([
            'status'    => 0,
            'message'   => $message
        ]);

    }

    public function list(Request $request){
     
        $keywords = strtolower($request->search);
        $limit    = $request->input('length');


        $rawquery = User_types::withoutTrashed()->where(function($query) use ($keywords) {
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
            $data          = $rawquery->orderby("id", "desc")->take($limit)->get();
            $totalFiltered = count($temp);
       
        } else { 
       
            $data          = $rawquery->orderby("id", "desc")->take($limit)->get();
     
            $totalFiltered = $totalRecords;
        }
 
        $newData = [];
        $i       = 0;
      
        foreach ($data as $d) { 
       
            $newData[$i] = [
                'name'          => '<div class="text-center">' . $d->name . '</div>', // show emp_code in first column
                'created_by' => user_name($d->created_by) ?? '-',
                'updated_by' => user_name($d->updated_by) ?? '-',
                'created_at' => '<div class="text-center">' . $d->created_at->format('F j, Y'). '<br>'. $d->created_at->format('l') . '</div>',
                'action'            => '<div class="dropdown text-center">
                                        <button class="btn btn-sm btn-primary dropdown-toggle" 
                                                type="button" 
                                                data-bs-toggle="dropdown" 
                                                data-bs-boundary="viewport" aria-expanded="false">
                                            Action
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item btn-edit" data-id="'. $d->id .'"><i class="bi bi-pencil-square me-2"></i> Edit</a></li>
                                            <li><a class="text-danger dropdown-item btn-delete" data-id="'. $d->id .'" data-details="'. $d->name. '"><i class="bi bi-trash me-2"></i> Delete</a></li></li>
                                        </ul>
                                    </div>' 
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
        $record = User_types::find($request->id);
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
        $record  = User_types::find($request->id);
        $details = $record->name;
        $record->update(['deleted_by' => Auth::user()->emp_code]);
        $record->delete();

        $message    = 'User Type Successfully Deleted';
            return response()->json([
                'status'    => 0,
                'message'   => $message
            ]);
    }



    public function destroy($id) 
    {
        try {
            $role = User_types::findOrFail($id);
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
        $role = User_types::findOrFail($id); 
        return response()->json($role);
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'user_type' => 'required|string|max:255|unique:user_types,name,' . $id,
        ]);

        $role = User_types::findOrFail($id);
        $role->update([
            'name' => $request->user_type,
            'updated_by' => Auth::user()->id,
        ]);
    }

}