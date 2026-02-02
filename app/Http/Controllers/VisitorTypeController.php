<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VisitorType;
use Illuminate\Validation\Rule;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;


class VisitorTypeController extends Controller
{
    // Show the form
    public function index()
    {
        return view('visitor_types.form');
    }

    public function list(Request $request){
     
        $keywords = strtolower($request->search);
        $limit    = $request->input('length');
        


        // $rawquery = VisitorType::withoutTrashed();
        // ->where(function($query) use ($keywords) {
        //                 $query->where('name', 'LIKE', "%$keywords%");
        //             });


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
       
            $newData[$i] = [
                'name'          => $d->name, // show emp_code in first column
                'created_by' => $d->getEmpName($d->created_by),
                'updated_by' => ($d->getEmpName($d->updated_by) ?? '-'),
                'created_at' => $d->created_at->format('F j, Y'). '<br>'. $d->created_at->format('l'),
                'action'            => '<button 
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
                                                    data-name="'.$d->name.'"
                                                    >
                                                    <i class="bi bi-pencil-square me-2"></i> Edit
                                                </button>

                                            </li>
                                            <li>
                                                <button 
                                                    type="button"
                                                    class="dropdown-item text-danger"
                                                    id="deleteBtn"
                                                    data-id="'.$d->id.'"
                                                    <i class="bi bi-trash me-2"></i> Delete
                                                </button>

                                            </li>
                                        </ul>' 
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


    // Save via AJAX
     public function save(Request $request)
    {
        $request->validate([
            'visitor_type' => [
                'required',
                'string',
                function ($attribute, $value, $fail) {
                    // Check if a visitor type with the same name exists and is not soft-deleted
                    $exists = VisitorType::whereRaw('LOWER(name) = ?', [strtolower($value)])
                                ->whereNull('deleted_at') // only consider non-deleted rows
                                ->exists();

                    if ($exists) {
                        $fail('Visitor Type already exists.');
                    }
                },
            ],
        ]);


        try {
            $id = new VisitorType();
            $id->name = ucfirst(strtolower($request->visitor_type)); // normalize case
            $id->created_by = Auth::id();
            $id->created_at = now();
            $id->save();

            return response()->json([
                'message' => 'Visitor Type successfully added'
            ], 200);

        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error saving Visitor Type: ' . $e->getMessage(),
            ]);
        }
    }
    public function editAjax(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:visitor_types,id',
            'visitor_type' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    $exists = VisitorType::whereRaw('LOWER(name) = ?', [strtolower($value)])
                        ->where('id', '!=', $request->id)  // exclude current record
                        ->whereNull('deleted_at')
                        ->exists();
                    if ($exists) {
                        $fail('Visitor Type already exists.');
                    }
                },
            ],
        ]);

        $visitor = VisitorType::find($request->id);

        $visitor->update([
            'name' => ucfirst(strtolower($request->visitor_type)),
            'updated_at' => now(),
            'updated_by' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Visitor type successfully updated'
        ], 200);
    }



    public function deleteAjax(Request $request)
    {
        $visitor = VisitorType::where('id', $request->id)->first();

        if (!$visitor) {
            return response()->json([
                'message' => 'Visitor Type not found'
            ], 404);
        }

        if ($visitor->deleted_at !== null) {
            return response()->json([
                'message' => 'Visitor type already deleted'
            ], 400);
        }

        $visitor->update([
            'deleted_at' => Carbon::now(),
            'deleted_by' => Auth::id(),
        ]);

        return response()->json([
            'message' => 'Visitor type successfully deleted'
        ]);
    }
}