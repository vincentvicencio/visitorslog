<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\VisitorType;
use App\Models\RegisteredID;
use Illuminate\Validation\Rule;


class VisitorController extends Controller
{
    public function index()
    {
        $visitors = VisitorType::orderBy('id', 'asc')->get();
        return view('homepage.form', compact('visitors'));
    }

    public function list()
    {
        $visitors = Visitor::orderBy('id', 'asc')->get();
        return view('homepage.list', compact('visitors'));
    }

    public function save(Request $request)
    {
        $request->validate([
            'first_name'   => 'required|string',
            'middle_name'  => 'nullable|string',
            'last_name'    => 'required|string',
            'visitor_type' => 'required|exists:visitor_types,id',

            'visitor_id' => [
                'required',
                'string',
                function ($attribute, $value, $fail) use ($request) {
                    // Check if visitor ID exists in the registered IDs table
                    $existing = RegisteredID::where('id_number', $value)->first();

                    if (!$existing) {
                        // ID does not exist in registered IDs
                        $fail('This Visitor ID is not registered.');
                    } elseif ($existing->visitor_type != $request->visitor_type) {
                        // ID exists but visitor type does not match
                        $fail('This Visitor ID is registered under a different visitor type.');
                    }
                },
                Rule::unique('visitors', 'visitor_id')
                    ->where(function ($query) use ($request) {
                        return $query->where('visitor_id', $request->visitor_id);
                    }),
            ],

            'image_path' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ], [
            'visitor_id.unique' => 'Visitor ID is already registered.',
        ]);



        try {
            // Handle image upload
            $imagePath = null;
            if ($request->hasFile('image_path')) {
                $imagePath = $request->file('image_path')->store('visitors', 'public');
            }

            // Save visitor
            $visitor = new Visitor();
            $visitor->first_name   = $request->first_name;
            $visitor->middle_name  = $request->middle_name;
            $visitor->last_name    = $request->last_name;
            $visitor->phone_number = $request->phone_number;
            $visitor->visitor_type = $request->visitor_type;
            $visitor->visitor_id   = $request->visitor_id;
            $visitor->location     = $request->location;
            $visitor->image_path   = $imagePath;
            $visitor->time_in      = now();
            $visitor->time_out     = null;
            $visitor->status       = null;
            $visitor->save();


            return response()->json([
                'success' => true,
                'message' => 'Visitor information saved successfully!',
            ]);

        } catch (\Exception $e) {
            return response()->json([
                'success' => false,
                'message' => 'Error saving visitor: ' . $e->getMessage(),
            ], 500);
        }
    }
}

