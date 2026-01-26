<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\VisitorType;
use Illuminate\Validation\Rule;
use Carbon\Carbon;


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
        $visitorTypes = VisitorType::where('deleted_at', null)
                   ->orderBy('id', 'asc')
                   ->get();

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
            $id->created_by = auth()->user()->name ?? 'Admin';
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

    // public function delete(Request $request)
    // {
    //     $request->validate([
    //         'id' => 'required|exists:visitor_types,id',
    //     ]);
    //         $visitor = VisitorType::findOrFail($request->id);
    //     if ($visitor) {
            
    //         // $visitor = VisitorType::findOrFail($request->id);
    //         $visitor->deleted_at = now();
    //         $visitor->save();
    //         return redirect()->back()->with('success', 'Visitor Type deleted successfully!');
    //     }


    //     return redirect()->back()->with('error', 'Visitor Type not found.');
    // }

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
            'deleted_by' => auth()->user()->name ?? 'Admin',
        ]);

        return response()->json([
            'message' => 'Visitor type successfully deleted'
        ]);
    }
}