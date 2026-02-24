<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User_types;
use App\Models\Visitor;
use App\Models\VisitorType;
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
public function index(Request $request)
{
    $roles = User_types::all();
    // dd($roles);
    // Check if there is a search query
    $search = $request->input('search');
    
    $registeredUsers = \App\Models\RegisteredUser::when($search, function ($query, $search) {
        return $query->where('user_name', 'like', "%{$search}%")
                     ->orWhere('first_name', 'like', "%{$search}%")
                     ->orWhere('last_name', 'like', "%{$search}%");
    })->get();

    $allEmployeesFromSession = session('all_emp', []); 

    $visitors     =  Visitor::where('status', 0)
                    ->whereNull('time_out')
                    ->orderBy('id', 'asc')
                    ->get();
    $visitorTypes = VisitorType::where('deleted_at', null)
                    ->orderBy('id', 'asc')
                    ->get();

    return view('pages.visitorlog', compact('roles', 'registeredUsers', 'allEmployeesFromSession', 'visitors', 'visitorTypes'));
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
