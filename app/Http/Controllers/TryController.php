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
    // In TryController.php
public function show_usertype()
{
    // Fetch roles from the database using your User_types model
    $roles = \App\Models\User_types::all(); 
    
    // Pass the $roles variable to the view
    return view('pages.usertype', compact('roles'));
}
    public function show_user(Request $request)
    {
        // return view('pages.users');
        $registeredUsers = RegisteredUser::all();
        // 3. Pass the variable to the view
        // return view('pages.users', compact('registeredUsers'));
    
    $roles = \App\Models\user_types::all();
   

    
    // Check if there is a search query
    $search = $request->input('search');
    
    $registeredUsers = \App\Models\RegisteredUser::with('userType')
    ->whereNull('deleted_at') // <--- Add this line here
    ->when($search, function ($query, $search) {
        $query->where('user_name', 'like', "%{$search}%")
              ->orWhere('first_name', 'like', "%{$search}%")
              ->orWhere('last_name', 'like', "%{$search}%");
    })
    ->get();
    // dd($registeredUsers);

    $allEmployeesFromSession = session('all_emp', []);
    //  dd($allEmployeesFromSession);
    // return view('home', compact('roles', 'registeredUsers', 'allEmployeesFromSession'));
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
    public function show_report()
    {
        return view('pages.report');
    }

    
}
