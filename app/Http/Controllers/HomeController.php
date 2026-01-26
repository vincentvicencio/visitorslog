<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    // public function index()
// {
//     $roles = \App\Models\User_types::all();
//     $registeredUsers = \App\Models\RegisteredUser::all(); 


//     // Fetch the employee data stored in the session during login
//     $allEmployeesFromSession = session('all_emp', []); 
        
    // dd($allEmployeesFromSession);
//     return view('home', compact('roles', 'registeredUsers', 'allEmployeesFromSession'));
// }
public function index(Request $request)
{
    $roles = \App\Models\user_types::all();
    
    // Check if there is a search query
    $search = $request->input('search');
    
    $registeredUsers = \App\Models\RegisteredUser::when($search, function ($query, $search) {
        return $query->where('user_name', 'like', "%{$search}%")
                     ->orWhere('first_name', 'like', "%{$search}%")
                     ->orWhere('last_name', 'like', "%{$search}%");
    })->get();

    $allEmployeesFromSession = session('all_emp', []); 

    return view('home', compact('roles', 'registeredUsers', 'allEmployeesFromSession'));
}

public function search_emp_code(Request $request){
    $employees  = collect(session('all_emp'));
    $details    = $employees->firstWhere('emp_code', $request->id);
    if(!$details){
        return response()->json(['msg' => 'No User Found']);
    }

    $code       = $details['emp_code'] ?? '';

    return response()->json([
        'emp_name'     => $details['first_name'] ." ". $details['last_name'] ?? '',
        'emp_code'     => $code ?? '',
    ]);
}
}
