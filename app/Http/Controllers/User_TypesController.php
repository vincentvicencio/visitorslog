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

    // public function edit($id) {
    //     $role = User_types::findOrFail($id);
    //     return response()->json($role);
    // }

    // public function update(Request $request, $id) {
    //     // Validation: Ignore current ID for unique check
    //     $request->validate([
    //         'user_type' => 'required|string|max:255|unique:user_types,name,' . $id
    //     ]);
        
    //     $role = User_types::findOrFail($id);
    //     $role->update([
    //         'name' => $request->user_type,
    //         'updated_by' => Auth::user()->name ?? 'System'
    //     ]);

    //     return response()->json(['success' => 'Role updated successfully!']);
    // }

    public function destroy($id) {
        $role = User_types::findOrFail($id);
        $role->delete();
        return response()->json(['success' => 'Role deleted successfully!']);
    }

    // 1. Return the data to the AJAX 'get' request
// 1. Fetch data for the modal
public function edit($id)
{
    // Changed 'Role' to 'User_types'
    $role = User_types::findOrFail($id); 
    return response()->json($role);
}

// 2. Process the Update
public function update(Request $request, $id)
{
    $request->validate([
        // Changed 'roles' table to 'user_types'
        'user_type' => 'required|string|max:255|unique:user_types,name,' . $id,
    ]);

    $role = User_types::findOrFail($id);
    $role->update([
        'name' => $request->user_type,
        'updated_by' => Auth::user()->name ?? 'System'
    ]);

    return response()->json(['success' => 'Role updated successfully!']);
}
}