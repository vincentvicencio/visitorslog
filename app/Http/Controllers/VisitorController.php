<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;

class VisitorController extends Controller
{
    public function store(Request $request)
    {
        // Validation
        $request->validate([
            'first_name' => 'required',
            'last_name' => 'required',
            'image_path' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
        ]);

        // Handle image upload
        $imagePath = null;
        if ($request->hasFile('image_path')) {
            $imagePath = $request->file('image_path')->store('visitors', 'public');
        }

        if($request->visitor_type == "Applicant"){
            $request->visitor_type = 1;
        }else if($request->visitor_type == "OJT"){
            $request->visitor_type = 2;
        }else if($request->visitor_type == "Trainee"){
            $request->visitor_type = 3;
        }

        // Save to database
        Visitor::create([
            'first_name'   => $request->first_name,
            'middle_name'  => $request->middle_name,
            'last_name'    => $request->last_name,
            'phone_number' => $request->phone_number,
            'visitor_type' => $request->visitor_type,
            'visitor_id'   => $request->visitor_id,
            'location'     => $request->location,
            'image_path'   => $imagePath,
            'time_in'      => now(),
            'time_out'     => now(),
            'status'       => 1,
            'created_by'   => "",
            'updated_by'   => "",
            'deleted_by'   => "",
            'created_at'   => now(),
            'updated_at'   => now(),
            'deleted_at'   => now(),
        ]);

        return redirect()->back()->with('success', 'Visitor added successfully!');
    }
    public function index()
    {
        // Your logic here
         return view('homepage.form');
    }
}
