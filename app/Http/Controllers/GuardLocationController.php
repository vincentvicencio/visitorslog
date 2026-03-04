<?php

namespace App\Http\Controllers;

use App\Models\Location;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class GuardLocationController extends Controller
{
    public function show()
    {
        if (!Auth::check() || (int) Auth::user()->user_type !== 3) {
            return redirect()->route('visitorslog');
        }

        if (session()->has('guard_location_id')) {
            return redirect()->route('visitorslog');
        }

        $locations = Location::orderBy('name', 'asc')->get(['id', 'name']);

        return view('components.triggers.location', compact('locations'));
    }

    public function store(Request $request)
    {
        if (!Auth::check() || (int) Auth::user()->user_type !== 3) {
            return redirect()->route('visitorslog');
        }

        $validated = $request->validate([
            'location_id' => ['required', 'integer', 'exists:locations,id'],
        ]);

        $location = Location::findOrFail($validated['location_id']);

        session([
            'guard_location_id' => (int) $location->location_id,
            'guard_location_name' => $location->name,
        ]);

        return redirect()->route('visitorslog');
    }
}
