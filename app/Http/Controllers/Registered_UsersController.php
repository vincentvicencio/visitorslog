<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegisteredUser;
use App\Models\User_types;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;

class Registered_UsersController extends Controller
{
 public function addusers(Request $request)
{
    // 1. Validation
    $request->validate([
        'user_type' => 'required',
        'emp_code'  => 'required|string|unique:registered_users,user_name',
        'password'  => 'required|string|min:6',
        'locations' => 'required',
    ]);

    $empCode = $request->input('emp_code');
    $selectedLocationId = $request->input('locations');

    // 2. SEARCH logic (Session)
    $employees = collect(session('all_emp'));
    $employeeData = $employees->firstWhere('emp_code', $empCode);

    if (!$employeeData) {
        return response()->json([
            'status' => 'error', 
            'message' => 'Employee Code not found in session records.'
        ], 422);
    }

    // 3. TRY/CATCH logic
    try {
        $apiUrl = "http://192.168.200.185:1924/api/employee_details/" . $empCode;
        // Set a timeout so it doesn't hang forever
        $response = Http::timeout(5)->get($apiUrl);

        // DATA PREPARATION: Use session data as default
        $firstName = $employeeData['first_name'] ?? 'N/A';
        $lastName  = $employeeData['last_name'] ?? 'N/A';
        $location = $selectedLocationId ?? 'N/A';

        if ($response->successful()) {
            $apiData = $response->json();
            $firstName = $apiData['FirstName'] ?? $firstName;
            $lastName  = $apiData['LastName'] ?? $lastName;
            $location = $selectedLocationId ?? 'N/A';
        } 
        
        RegisteredUser::create([
            'user_name'  => $empCode,
            'first_name' => $firstName, 
            'last_name'  => $lastName,
            'location'   => $employeeData['location'] ?? 'N/A',
            'password'   => Hash::make($request->password),     
            'user_type'  => $request->user_type,
            'location'   => $location,
            'created_by' => Auth::user()->first_name ?? 'System', 
            'updated_by' => Auth::user()->first_name ?? 'System',
        ]);

        return response()->json([
            'status' => 'success', 
            'message' => 'User registered successfully!'
            
        ]);

    } catch (\Exception $e) {
        // This prevents the "undefined" error by returning a JSON message
        return response()->json([
            'status' => 'error', 
            'message' => 'System Error: ' . $e->getMessage()
        ], 500);
    }
}

// --- NEW: DELETE METHOD ---
    public function deleteUser($id)
{
    try {
        $user = RegisteredUser::findOrFail($id);

        // Instead of $user->delete(), we update the column
        $user->update([
            'deleted_at' => NOW(),
            'deleted_by' => Auth::user()->first_name ?? 'System' // Optional: track who deleted it
        ]);

        return response()->json([
            'status' => 'success', 
            'message' => 'User deactivated successfully.'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status' => 'error', 
            'message' => 'Failed to remove user.'
        ], 500);
    }
}

public function getUserTypes()
{
    // Assuming your model is named User_types based on your use statements
    return response()->json(\App\Models\User_types::all());
}

    // --- NEW: GET USER DATA (For Edit Modal) ---
    public function getUser($id)
    {
        $user = RegisteredUser::findOrFail($id);
        return response()->json($user);
    }
public function updateUser(Request $request, $id) 
{
    $request->validate([
        'user_type' => 'required',
        'emp_code'  => 'required' // Ensuring the code is present in the request
    ]);

    try {
        // Find the user we are currently editing
        $user = RegisteredUser::findOrFail($id);
        
        if ($request->emp_code !== $user->user_name) {
            return response()->json([
                'status' => 'error', 
                'message' => 'The Entered Employee Code Does not Match'
            ], 422);
        }

        $updateData = [
            'user_type'  => $request->user_type,
            'user_name'  => $request->emp_code, // Update the code if it changed
            'updated_by' => Auth::user()->first_name ?? 'System',
        ];

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return response()->json(['status' => 'success', 'message' => 'User updated successfully!']);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => 'Update failed: ' . $e->getMessage()], 500);
    }
}

    public function location()
{
    $location = collect(session('all_location'));
    $data = [];
    
    // Placeholder
    $data[] = ['id' => '', 'text' => 'Choose Location/Site'];

    foreach ($location as $record) {
        $data[] = [
            'id'   => $record['id'], // Ensure 'id' exists in your session array
            'text' => $record['name']
        ];
    }

    return response()->json($data);
}
}