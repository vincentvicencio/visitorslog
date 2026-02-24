<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegisteredUser;
use App\Models\User_types;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;

class Registered_UsersController extends Controller
{
    public function index()
    {
         return view('pages.registerUser.users');
    }
 public function save(Request $request)
{
    // Get the user type to check role
    $userType = User_types::find($request->user_type);
    $roleId = $userType ? $userType->id : null;
    $roleName = $userType ? strtolower($userType->name) : '';
    // Use roleId for role checks
    $isAdmin = $roleId === 1;
    $isReceptionist = $roleId === 2;
    $isGuard = $roleId === 3;
    $isAdminOrReceptionist = $isAdmin || $isReceptionist;
    
    // 1. Validation - different rules based on role
    $validationRules = [
        'user_type' => 'required',
        'locations' => 'required',
    ];
    
    // Check if editing (record_id present)
    $recordId = $request->input('record_id');
    $isEditing = !empty($recordId);
    
    if ($isGuard) {
        // Guard requires first_name, last_name, password (only required when creating)
        $validationRules['first_name'] = ['required', 'string', 'max:40','regex:/^[a-zA-Z\s]+$/'];
        $validationRules['last_name'] = ['required', 'string', 'max:40','regex:/^[a-zA-Z\s]+$/'];
        if (!$isEditing) {
            $validationRules['password'] = 'required|string|min:6';
        }
    } else {
        // Skip duplicate check when editing (record_id is present)
        $validationRules['emp_code'] = [
            'required',
            'string',
            function ($attribute, $value, $fail) use ($recordId) {
                $query = RegisteredUser::where('user_name', $value)
                    ->whereNull('deleted_at');
                // Exclude current record when editing (cast to int for comparison)
                if (!empty($recordId)) {
                    $query->where('id', '!=', (int) $recordId);
                }
                if ($query->exists()) {
                    $fail('Employee Code already exists.');
                }
            }
        ];
    }
    
    $messages = [
        'user_type.required' => 'User type is required.',
        'locations.required' => 'Location is required.',
        'first_name.required' => 'First name is required.',
        'first_name.regex' => 'First name must contain letters only.',
        'last_name.regex' => 'Last name must contain letters only.',
        'last_name.required' => 'Last name is required.',
        'password.required' => 'Password is required.',
        'password.min' => 'Password must be at least 6 characters.',
        'emp_code.required' => 'Employee code is required.',
        'emp_code.unique' => 'Employee code already exists.',
    ];

    $validator = Validator::make($request->all(), $validationRules, $messages);

    if ($validator->fails()) {
        return response()->json([
            'status' => 1,
            'errors' => $validator->errors(),
        ]);
    }

    $locationsInput = $request->input('locations');
    
    // Handle locations - can be a single value or JSON string of array
    $locations = [];
    if (is_array($locationsInput)) {
        $locations = $locationsInput;
    } elseif (is_string($locationsInput)) {
        // Try to decode if it's JSON
        $decoded = json_decode($locationsInput, true);
        $locations = is_array($decoded) ? $decoded : [$locationsInput];
    } else {
        $locations = [$locationsInput];
    }

    $locations = array_values(array_filter($locations, function ($value) {
        return $value !== null && $value !== '';
    }));

    if (count($locations) === 0) {
        return response()->json([
            'status' => 1,
            'title' => 'Select Location',
            'message' => 'Please select at least one location.'
        ], 422);
    }

    if ($roleId === 2 && count($locations) > 1) { // Receptionist
        return response()->json([
            'status' => 1,
            'title' => 'Select Location',
            'message' => 'Receptionist can only have one location.'
        ], 422);
    }

    try {
        if ($isGuard) {
            // Guard: Use provided first_name and last_name, generate username
            $firstName = $request->input('first_name');
            $lastName = $request->input('last_name');
            $baseUsername = strtolower(preg_replace('/\s+/', '', $firstName)) . '.' . strtolower(preg_replace('/\s+/', '', $lastName));
            $username = $baseUsername;
            $counter = 2;
            
            // Check if username already exists and make it unique (exclude current record when editing)
            $usernameQuery = RegisteredUser::where('user_name', $username);
            if ($isEditing) {
                $usernameQuery->where('id', '!=', $recordId);
            }
            while ($usernameQuery->exists()) {
                $username = $baseUsername . '.' . $counter;
                $counter++;
                $usernameQuery = RegisteredUser::where('user_name', $username);
                if ($isEditing) {
                    $usernameQuery->where('id', '!=', $recordId);
                }
            }
            
            $guardData = [
                'user_name'  => $username,
                'first_name' => $firstName, 
                'last_name'  => $lastName,
                'location'   => $locations,
                'user_type'  => $request->user_type,
                'updated_by' => Auth::user()->id,
            ];
            
            // Only hash password if provided
            if ($request->filled('password')) {
                $guardData['password'] = Hash::make($request->password);
            }
            
            if ($isEditing) {
                $user = RegisteredUser::findOrFail($recordId);
                $user->update($guardData);
                $message = 'Guard updated successfully!';
            } else {
                $guardData['password'] = Hash::make($request->password);
                $guardData['created_by'] = Auth::user()->id;
                RegisteredUser::create($guardData);
                $message = 'Guard registered successfully!';
            }
            
            return response()->json([
                'status' => 0,
                'message' => $message
            ]);
        }
        
        // For Admin/Receptionist and other roles with emp_code
        $empCode = $request->input('emp_code');
        
        // SEARCH logic (Session)
        $employees = collect(session('all_emp'));
        $employeeData = $employees->firstWhere('emp_code', $empCode);

        // When editing, use existing DB data if session lookup fails
        if ($isEditing) {
            $existingUser = RegisteredUser::findOrFail($recordId);
            $firstName = $existingUser->first_name;
            $lastName  = $existingUser->last_name;
        } else {
            // For new users, require session data
            if (!$employeeData) {
                return response()->json([
                    'status' => 1,
                    'message' => 'Employee Code not found in session records.'
                ], 422);
            }
            $firstName = $employeeData['first_name'] ?? 'N/A';
            $lastName  = $employeeData['last_name'] ?? 'N/A';
        }

        // Try to get updated name from API (optional)
        $apiUrl = "http://192.168.200.185:1924/api/employee_details/" . $empCode;
        $response = Http::timeout(5)->get($apiUrl);
        
        if ($response  ->successful()) {
            $apiData   = $response->json();
            $firstName = $apiData['FirstName'] ?? $firstName;
            $lastName  = $apiData['LastName'] ?? $lastName;
        }

        $userData = [
            'user_name'  => $empCode,
            'first_name' => $firstName, 
            'last_name'  => $lastName,
            'location'   => $locations,
            'user_type'  => $request->user_type,
            'updated_by' => Auth::id(),
        ];
        
        // Only hash password if provided
        if ($request->filled('password')) {
            $userData['password'] = Hash::make($request->password);
        }
        
        if ($isEditing) {
            $user = RegisteredUser::findOrFail($recordId);
            $user->update($userData);
            $message = 'User updated successfully!';
        } else {
            $userData['password'] = Hash::make($request->password);
            $userData['created_by'] = Auth::id();
            RegisteredUser::create($userData);
            $message = 'User registered successfully!';
        }

        return response()->json([
            'status' => 0,
            'message' => $message
        ]);

    } catch (\Exception $e) {
        return response()->json([
            'status' => 1,
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
            'deleted_by' => Auth::user()->id // Optional: track who deleted it
        ]);

        return response()->json([
            'status'  => 'success', 
            'message' => 'User deactivated successfully.'
        ]);
    } catch (\Exception $e) {
        return response()->json([
            'status'  => 'error', 
            'message' => 'Failed to remove user.'
        ], 500);
    }
}

