<?php

namespace App\Http\Controllers;

use App\Models\EmployeeLogs;
use Illuminate\Http\Request;
use Carbon\Carbon;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use App\Models\Location;

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

                    // Then filter active / not timed out
                    $query->where(function ($q) {
                        $q->where('status', 0)
                        ->where('time_out', null);
                    });
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

            if($d->status == 0){
                $status = 'Active';
            }else{
                $status = 'Timed Out';
            }

            $time_in = Carbon::parse($d->time_in)->format('h:i A');

            $time_out = $d->time_out ? Carbon::parse($d->time_out)->format('h:i A') : '-';

            $createdby = $d->created_by ? user_name($d->created_by) : '-';
            $updatedby = $d->updated_by ? user_name($d->updated_by) : '-';
            

            $newData[$i] = [
                
                'emp_code' => $d->emp_code,

                'full_name' => $d->full_name,

                'location' => '<div class="text-center">' . $locationLabel . '</div>',

                'log_date' =>  '<div class="text-center">' . 
                                    $d->created_at->format("F d, Y") .'<br>
                                    '. $d->created_at->format('l')
                                 . '</div>',

                'time_in' => '<div class="text-center">
                                <small> '. $time_in .'</small><br>
                            </div>',
                'time_out' => '<div class="text-center">
                                <small> '. $time_out .'</small><br>
                            </div>',

                'creator' => '<div class="text-center">
                                '. $createdby .'
                            </div>',
                
                'status'       => '<div class="status-cell"><div class="status rounded-2"> '. $status .'</div></div>',

                'action' => '<div class="dropdown">
                                        <button 
                                            class="dropdown-item"
                                            id="viewBtn"
                                            data-id="'. $d->id .'"
                                            data-type="visitorslog">
                                            View
                                        </button>
                                        <button 
                                            type="button"
                                            class="dropdown-item text-danger"
                                            id="empTimeoutBtn"
                                            data-id="'. $d->id .'">
                                            Timeout
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
            'redirect'   => route('view.page', [
                'id'     => $visitor->id,
                'type'   => $request->type,
            ])
        ]);

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
                'first_name'    => 'required|string|max:40',
                'last_name'     => 'required|string|max:40',
                'full_name'     => 'required|string|max:100',
            ], [
                'emp_code.required'    => 'Employee Code is required',
                'first_name.required'  => 'First Name is required',
                'last_name.required'   => 'Last Name is required',
                'full_name.required'   => 'Full Name is required',
            ]);

            $user = Auth::user();
            $locationForSave = $this->resolveLocationForSave($user);

            $employeeLog = new EmployeeLogs();
            $employeeLog->emp_code = $request->emp_code;
            $employeeLog->first_name = $request->first_name;
            $employeeLog->last_name = $request->last_name;
            $employeeLog->full_name = $request->full_name;
            $employeeLog->location = $locationForSave ?? '?';
            $employeeLog->time_in = now();
            $employeeLog->status = 0; // Active
            $employeeLog->created_by = $user->id;
            $employeeLog->save();

            // Audit log for new employee log
            log_audit(
                'employee logs',
                'created',
                $employeeLog->id,
                null,
                $employeeLog->toArray(),
                'save'
            );

            return response()->json([
                'status' => 0,
                'title' => 'Success',
                'message' => 'Employee successfully logged in'
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
