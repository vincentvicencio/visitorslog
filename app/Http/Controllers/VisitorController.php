<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\VisitorType;
use App\Models\RegisteredID;
use Illuminate\Validation\Rule;
use Carbon\Carbon;


class VisitorController extends Controller
{
    public function index()
    {
        $visitors = VisitorType::where('deleted_at', null)
                   ->orderBy('id', 'asc')
                   ->get();
        return view('homepage.form', compact('visitors'));
    }

    public function list()
    {
        $visitors = Visitor::where('status', 1)
                   ->orderBy('id', 'asc')
                   ->get();

        return view('homepage.list', compact('visitors'));

    }

    // public function timeout(Request $request)
    // {
    //     $request->validate([
    //         'visitor_id' => 'required|exists:visitors,visitor_id',
    //     ]);

    //     $visitor = Visitor::where('visitor_id', $request->visitor_id)->latest('id')->first();

    //     if ($visitor) {
    //         $visitor->time_out = now();
    //         $visitor->status = 0; // timed out
    //         $visitor->save();

    //         // ✅ Set flash message
    //         return redirect()->back()->with('success', 'Visitor timed out successfully!');
    //     }

    //     return redirect()->back()->with('error', 'Visitor not found.');
    // }

    public function timeoutAjax(Request $request)
    {
        $visitor = Visitor::where('visitor_id', $request->visitor_id)->first();

        if (!$visitor) {
            return response()->json([
                'message' => 'Visitor not found'
            ], 404);
        }

        if ($visitor->status == 1) {
            return response()->json([
                'message' => 'Visitor already timed out'
            ], 400);
        }

        $visitor->update([
            'time_out' => Carbon::now(),
            'status'   => 1,
            'updated_by' => auth()->user()->name ?? 'Admin',
        ]);

        return response()->json([
            'message' => 'Visitor successfully timed out'
        ]);
    }

    // public function view(Request $request)
    // {
    //     $request->validate([
    //         'visitor_id' => 'required|exists:visitors,visitor_id',
    //     ]);

    //     // Get only one visitor with visitor_id and status = 1
    //     $visitor = Visitor::where('visitor_id', $request->visitor_id)
    //                     ->where('status', 1)
    //                     ->latest('id') // most recent if multiple
    //                     ->first();     // only one record

    //     if ($visitor) {
    //         return view('homepage.view', compact('visitor'));
    //     }

    //     // Optional: handle case where visitor not found
    //     return redirect()->back()->with('error', 'Visitor not found or inactive.');
    // }
    public function view(Request $request)
    {
        $request->validate([
            'visitor_id' => 'required|exists:visitors,visitor_id',
        ]);

        $visitor = Visitor::where('visitor_id', $request->visitor_id)
            ->where('status', 0)
            ->latest('id')
            ->first();

        if (!$visitor) {
            return response()->json([
                'message' => 'Visitor not found or inactive'
            ], 404);
        }

        return response()->json([
            'redirect' => route('visitor.view.page', $visitor->visitor_id)
        ]);
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
                    $timein = Visitor::where('visitor_id', $value)
                                ->whereNull('time_out')
                                ->first();
                    if ($timein) {
                        $fail('This Visitor ID is already checked in and has not timed out.');
                    }

                    if (!$existing) {
                        // ID does not exist in registered IDs
                        $fail('This Visitor ID is not registered.');
                    } elseif ($existing->visitor_type != $request->visitor_type) {
                        // ID exists but visitor type does not match
                        $fail('This Visitor ID is registered under a different visitor type.');
                    }
                },
            ],

            'image_path' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
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
            $visitor->created_by   = auth()->user()->name ?? 'Admin';
            $visitor->image_path   = $imagePath;
            $visitor->time_in      = now();
            $visitor->save();


            return response()->json([
                'message' => 'Visitor successfully added'
            ], 200);



        } catch (\Exception $e) {
            return response()->json([
                'message' => 'Error saving visitor: ' . $e->getMessage(),
            ], 500);
        }
    }
}

