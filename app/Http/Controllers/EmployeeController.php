<?php

namespace App\Http\Controllers;

use App\Models\EmployeeLogs;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use App\Models\Location;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Str;

class EmployeeController extends Controller
{
    private function guardLocationRequired()
    {
        $user = Auth::user();

        return $user && (int) $user->user_type === 3 && !session()->has('guard_location_id');
    }

    private function resolveLocationForSave($user): ?string
    {
        if ((int) $user->user_type === 3) {
            if (session()->has('guard_location_name')) {
                return (string) session('guard_location_name');
            }

            if (session()->has('guard_location_id')) {
                return (string) session('guard_location_id');
            }
        }

        $filters = $this->resolveUserLocationFilters($user);

        return $filters[0] ?? null;
    }

    private function saveProfilePicFromBase64(?string $data, string $empCode): ?string
    {
        if (empty($data)) {
            return null;
        }

        if (!str_starts_with($data, 'data:image/')) {
            // Already a URL/path, keep as-is.
            return $data;
        }

        try {
            [$meta, $encoded] = explode(',', $data, 2);
        } catch (\Throwable $e) {
            return null;
        }

        $mime = null;
        if (preg_match('/^data:(image\/\w+);base64$/', $meta, $m)) {
            $mime = $m[1];
        }

        if (empty($mime) || empty($encoded)) {
            return null;
        }

        $decoded = base64_decode($encoded);
        if ($decoded === false) {
            return null;
        }

        $extension = 'png';
        if ($mime === 'image/jpeg') {
            $extension = 'jpg';
        } elseif ($mime === 'image/gif') {
            $extension = 'gif';
        } elseif ($mime === 'image/webp') {
            $extension = 'webp';
        }

        $filename = sprintf('emp_%s_%s.%s', preg_replace('/[^A-Za-z0-9_\-]/', '_', (string) $empCode), time(), $extension);
        $diskPath = 'employee_profile_pics/' . $filename;
        $publicPath = '/storage/employee_profile_pics/' . $filename;

        \Illuminate\Support\Facades\Storage::disk('public')->put($diskPath, $decoded);

        return $publicPath;
    }

    private function resolveUserLocationFilters($user): array
    {
        if ((int) $user->user_type === 3) {
            $filters = [];

            if (session()->has('guard_location_id')) {
                $filters[] = (string) session('guard_location_id');
            }

            if (session()->has('guard_location_name')) {
                $filters[] = (string) session('guard_location_name');
            }

            return array_values(array_unique(array_filter($filters, fn ($value) => $value !== '')));
        }

        $allLocations = collect(session('all_location', []));
        $rawLocation = $user->location;
        if (is_array($rawLocation)) {
            $userLocations = $rawLocation;
        } elseif (is_string($rawLocation)) {
            $decoded = json_decode($rawLocation, true);
            if (json_last_error() === JSON_ERROR_NONE && is_array($decoded)) {
                $userLocations = $decoded;
            } elseif ($rawLocation !== '') {
                $userLocations = [$rawLocation];
            } else {
                $userLocations = [];
            }
        } elseif (is_numeric($rawLocation)) {
            $userLocations = [(string) $rawLocation];
        } else {
            $userLocations = [];
        }
        $locationFilters = [];

        foreach ($userLocations as $locationValue) {
            if ($locationValue === null || $locationValue === '') {
                continue;
            }

            $locationFilters[] = (string) $locationValue;
            $match = $allLocations->firstWhere('id', $locationValue);
            if ($match && !empty($match['name'])) {
                $locationFilters[] = (string) $match['name'];
            }
        }

        return array_values(array_unique(array_filter($locationFilters, fn ($value) => !empty($value))));
    }

