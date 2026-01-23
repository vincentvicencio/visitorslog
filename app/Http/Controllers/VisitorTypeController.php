<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VisitorType;
use Illuminate\Validation\Rule;


class VisitorTypeController extends Controller
{
    // Show the form
    public function index()
    {
        return view('visitor_types.form');
    }

    public function list()
    {
        // Get all registered IDs, latest first
        $visitorTypes = VisitorType::orderBy('id', 'asc')->get();

        // Pass to the view
        return view('visitor_types.list', compact('visitorTypes'));
    }


    // Save via AJAX
     public function save(Request $request)
    {
        $request->validate([
            'visitor_type' => [
                'required',
                'string',
                Rule::unique('visitor_types', 'name')
                    ->where(function ($query) use ($request) {
                        return $query->whereRaw('LOWER(name) = ?', [
                            strtolower($request->visitor_type)
                        ]);
                    }),
            ],
        ], [
            'visitor_type.unique' => 'Visitor Type already exists.',
        ]);

        try {
            $id = new VisitorType();
            $id->name = ucfirst(strtolower($request->visitor_type)); // normalize case
            $id->created_by = null;
            $id->updated_by = null;
            $id->deleted_by = null;
            $id->created_at = now();
            $id->updated_at = now();
            $id->deleted_at = null;
            $id->save();

            return response()->json([
                'success' => true,
                'message' => 'Visitor Type added successfully!',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving Visitor Type: ' . $e->getMessage(),
            ]);
        }
    }

    // public function save(Request $request)
    // {
    //     // Validate input
    //     $request->validate([
    //         'visitor_type' => 'required|string',
    //     ]);

    //     try {
    //         $id = new VisitorType();
    //         $id->name = $request->visitor_type;
    //         $id->created_by = null;
    //         $id->updated_by = null;
    //         $id->deleted_by = null;
    //         $id->created_at = now();
    //         $id->updated_at = now();
    //         $id->deleted_at = null;
    //         $id->save();

    //         return response()->json([
    //             'success' => true,
    //             'message' => 'Visitor Type added successfully!',
    //         ]);

    //     } catch (\Exception $e) {
    //         return response()->json([
    //             'success' => false,
    //             'message' => 'Error saving Visitor Type: ' . $e->getMessage(),
    //         ]);
    //     }
    // }
}
