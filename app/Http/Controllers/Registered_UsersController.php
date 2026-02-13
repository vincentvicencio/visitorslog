<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\RegisteredUser;
use App\Models\User_types;
use App\Models\VisitorType;
use App\Models\Visitor;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Illuminate\Validation\Rule;
use Illuminate\Support\Facades\Session;

class Registered_UsersController extends Controller
{
    public function index(Request $request)
    {
        $registeredUsers = RegisteredUser::all();
        $roles = User_types::all();
        $visitorTypes = VisitorType::all();

        // $visitorlogs = Visitor::with('visitor_type')->get();
        $visitorlogs = Visitor::where('status', 0)
                   ->orderBy('id', 'asc')
                   ->get();
        $search = $request->input('search');

        $empMap = collect(session('all_emp'))->keyBy('emp_code');
        
        $registeredUsers = RegisteredUser::with('userType')
        ->whereNull('deleted_at')
        ->when($search, function ($query, $search) {
            $query->where('user_name', 'like', "%{$search}%")
                ->orWhere('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%");
        })
        ->get();

        $allEmployeesFromSession = session('all_emp', []);
        return view('pages.registerUser.users', compact('roles', 'registeredUsers', 'allEmployeesFromSession', 'visitorlogs', 'visitorTypes','empMap'));
    }
 public function addusers(Request $request)
{
    // Get the user type to check role
    $userType = User_types::find($request->user_type);
    $roleName = $userType ? strtolower($userType->name) : '';
    $isAdminOrReceptionist = in_array($roleName, ['admin', 'receptionist']);
    $isGuard = $roleName === 'guard';
    
    // 1. Validation - different rules based on role
    $validationRules = [
        'user_type' => 'required',
        'locations' => 'required',
    ];
    
    if ($isGuard) {
        // Guard requires first_name, last_name, password - no emp_code
        $validationRules['first_name'] = 'required|string';
        $validationRules['last_name'] = 'required|string';
        $validationRules['password'] = 'required|string|min:6';
    } else {
        // Other roles require emp_code

        // original code
        $validationRules['emp_code'] = 'required|string|unique:registered_users,user_name';
        // charle - changes
            // $validationRules['emp_code'] = [
            //     'required',
            //     'string',
            //     function ($attribute, $value, $fail) {
            //         $exist = RegisteredUser::where('user_name', $value)
            //             ->whereNull('deleted_at')
            //             ->exists();
            //         if ($exist) {
            //             $fail('Employee Code already exists.');
            //         }
            //     }

            // ];        

        // Only require password for non-Admin/Receptionist roles
        if (!$isAdminOrReceptionist) {
            $validationRules['password'] = 'required|string|min:6';
        }
    }
    
    $request->validate($validationRules);

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
            'status' => 'error',
            'message' => 'Please select at least one location.'
        ], 422);
    }

    if ($roleName === 'receptionist' && count($locations) > 1) {
        return response()->json([
            'status' => 'error',
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
            
            // Check if username already exists and make it unique
            while (RegisteredUser::where('user_name', $username)->exists()) {
                $username = $baseUsername . '.' . $counter;
                $counter++;
            }
            
            RegisteredUser::create([
                'user_name'  => $username,
                'first_name' => $firstName, 
                'last_name'  => $lastName,
                'location'   => $locations,
                'password'   => Hash::make($request->password),     
                'user_type'  => $request->user_type,
                'created_by' => Auth::user()->id, 
                'updated_by' => Auth::user()->id,
            ]);
            
            return response()->json([
                'status' => 'success', 
                'message' => 'Guard registered successfully!'
            ]);
        }
        
        // For Admin/Receptionist and other roles with emp_code
        $empCode = $request->input('emp_code');
        
        // SEARCH logic (Session)
        $employees = collect(session('all_emp'));
        $employeeData = $employees->firstWhere('emp_code', $empCode);

        if (!$employeeData) {
            return response()->json([
                'status' => 'error', 
                'message' => 'Employee Code not found in session records.'
            ], 422);
        }

        $apiUrl = "http://192.168.200.185:1924/api/employee_details/" . $empCode;
        $response = Http::timeout(5)->get($apiUrl);

        // DATA PREPARATION: Use session data as default
        $firstName = $employeeData['first_name'] ?? 'N/A';
        $lastName  = $employeeData['last_name'] ?? 'N/A';

        RegisteredUser::create([
            'user_name'  => $empCode,
            'first_name' => $firstName, 
            'last_name'  => $lastName,
            'location'   => $locations,
            'password'   => Hash::make($request->password),     
            'user_type'  => $request->user_type,
            'created_by' => Auth::id(), 
            'updated_by' => Auth::id(),
        ]);

        if ($response->successful()) {
            $apiData = $response->json();
            $firstName = $apiData['FirstName'] ?? $firstName;
            $lastName  = $apiData['LastName'] ?? $lastName;
        } 

        return response()->json([
            'status' => 'success', 
            'message' => 'User registered successfully!'
        ]);

    } catch (\Exception $e) {
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
            'deleted_by' => Auth::user()->id // Optional: track who deleted it
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
    return response()->json(User_types::all());
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
        'location_names' => $locationNames // Display names
    ]);
}
public function updateUser(Request $request, $id) 
{
    try {
        // Find the user we are currently editing
        $user = RegisteredUser::findOrFail($id);
        
        // Get the user type to check role
        $userType = User_types::find($request->user_type);
        $roleName = $userType ? strtolower($userType->name) : '';
        $isGuard = $roleName === 'guard';
        
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
            $counter = 2;
            
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
                    'status' => 'error',
                    'message' => 'Please select at least one location.'
                ], 422);
            }

            if ($roleName === 'receptionist' && count($locations) > 1) {
                return response()->json([
                    'status' => 'error',
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
    // dd($data);
        foreach ($location as $record) {
        $data[] = [
            'id'   => $record['id'], // Ensure 'id' exists in your session array
            'text' => $record['name']
        ];
    }

    return response()->json($data);
}

    public function searchEmployees(Request $request)
    {
        $search = strtolower($request->input('q', ''));
        $employees = collect(session('all_emp', []));
        
        // Filter employees based on search term
        $filtered = $employees->filter(function ($emp) use ($search) {
            if (empty($search)) return true;
            
            $empCode = strtolower($emp['emp_code'] ?? '');
            $firstName = strtolower($emp['first_name'] ?? '');
            $lastName = strtolower($emp['last_name'] ?? '');
            
            return str_contains($empCode, $search) || 
                   str_contains($firstName, $search) || 
                   str_contains($lastName, $search);
        })->take(20); // Limit results
        
        $results = $filtered->map(function ($emp) {
            return [
                'id' => $emp['emp_code'],
                'text' => $emp['emp_code'] . ' - ' . ($emp['first_name'] ?? '') . ' ' . ($emp['last_name'] ?? ''),
                'first_name' => $emp['first_name'] ?? '',
                'last_name' => $emp['last_name'] ?? ''
            ];
        })->values()->toArray();
        
        return response()->json(['results' => $results]);
    }


public function list(Request $request){
     
        $keywords = strtolower($request->search);
        $limit    = $request->input('length');

        $rawquery = RegisteredUser::with('userType')
                    ->withoutTrashed()
                    ->where('deleted_at', null)
                    ->when($keywords, function ($query) use ($keywords) {
                        $query->where('user_name', 'LIKE', "%{$keywords}%")
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
            $data          = $rawquery->orderby("id", "desc")->take($limit)->get();
            $totalFiltered = count($temp);
       
        } else { 
       
            $data          = $rawquery->orderby("id", "desc")->take($limit)->get();
     
            $totalFiltered = $totalRecords;
        }
 
        $newData = [];
        $i       = 0;
      
        foreach ($data as $d) { 
       
            $newData[$i] = [
                'user_name'  => $d->first_name . ' ' . $d->last_name, // show emp_code in first column
                'user_type'  => $d->userType->name ?? '-',
                'created_by' => user_name($d->created_by) ?? '-',
                'updated_by' => user_name($d->updated_by) ?? '-',
                'created_at' => $d->created_at->format('F j, Y'). '<br>'. $d->created_at->format('l'),
                'updated_at' => $d->updated_at->format('F j, Y'). '<br>'. $d->updated_at->format('l'),
                'action'            => ' <div class="dropdown">
                                                    <button class="btn btn-sm btn-primary dropdown-toggle" 
                                                        type="button" 
                                                        data-bs-toggle="dropdown"
                                                        data-bs-boundary="viewport" aria-expanded="false">
                                                    Action
                                                </button>
                                                <ul class="dropdown-menu">
                                                    <li>
                                                        <a class="dropdown-item edit-user" href="javascript:void(0)" 
                                                        data-id="'.$d->id .'">
                                                            <i class="bi bi-pencil-square me-2"></i> Edit
                                                        </a>
                                                    </li>
                                                <li>
                                                    <button type="button" class="dropdown-item text-danger delete-user" 
                                                            data-id="'.$d->id .'">
                                                        <i class="bi bi-trash me-2"></i> Delete
                                                    </button>
                                                </li>
                                                </ul>
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

}