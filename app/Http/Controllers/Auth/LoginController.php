<?php

namespace App\Http\Controllers\Auth;
use App\Models\RegisteredUser;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Http;
use App\Helpers\APIHelper;
// use App\Helpers\fetchdata_api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Helpers\fetchdata_api;
use function fetchdata_api;
use function PHPUnit\Framework\isNull;

class LoginController extends Controller

{
    /*
    |--------------------------------------------------------------------------
    | Login Controller
    |--------------------------------------------------------------------------
    |
    | This controller handles authenticating users for the application and
    | redirecting them to your home screen. The controller uses a trait
    | to conveniently provide its functionality to your applications.
    |
    */

    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/visitorslog';

    /**
     * Create a new controller instance.
     *
     * @return void
     */


    public function username()
    {
        return 'emp_code';
    }

    protected function guard()
    {
        return Auth::guard('employee');
    }
    
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
        $this->middleware('auth')->only('logout');
    }

    public function logout(Request $request){
        $token = session('auth_token');
         if ($token) {
            Http::withToken($token)->post(env('CENTRALHUB_API') . '/logout');
        }

        $this->guard()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect('/login');
    }

    // protected function authenticated(Request $request, $user)
    // {
    //     // Determine employee code
    //     $emp_code = isset($user->emp_code) ? $user->emp_code : $user->tr_no;

    //     // Fetch all employee data
    //     $this->fetch_emp_data('all_emp');
    //     $this->fetch_api_data('all_location', 'location');
    //     $location = collect(session('all_location'));
    //     return redirect()->route('visitorslog'); // ADMIN
    // }

    // app/Http/Controllers/Auth/LoginController.php

    // protected function authenticated(Request $request, $user)
    // {
        
    //     // 1. Look up the user in the registered_users table using the emp_code
    //     $registeredUser = RegisteredUser::where('user_name', $user->emp_code)->first();

    //     // 2. If the user isn't in our registered_users table, kick them out
    //     if (!$registeredUser) {
    //         Auth::logout();
    //         return redirect('/login')->with('error', 'Your account is not authorized to access this system.');
    //     }

    //     // 3. Store the user_type in the session so we can use it in sidebars and views
    //     session(['user_type' => $registeredUser->user_type]);

    //     // Fetch existing API data (from your original code)
    //     $this->fetch_emp_data('all_emp');
    //     $this->fetch_api_data('all_location', 'location');

    //     return redirect()->route('visitorslog');
    // }

    
    protected function attemptLogin(Request $request)
    {
        return Auth::guard('employee')->attempt(
            $this->credentials($request),
            $request->filled('remember')
        );
    }


    public function authenticated(Request $request, $user)
    {

        $registeredUser = RegisteredUser::where('user_name', $request->emp_code)->first();
        if ($registeredUser->user_type != 3){
            $registeredUser->update(['password' => NULL]);
            
        }

        $this->fetch_emp_data('all_emp');
        $this->fetch_api_data('all_location', 'location');

        // return redirect()->route('visitorslog');

    }
    
    private function fetch_emp_data($sessionKey){
        if(!Session::has($sessionKey)){
            $payload = [
                'model' => 'emp_details',
                'select' => [
                    'id',
                    'emp_code',
                    'first_name',
                    'last_name',
                    'department_id',
                    'section_id',
                    'location_id'
                ]
            ];
            $api_data = fetchdata_api('api_data', $payload);
            // dd($api_data);
            // dd(function_exists('fetchdata_api'));

            Session::put($sessionKey, $api_data);
        }
        else{
            $api_data = Session::get($sessionKey);
        }

        return $api_data;
    }

    private function fetch_api_data($sessionKey, $model){
        if(!Session::has($sessionKey)){
            $payload = [
                'model' => $model,
            ];
            $api_data = fetchdata_api('api_data', $payload);
            Session::put($sessionKey, $api_data);
        }
        else{
            $api_data = Session::get($sessionKey);
        }

        return $api_data;
    }
}