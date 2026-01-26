<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegisteredID;
use App\Models\VisitorType;

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
        $visitorTypes = VisitorType::orderBy('id', 'asc')->get();

        // Pass to the view
        return view('registerid.list', compact('registeredIds', 'visitorTypes'));
    }

    // Save via AJAX
    public function save(Request $request)
    {
        // 1️⃣ VALIDATION
        $request->validate([
            // Check if visitor_id already exists
            'visitor_id'   => 'required|numeric|unique:registered_visitor_ids,id_number',

            // Check if visitor_type exists in visitor_types table (ID)
            'visitor_type' => 'required|exists:visitor_types,id',
        ]);

        // 2️⃣ SAVE DATA
        $registeredID = new RegisteredID();
        $registeredID->id_number = $request->visitor_id;

        // visitor_type IS ALREADY the ID from visitor_types table
        $registeredID->visitor_type = $request->visitor_type;

        $registeredID->created_at = now();
        $registeredID->save();

        // 3️⃣ RESPONSE
        return response()->json([
            'success' => true,
            'message' => 'Visitor ID registered successfully!',
        ]);
    }

    public function delete(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:registered_visitor_ids,id',
        ]);
            $visitor = RegisteredID::findOrFail($request->id);
        if ($visitor) {
            
            // $visitor = VisitorType::findOrFail($request->id);
            $visitor->deleted_at = now();
            $visitor->save();
            return redirect()->back()->with('success', 'Visitor ID deleted successfully!');
        }


        return redirect()->back()->with('error', 'Visitor ID not found.');
    }

}