    public function getUserTypes()
    {
        $user_type = User_types::get(['id', 'name']);
        $data = [];
    
        // Placeholder
        $data[] = ['id' => '', 'text' => 'Choose User Type'];

        foreach ($user_type as $record) {
            $data[] = [
                'id'   => $record['id'], 
                'text' => $record['name']
            ];
        }
        return response()->json($data);
    }


    public function getUser($id)
{
    // 1. Find the registered user in the database
    $user = RegisteredUser::findOrFail($id);

    // 2. Fetch all employees and locations from session
    $allEmployees = collect(session('all_emp'));
    $allLocations = collect(session('all_location'));

    // 3. Find this specific employee in the session data by their code
    $sessionEmployee = $allEmployees->firstWhere('emp_code', $user->user_name);

    // 4. Get the Location Names
    // If $user->location is an array of IDs, map them to location objects
    $locationIds = is_array($user->location) ? $user->location : (is_string($user->location) ? [$user->location] : []);
    $locationNames = [];
    
    foreach ($locationIds as $locId) {
        $locData = $allLocations->firstWhere('id', $locId);
        if ($locData) {
            $locationNames[] = $locData['name'];
        }
    }
    
    // Get the user type name to determine if it's Admin/Receptionist
    $userType = User_types::find($user->user_type);
    $roleName = $userType ? $userType->name : '';

    return response()->json([
        'id'            => $user->id,
        'emp_code'      => $user->user_name,
        'first_name'    => $user->first_name,
        'last_name'     => $user->last_name,
        'role_id'       => $user->user_type,
        'role_name'     => $roleName,
        'location_id'   => $locationIds, // Return as array of IDs for multi-select
        'location_names'=> $locationNames // Display names
    ]);
}
public function edit(Request $request, $id) 
{
    try {
        // Find the user we are currently editing
        $user = RegisteredUser::findOrFail($id);
        
        // Get the user type to check role
        $userType = User_types::find($request->user_type);
        $roleId = $userType ? $userType->id : null;
        $roleName = $userType ? strtolower($userType->name) : '';
        // Use roleId for role checks
        $isAdmin = $roleId === 1;
        $isReceptionist = $roleId === 2;
        $isGuard = $roleId === 3;
        $isAdminOrReceptionist = $isAdmin || $isReceptionist;
        
        // Validation based on role
        $validationRules = ['user_type' => 'required'];
        
        if ($isGuard) {
            $validationRules['first_name'] = 'required|string';
            $validationRules['last_name'] = 'required|string';
        } else {
            $validationRules['emp_code'] = 'required';
        }
        
        $request->validate($validationRules);
        
        // For non-Guard roles, verify emp_code matches
        if (!$isGuard && $request->emp_code !== $user->user_name) {
            return response()->json([
                'status' => 'error', 
                'message' => 'The Entered Employee Code Does not Match'
            ], 422);
        }

        $updateData = [
            'user_type'  => $request->user_type,
            'updated_by' => Auth::user()->id,
        ];
        
        // For Guard, update first_name and last_name
        if ($isGuard) {
            $updateData['first_name'] = $request->input('first_name');
            $updateData['last_name'] = $request->input('last_name');
            
            $baseUsername = strtolower(preg_replace('/\s+/', '', $updateData['first_name'])) . '.' . strtolower(preg_replace('/\s+/', '', $updateData['last_name']));
            $username = $baseUsername;
            $counter  = 2;
            
            // Ensure username is unique, ignoring current user
            while (RegisteredUser::where('user_name', $username)->where('id', '!=', $user->id)->exists()) {
                $username = $baseUsername . '.' . $counter;
                $counter++;
            }
            
            $updateData['user_name'] = $username;
        } else {
            $updateData['user_name'] = $request->emp_code;
        }

        // Handle locations if provided
        if ($request->has('locations')) {
            $locationsInput = $request->input('locations');
            
            // Handle locations - can be a single value or JSON string of array
            $locations = [];
            if (is_array($locationsInput)) {
                $locations = $locationsInput;
            } elseif (is_string($locationsInput)) {
                // Try to decode if it's JSON
                $decoded = json_decode($locationsInput, true);
                $locations = is_array($decoded) ? $decoded : [$locationsInput];
            } else {
                $locations = [$locationsInput];
            }

            $locations = array_values(array_filter($locations, function ($value) {
                return $value !== null && $value !== '';
            }));

            if (count($locations) === 0) {
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Please select at least one location.'
                ], 422);
            }

            if ($roleId === 2 && count($locations) > 1) { // Receptionist
                return response()->json([
                    'status'  => 'error',
                    'message' => 'Receptionist can only have one location.'
                ], 422);
            }
            
            $updateData['location'] = $locations;
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

    public function location()
{
    $location = collect(session('all_location'));
    $data = [];
    
    // Placeholder
    $data[] = ['id' => '', 'text' => 'Choose Location/Site'];
        foreach ($location as $record) {
        $data[] = [
            'id'    => $record['id'], // Ensure 'id' exists in your session array
            'text'  => $record['name']
        ];
    }

    return response()->json($data);
}

    public function searchEmployees(Request $request)
    {
        $search    = strtolower($request->input('q', ''));
        $employees = collect(session('all_emp', []));
        
        // Filter employees based on search term
        $filtered = $employees->filter(function ($emp) use ($search) {
            if (empty($search)) return true;
            
            $empCode   = strtolower($emp['emp_code'] ?? '');
            $firstName = strtolower($emp['first_name'] ?? '');
            $lastName  = strtolower($emp['last_name'] ?? '');
            
            return str_contains($empCode, $search) || 
                   str_contains($firstName, $search) || 
                   str_contains($lastName, $search);
        })->take(20); // Limit results
        
        $results = $filtered->map(function ($emp) {
            return [
                'id'         => $emp['emp_code'],
                'text'       => $emp['emp_code'] . ' - ' . ($emp['first_name'] ?? '') . ' ' . ($emp['last_name'] ?? ''),
                'first_name' => $emp['first_name'] ?? '',
                'last_name'  => $emp['last_name'] ?? ''
            ];
        })->values()->toArray();
        
        return response()->json(['results' => $results]);
    }


public function list(Request $request){
     
        $keywords = strtolower($request->search);
        $limit    = $request->input('length');

        $rawquery = RegisteredUser::with('userType')
                    ->whereNull('deleted_at')
                    ->when($keywords, function ($query) use ($keywords) {
                        $query->where('user_name', 'LIKE', "%{$keywords}%")
                              ->orWhere('first_name', 'LIKE', "%{$keywords}%")
                              ->orWhere('last_name', 'LIKE', "%{$keywords}%")
                              ->orWhere('first_name', 'LIKE', "%{$keywords}%")
                              ->orWhere('last_name', 'LIKE', "%{$keywords}%")
                            ->orWhereHas('userType', function ($q) use ($keywords) {
                                $q->where('name', 'LIKE', "%{$keywords}%");
                            });
                            
                            
                    });
        $totalRecords = $rawquery->get()->count();
        
        if ($request->input('draw') > 1) { 
            $start         = $request->input('start'); 
            $column        = $request->input('order.0.column');
            $direction     = $request->input('order.0.dir');
            $order         = $request->input('columns')[$column]['data']; 
            $temp          = $rawquery->get(); 
            $rawQuery      = $limit > 0 ? $rawquery->skip($start)->take($limit) : $rawquery; 
            $data          = $rawquery->orderby("updated_at", "desc")->take($limit)->get();
            $totalFiltered = count($temp);
       
        } else { 
       
            $data          = $rawquery->orderby("updated_at", "desc")->take($limit)->get();
     
            $totalFiltered = $totalRecords;
        }
 
        $newData = [];
        $i       = 0;
      
        foreach ($data as $d) { 
       
            $newData[$i] = [
                'user_name'  => $d->first_name . ' ' . $d->last_name, // show emp_code in first column
                'user_type'  => $d->userType->name ?? '-',
                'created_by' => $d->created_by ? user_name($d->created_by) : '-',
                'updated_by' => $d->updated_by ? user_name($d->updated_by) : '-',
                'created_at' => $d->created_at ? ($d->created_at->format('F j, Y'). '<br>'. $d->created_at->format('l')) : '-',
                'updated_at' => $d->updated_at ? ($d->updated_at->format('F j, Y'). '<br>'. $d->updated_at->format('l')) : '-',
                'action'            => '<div class="dropdown text-center">
                                        
                                        <button class="dropdown-item btn-edit" data-id="'. $d->id .'"> Edit</button>
                                        <button class="text-danger dropdown-item btn-delete" data-id="'. $d->id .'" data-details="'. $d->first_name. '"> Delete</button>
                                    </div>' 
            ];
            $i++;
        } 
 
        return response()->json([
            'draw'              => intval($request->input('draw')),
            'recordsTotal'      => $totalRecords,
            'recordsFiltered'   => $totalFiltered,
            'data'              => $newData            
        ]);
    }



    public function search(Request $request){
        $record = RegisteredUser::find($request->id);
        if(!$record){
            return response()->json([
                'status'     => 1,
                'message'    => 'No Data Found'
            ]);
        }

        return response()->json([
            'status'     => 0,
            'data'       => $record
        ]);
    }

    public function delete(Request $request){
        $record  = RegisteredUser::find($request->id);
        $details = $record->emp_code;
        $record->update(['deleted_by' => Auth::user()->id]);
        $record->delete();

        $message    = 'Registered User Successfully Deleted';
            return response()->json([
                'status'     => 0,
                'message'    => $message
            ]);
    }

    public function destroy($id) 
    {
        try {
            $role = RegisteredUser::findOrFail($id);
            $role->update(['deleted_by' => Auth::user()->id]);
            $role->delete();
            
        } catch (\Exception $e) {
            return response()->json(['message' => 'An error occurred while deleting the Registered User.'], 500);
        }
    }   

    public function update(Request $request, $id)
    {
        $request->validate([
            'RegisteredID' => 'required|string|max:255|unique:registered_users,name,' . $id,
        ]);

        $role = RegisteredUser::findOrFail($id);
        $role->update([
            'name'        => $request->RegisteredID,
            'updated_by'  => Auth::user()->id,
        ]);
    }
}