    public function list(Request $request){

        $keywords = strtolower($request->search);
        $limit    = $request->input('length');


        $rawquery = EmployeeLogs:: withoutTrashed()
                ->where(function ($query) {

                    $user = Auth::user();
                    if ((int) $user->user_type !== 1) {
                        $userLocations = $this->resolveUserLocationFilters($user);

                        // First filter by location (non-admin only)
                        $query->whereIn('location', $userLocations);
                    }
                })

                -> when($keywords, function ($query) use ($keywords) {
                    $query->where(function ($q) use ($keywords) {
                        $q->where   ('full_name', 'LIKE', "%{$keywords}%")
                          ->orWhere('emp_code', 'LIKE', "%{$keywords}%");
                    });
                });
        
        $totalRecords = $rawquery->get()->count();
        
        if ($request->input('draw') > 1) { 
            $start         = $request ->input('start'); 
            $column        = $request ->input('order.0.column');
            $direction     = $request ->input('order.0.dir');
            $order         = $request ->input('columns')[$column]['data']; 
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
        $companyLocations = collect(session('all_location'))->keyBy(function ($item) {
            return (string) data_get($item, 'id');
        });
        $guardLocations = Location::query()->get(['id', 'name'])->keyBy(function ($item) {
            return (string) $item->id;
        });
      
        foreach ($data as $d) { 
            $locationLabel = '';
            $locationLabel = (string) $d->location;

            if (is_numeric($locationLabel)) {
                $companyLocation = $companyLocations->get($locationLabel);
                $guardLocation = $guardLocations->get($locationLabel);

                if ($companyLocation) {
                    $locationLabel = data_get($companyLocation, 'name', '');
                } elseif ($guardLocation) {
                    $locationLabel = $guardLocation->name;
                }
            }

            $status = '';
            $statuslayout = '';

            if($d->status == 0){
                $status = 'In';
            }else{
                $status = 'Out';
            }

            if ($status === 'Out') {
                $statuslayout = '<div class="status-cell"><div class="status text-danger border border-danger rounded-2"> '. $status .'</div></div>';
            }
            else{
                $statuslayout = '<div class="status-cell"><div class="status rounded-2" > '. $status .'</div></div>';
            }

            $time = Carbon::parse($d->time)->format('h:i A');

            $createdby = $d->created_by ? user_name($d->created_by) : '-';

            $activity = $d->activity ? $d->activity : '-';

            $newData[$i] = [
                
                'emp_code' => $d->emp_code,

                'full_name' => $d->full_name,

                'location' => '<div class="text-center">' . $locationLabel . '</div>',

                'log_date' =>  '<div class="text-center">' . 
                                    $d->created_at->format("F d, Y") .'<br>
                                    '. $d->created_at->format('l')
                                 . '</div>',

                'time' => '<div class="text-center">
                                <small> '. $time .'</small><br>
                            </div>',
                'activity' => '<div class="text-center">
                                '. $activity .'<br>
                            </div>',

                'status'   => $statuslayout,

                'creator' => '<div class="text-center">
                                '. $createdby .'
                            </div>',

                'action' => '<div class="dropdown">
                                        <button 
                                            class="dropdown-item"
                                            id="empViewBtn"
                                            data-id="'. $d->id .'"
                                            data-type="visitorslog">
                                            View
                                        </button>
                            </div>',
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

    public function timeout(Request $request)
    {
        $visitor = EmployeeLogs::where('id', $request->id)->first();

        if (!$visitor) {
            return response()->json([
                'status'  => 1,
                'title'   => 'Error',
                'message' => 'Employee not found'
            ], 404);
        }

        if ($visitor->status == 1) {
            return response()->json([
                'status'  => 1,
                'title'   => 'Error',
                'message' => 'Employee already timed out'
            ], 400);
        }

        $oldData = $visitor->getOriginal();

        $visitor->update([
            'time_out'   => Carbon::now(),
            'status'     => 1,
            'updated_by' => Auth::user()->id,
        ]);

        log_audit(
            'employee logs',
            'timed out',
            $visitor->id,
            $oldData,
            $visitor->getAttributes(),
            'timeout'
        );

        return response()->json([
            'status'     => 0,
            'title'      => 'Success',
            'message'    => 'Employee successfully timed out'
        ]);
    }
    public function view(Request $request)
    {
        $request->validate([
            'id' => 'required|exists:emp_logs,id',
        ]);

        $visitor = EmployeeLogs::where('id', $request->id)
            ->latest('id')
            ->first();

        if (!$visitor) {
            return response()->json([
                'message' => 'Empyloyee not found or inactive'
            ], 404);
        }

        // log view action before redirecting
        log_audit(
            'employee logs',
            'viewed',
            $visitor->id,
            null,
            null,
            'view'
        );

        return response()->json([
            'redirect'   => route('viewEmp.page', [
                'id'     => $visitor->id,
                'type'   => $request->type,
            ])
        ]);

    }

    public function form(){
        $user = Auth::user();
        $locationLabel = $this->resolveLocationForSave($user) ?? '';

        // Try to resolve a human-readable name from all_location session
        if (is_numeric($locationLabel)) {
            $allLocations = collect(session('all_location', []));
            $match = $allLocations->firstWhere('id', $locationLabel);
            if ($match && !empty($match['name'])) {
                $locationLabel = $match['name'];
            } else {
                $guardLocation = \App\Models\Location::find($locationLabel);
                if ($guardLocation) {
                    $locationLabel = $guardLocation->name;
                }
            }
        }

        return view('pages.employeeslog.form', compact('locationLabel'));
    }
    
    public function searchEmployees(Request $request)
    {
        $search    = strtolower(trim((string) $request->input('q', '')));
        $employees = collect(session('all_emp', []));
        // $sessionEmployee = $allEmployees->firstWhere('emp_code', $user->user_name);

        // Fallback for users without auth_token-backed session data (e.g., guards)
        if ($employees->isEmpty()) {
            $response = Http::post(env('CENTRALHUB_API') . '/login_api', [
                'emp_code' => env('WEBDEV_APICRED'),
                'password' => env('WEBDEV_APIPASS'),
                'app_name' => 'VISITORS_LOG',
            ]);
     
            $token = $response->successful() ? data_get($response->json(), 'token') : null;
            if ($token) {
                session(['auth_token' => $token]);
                $payload = [
                'model'  => 'emp_details',
                'select' => [
                    'id',
                    'emp_code',
                    'first_name',
                    'middle_name',
                    'last_name',
                    'department_id',
                    'section_id',
                    'location_id'
                ]
                ];
                $employees = collect(fetchdata_api('api_data', $payload));
            }

        }

        // Filter employees based on search term
        $filtered = $employees->filter(function ($emp) use ($search) {
            if (empty($search)) return true;
            
            $empCode   = strtolower($emp['emp_code'] ?? '');
            $firstName = strtolower($emp['first_name'] ?? '');
            $middleName = strtolower($emp['middle_name'] ?? '');
            $lastName  = strtolower($emp['last_name'] ?? '');

            $fullName = trim(preg_replace('/\s+/', ' ', $firstName . ' ' . $middleName . ' ' . $lastName));
            $firstLast = trim(preg_replace('/\s+/', ' ', $firstName . ' ' . $lastName));
            $lastFirst = trim(preg_replace('/\s+/', ' ', $lastName . ' ' . $firstName));
            $normalizedSearch = trim(preg_replace('/\s+/', ' ', $search));
            
            return str_contains($empCode, $search) || 
                   str_contains($firstName, $search) || 
                   str_contains($middleName, $search) || 
                   str_contains($lastName, $search) ||
                   str_contains($fullName, $normalizedSearch) ||
                   str_contains($firstLast, $normalizedSearch) ||
                   str_contains($lastFirst, $normalizedSearch);
        })->take(20); // Limit results
        
        $results = $filtered->map(function ($emp) {
            $pic = fetch_profile_pic($emp['emp_code'] ?? '');
            return [
                'id'         => $emp['emp_code'],
                'text'       => trim(($emp['emp_code'] ?? '') . ' - ' . ($emp['first_name'] ?? '') . ' ' . ($emp['middle_name'] ?? '') . ' ' . ($emp['last_name'] ?? '')),
                'first_name' => $emp['first_name'] ?? '',
                'middle_name' => $emp['middle_name'] ?? '',
                'last_name'  => $emp['last_name'] ?? '',
                'profile_pic' => $pic['image'] ?? null,
            ];
        })->values()->toArray();
        
        return response()->json(['results' => $results]);
    }

    public function save(Request $request)
    {
        if ($this->guardLocationRequired()) {
            return response()->json([
                'status' => 1,
                'title' => 'Error',
                'message' => 'Please select guard location first.',
                'redirect' => route('guard.location.show'),
            ], 422);
        }

        try {
            $request->validate([
                'emp_code'      => 'required|string',
                'full_name'     => 'required|string|max:100',
                'activity'      => 'nullable|string',
            ], [
                'emp_code.required'    => 'Employee Code is required',
                'full_name.required'   => 'Full Name is required',
            ]);

            // $latestLog = EmployeeLogs::where('emp_code', $request->emp_code)
            //     ->latest()
            //     ->first();

            // $latestLogCount = EmployeeLogs::where('emp_code', $request->emp_code)
            //     ->count();

            // $isSameStatus = $latestLog && $latestLog->status == $request->status;

            // if ($isSameStatus) {
            //     $message = $latestLog->status == 0
            //         ? 'Employee is currently in'
            //         : 'Employee is currently out';

            //     return response()->json([
            //         'status' => 1,
            //         'title' => 'Invalid',
            //         'message' => $message,
            //     ], 200);
            // }

            // if($latestLogCount == 0 && $request->status == 1){
            //     return response()->json([
            //         'status' => 1,
            //         'title' => 'Invalid',
            //         'message' => 'Employee has not logged in yet.',
            //     ], 200);
            // }

            $user = Auth::user();
            $locationForSave = $this->resolveLocationForSave($user);

            $activity = Str::title(trim((string) $request->activity ?? '-'));

            $employeeLog = new EmployeeLogs();
            $employeeLog->emp_code = $request->emp_code;
            $employeeLog->full_name = $request->full_name;
            $employeeLog->profile_pic = $this->saveProfilePicFromBase64($request->image_path ?? null, $request->emp_code);
            $employeeLog->location = $locationForSave ?? '?';
            $employeeLog->time = now();
            $employeeLog->activity = $activity;
            $employeeLog->status = $request->status; // Active
            $employeeLog->created_by = $user->id;
            $employeeLog->save();

            // Audit log for new employee log. Avoid storing entire base64 image in audit metadata.
            try {
                log_audit(
                    'employee logs',
                    'created',
                    $employeeLog->id,
                    null,
                    [
                        'emp_code' => $employeeLog->emp_code,
                        'full_name' => $employeeLog->full_name,
                        'location' => $employeeLog->location,
                        'profile_pic' => $employeeLog->profile_pic ? 'included' : 'none',
                    ],
                    'save'
                );
            } catch (\Throwable $e) {
                // Keep save success even if audit fails.
                \Log::error('Audit save failed for EmployeeLog: ' . $e->getMessage());
            }
            
            $message = '';
            if($request->status == 1){
                $message = 'Employee successfully logged out';
            }else{
                $message = 'Employee successfully logged in';
            }
            return response()->json([
                'status' => 0,
                'title' => 'Success',
                'message' => $message
            ], 200);

        } catch (\Illuminate\Validation\ValidationException $e) {
            $errors = $e->errors();
            $firstError = collect($errors)->flatten()->first();
            return response()->json([
                'status' => 1,
                'title' => 'Error',
                'message' => $firstError ?? 'Validation error'
            ], 422);

        } catch (\Exception $e) {
            return response()->json([
                'status' => 1,
                'title' => 'Error',
                'message' => 'Failed to log employee: ' . $e->getMessage()
            ], 500);
        }
    }
}
