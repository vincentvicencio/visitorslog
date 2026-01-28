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

class TryController extends Controller
{
    public function show()
    {
        $visitors = Visitor::where('status', 1)
                   ->orderBy('id', 'asc')
                   ->get();
         $visitorTypes = VisitorType::orderBy('id', 'asc')->get();

        return view('pages.visitorlog', compact('visitors', 'visitorTypes'));
    }
public function show_usertype()
{
    $roles = \App\Models\User_types::all(); 
    // ->whereNull('deleted_at')
    // $roles = User_types::whereNull('deleted_at')->get();
    
    return view('pages.usertype', compact('roles'));
}
    public function show_user(Request $request)
    {
        $registeredUsers = RegisteredUser::all();
        $roles = \App\Models\user_types::all();
        $search = $request->input('search');
        
        $registeredUsers = \App\Models\RegisteredUser::with('userType')
        ->whereNull('deleted_at') // <--- Add this line here
        ->when($search, function ($query, $search) {
            $query->where('user_name', 'like', "%{$search}%")
                ->orWhere('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%");
        })
        ->get();

    $allEmployeesFromSession = session('all_emp', []);
    return view('pages.users', compact('roles', 'registeredUsers', 'allEmployeesFromSession'));
    }

    public function show_visitortype()
    {
        // Get all registered IDs, latest first
        $visitorTypes = VisitorType::orderBy('id', 'asc')->get();
        // Pass to the view
        return view('pages.visitortype', compact('visitorTypes'));
    }
    public function show_id()
    {
        $registeredIds = RegisteredID::where('deleted_at', null)
                   ->orderBy('visitor_type', 'asc')
                   ->get();
        $visitorTypes = VisitorType::orderBy('id', 'asc')->get();
        return view('pages.id', compact('registeredIds', 'visitorTypes'));
    }
    public function show_report(Request $request)
    {
        $visitorlogs = Visitor::all();
        $search = $request->input('searchreport');
    
        $visitorlogs = Visitor::with('visitor_type')
        // ->whereNull('deleted_at') // <--- Add this line here
        ->when($search, function ($query, $search) {
            $query->where('user_name', 'like', "%{$search}%")
                ->orWhere('first_name', 'like', "%{$search}%")
                ->orWhere('last_name', 'like', "%{$search}%");
        })
        ->get();

        $allEmployeesFromSession = session('all_emp', []);
        return view('pages.report', compact('visitorlogs', 'allEmployeesFromSession'));
    }
}
