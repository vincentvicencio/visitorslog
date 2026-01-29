<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User_types; // Ensure this is correct
use Illuminate\Support\Facades\Auth;

class User_TypesController extends Controller
{
    public function addusertype(Request $request)
    {
        $request->validate([
            'user_type' => 'required|string|max:255|unique:user_types,name',
        ]);

        User_types::create([
            'name'       => $request->user_type,
            'created_by' => Auth::user()->name ?? 'System',
            'updated_by' => Auth::user()->name ?? 'System',
        ]);

        return response()->json(['status' => 'success']);
    }
    public function destroy($id) 
    {
        try {
            $role = User_types::findOrFail($id);
            $role->update([
                'deleted_at' => now(), 
                'deleted_by' => auth()->user()->first_name ?? 'System' 
            ]);
        } catch (\Exception $e) {
            return response()->json(['error' => 'Failed to delete role.'], 500);
        }
    }

public function edit($id)
{
    $role = User_types::findOrFail($id); 
    return response()->json($role);
}

public function update(Request $request, $id)
{
    $request->validate([
        'user_type' => 'required|string|max:255|unique:user_types,name,' . $id,
    ]);

    $role = User_types::findOrFail($id);
    $role->update([
        'name' => $request->user_type,
        'updated_by' => Auth::user()->name ?? 'System'
    ]);
}
}