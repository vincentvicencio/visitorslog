<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegisteredID;
use App\Models\VisitorType;
use Carbon\Carbon;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Auth;

class RegisterIDController extends Controller
{
    // Show the form
    public function index()
    {
        $visitorTypes = VisitorType::where('deleted_at', null)
                   ->orderBy('id', 'asc')
                   ->get();
        return view('registerid.form', compact('visitorTypes'));
    }


    public function list()
    {
        // Get all registered IDs, latest first
        $registeredIds = RegisteredID::where('deleted_at', null)
                   ->orderBy('visitor_type', 'asc')
                   ->get();
        $visitorTypes = VisitorType::where('deleted_at', null)
                   ->orderBy('id', 'asc')
                   ->get();

        // Pass to the view
        return view('registerid.list', compact('registeredIds', 'visitorTypes'));
    }

    // Save via AJAX
    public function save(Request $request)
    {
        // 1️⃣ VALIDATION
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
        ]);



        // 2️⃣ SAVE DATA
        $registeredID = new RegisteredID();
        $registeredID->id_number = $request->id_number;
        // visitor_type IS ALREADY the ID from visitor_types table
        $registeredID->visitor_type = $request->visitor_type;
        $registeredID->created_by = Auth::id();
        // Auth::user()->first_name . ' ' .Auth::user()->last_name ?? 'System';
        $registeredID->created_at = now();
        $registeredID->save();

        // 3️⃣ RESPONSE
        if (!$registeredID) {
            return response()->json([
                'message' => 'Visitor Id not found'
            ], 404);
        }

        return response()->json([
            'message' => 'Visitor Id successfully registered'
        ]);
    }

    public function editAjax(Request $request)
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



    public function deleteAjax(Request $request)
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
