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
            'updated_by' => auth()->user()->name ?? 'Admin',
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
            'deleted_by' => auth()->user()->name ?? 'Admin',
        ]);

        return response()->json([
            'message' => 'Visitor type successfully deleted'
        ]);
    }
}