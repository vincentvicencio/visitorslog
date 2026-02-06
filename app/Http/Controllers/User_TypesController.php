<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User_types;
use App\Models\VisitorType;
use Illuminate\Support\Facades\Auth;

class User_TypesController extends Controller
{
    public function index()
    {
        $roles = User_types::all(); 
        $visitorTypes = VisitorType::all();
        $empMap = collect(session('all_emp'))->keyBy('emp_code');
        
        return view('pages.userType.usertype', compact('roles', 'visitorTypes','empMap'));
    }
    public function addusertype(Request $request)
    {
        $request->validate([
            'user_type' => 'required|string|max:255|unique:user_types,name',
        ]);

        User_types::create([
            'name'       => $request->user_type,
            'created_by' => Auth::id(),
            'updated_by' => Auth::id(),
        ]);

        return response()->json(['status' => 'success']);
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
                'created_by' => '<div class="text-center">' . $d->getEmpName($d->created_by) . '</div>',
                'updated_by' => '<div class="text-center"> ' . ($d->getEmpName($d->updated_by) ?? '-') . '</div>',
                'created_at' => '<div class="text-center">' . $d->created_at->format('F j, Y'). '<br>'. $d->created_at->format('l') . '</div>',
                'action'            => '<div class="dropdown text-center">
                                        <button class="btn btn-sm btn-primary dropdown-toggle" 
                                                type="button" 
                                                data-bs-toggle="dropdown" 
                                                data-bs-boundary="viewport" aria-expanded="false">
                                            Action
                                        </button>
                                        <ul class="dropdown-menu">
                                            <li><a class="dropdown-item edit-type" href="javascript:void(0)" data-id="'. $d->id .'"><i class="bi bi-pencil-square me-2"></i> Edit</a></li>
                                            <li><button class="dropdown-item text-danger delete-type" data-id="'. $d->id .'"><i class="bi bi-trash me-2"></i> Delete</button></li>
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

    public function destroy($id) 
    {
        try {
            $role = User_types::findOrFail($id);
            $role->update([
                'deleted_at' => now(), 
                'deleted_by' => Auth::id(), 
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
            'updated_by' => Auth::id(),
        ]);
    }

    public function save(Request $request)
    {
        $record = $request->id;
        $request->validate([
            'user_type' => 'required|string|max:255|unique:user_types,name',
        ]);

        if($record){
            $role = User_types::findOrFail($record);
            $role->update([
                'name' => $request->user_type,
                'updated_by' => Auth::id(),
            ]);
        } else {
            User_types::create([
                'name' => $request->user_type,
                'created_by' => Auth::id(),
                'updated_by' => Auth::id(),
            ]);
        }

        return response()->json(['status' => 'success']);
    }
}