<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegisteredUser;
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
    ]);

    $empCode = $request->input('emp_code');

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

        // If API is successful, update names from API
        if ($response->successful()) {
            $apiData = $response->json();
            $firstName = $apiData['FirstName'] ?? $firstName;
            $lastName  = $apiData['LastName'] ?? $lastName;
        } 
        // NOTE: Even if the API fails, we continue because we have the session data!

        RegisteredUser::create([
            'user_name'  => $empCode,
            'first_name' => $firstName, 
            'last_name'  => $lastName,
            'location'   => $employeeData['location'] ?? 'N/A',
            'password'   => Hash::make($request->password),
            'user_type'  => $request->user_type,
            'created_by' => Auth::id() ?? 1,
            'updated_by' => Auth::id() ?? 1,
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
            $user->delete();
            return response()->json(['status' => 'success', 'message' => 'User deleted successfully.']);
        } catch (\Exception $e) {
            return response()->json(['status' => 'error', 'message' => 'Failed to delete user.'], 500);
        }
    }

    // --- NEW: GET USER DATA (For Edit Modal) ---
    public function getUser($id)
    {
        $user = RegisteredUser::findOrFail($id);
        return response()->json($user);
    }

    // --- NEW: UPDATE METHOD ---
    // public function updateUser(Request $request)
    // {
    //     $request->validate([
    //         'id' => 'required|exists:registered_users,id',
    //         'user_type' => 'required'
    //     ]);

    //     try {
    //         $user = RegisteredUser::findOrFail($request->id);
            
    //         $updateData = [
    //             'user_type'  => $request->user_type,
    //             'updated_by' => Auth::id() ?? 1,
    //         ];

    //         // Only update password if a new one is provided
    //         if ($request->filled('password')) {
    //             $updateData['password'] = Hash::make($request->password);
    //         }

    //         $user->update($updateData);

    //         return response()->json(['status' => 'success', 'message' => 'User updated successfully!']);
    //     } catch (\Exception $e) {
    //         return response()->json(['status' => 'error', 'message' => 'Update failed: ' . $e->getMessage()], 500);
    //     }
    // }
    // Registered_UsersController.php

public function updateUser(Request $request, $id) // Accept $id here
{
    // Validation: We no longer need 'id' in the body since it's in the URL
    $request->validate([
        'user_type' => 'required'
    ]);

    try {
        // Use the $id from the URL parameter
        $user = RegisteredUser::findOrFail($id);
        
        $updateData = [
            'user_type'  => $request->user_type,
            'updated_by' => Auth::id() ?? 1,
        ];

        // Update employee code if you allowed it in the form
        if ($request->filled('emp_code')) {
            $updateData['user_name'] = $request->emp_code;
        }

        if ($request->filled('password')) {
            $updateData['password'] = Hash::make($request->password);
        }

        $user->update($updateData);

        return response()->json(['status' => 'success', 'message' => 'User updated successfully!']);
    } catch (\Exception $e) {
        return response()->json(['status' => 'error', 'message' => 'Update failed: ' . $e->getMessage()], 500);
    }
}
}