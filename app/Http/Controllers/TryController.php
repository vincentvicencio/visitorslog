<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\VisitorType;
use App\Models\UserType;
use App\Models\RegisteredUser;
use App\Models\RegisteredID;
use Illuminate\Support\Facades\Session;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Redirect;
use Auth;

class TryController extends Controller
{
    public function show()
    {
        $visitors = Visitor::where('status', 0)
                   ->whereNull('time_out')
                   ->orderBy('id', 'desc')
                   ->get();
        $visitorTypes = VisitorType::where('deleted_at', null)
                   ->orderBy('id', 'desc')
                   ->get();
        $empMap = collect(session('all_emp'))->keyBy('emp_code');
        return view('pages.visitorlog', compact('visitors', 'visitorTypes', 'empMap'));
    }
public function show_usertype()
{
    $roles = \App\Models\User_types::all(); 
    $visitorTypes = VisitorType::all();
    
    return view('pages.usertype', compact('roles', 'visitorTypes'));
}
    public function show_user(Request $request)
    {
        $registeredUsers = RegisteredUser::all();
        $roles = \App\Models\user_types::all();
        $visitorTypes = VisitorType::all();

        $visitorlogs = Visitor::with('visitor_type')->get();
        $search = $request->input('search');
        
        $registeredUsers = \App\Models\RegisteredUser::with('userType')
        ->whereNull('deleted_at')
        ->when($search, function ($query, $search) {
            $query->where('user_name', 'like', "%{$search}%")
                ->orWhere('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%");
        })
        ->get();

    $allEmployeesFromSession = session('all_emp', []);
    return view('pages.users', compact('roles', 'registeredUsers', 'allEmployeesFromSession', 'visitorlogs', 'visitorTypes'));
    }

    public function show_visitortype()
    {
        // Get all registered IDs, latest first
        $visitorTypes = VisitorType::where('deleted_at', null)
        ->orderBy('id', 'desc')
        ->get();
        // Pass to the view
        return view('pages.visitortype', compact('visitorTypes'));
    }
    public function show_id()
    {
        $registeredIds = RegisteredID::where('deleted_at', null)
                   ->orderBy('id', 'desc')
                   ->get();
        $visitorTypes = VisitorType::where('deleted_at', null)
                   ->orderBy('id', 'desc')
                   ->get();
        $visitorsLogs = Visitor::where('status', 0)
                   ->whereNull('time_out')
                   ->orderBy('id', 'desc')
                   ->get();
        return view('pages.id', compact('registeredIds', 'visitorTypes', 'visitorsLogs'));
    }
    public function show_report(Request $request)
{
    $query = Visitor::with('visitor_type');

    // Filter by Date Range
    if ($request->filled('date_from')) {
        $query->whereDate('created_at', '>=', $request->date_from);
    }
    if ($request->filled('date_to')) {
        $query->whereDate('created_at', '<=', $request->date_to);
    }

    // Filter by Visitor Type
    if ($request->filled('visitor_type')) {
        $query->where('visitor_type', $request->visitor_type);
    }

    // Existing Search Logic
    if ($request->filled('searchreport')) {
        $search = $request->searchreport;
        $query->where(function($q) use ($search) {
            $q->where('first_name', 'like', "%$search%")
              ->orWhere('last_name', 'like', "%$search%");
        });
    }

    $visitorlogs = $query->orderBy('created_at', 'desc')->get();
    $visitorTypes = VisitorType::all();
    $allEmployeesFromSession = session('all_emp', []);

    return view('pages.report', compact('visitorlogs', 'visitorTypes', 'allEmployeesFromSession'));
}


public function destroy($id)

{
try {
        $visitor = Visitor::findOrFail($id);

        // Instead of $user->delete(), we update the column
        $visitor->update([
            'deleted_at' => NOW(),
            'deleted_by' => Auth::user()->first_name ?? 'System' // Optional: track who deleted it
        ]);

        // return response()->json([
        //     'status' => 'success', 
        //     'message' => 'Record Deleted successfully.'
        // ]);
    } catch (\Exception $e) {
        // return response()->json([
        //     'status' => 'error', 
        //     'message' => 'Failed to remove user.'
        // ], 500);
    }

}

}
