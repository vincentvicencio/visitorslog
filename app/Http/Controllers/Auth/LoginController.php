<?php

namespace App\Http\Controllers\Auth;
use App\Models\RegisteredUser;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Support\Facades\Http;
use App\Helpers\APIHelper;
use App\Helpers\fetchdata_api;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use function fetchdata_api;
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
    protected $redirectTo = '/home';

    /**
     * Create a new controller instance.
     *
     * @return void
     */


    public function username()
    {
        return 'emp_code';
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



    protected function authenticated(Request $request, $user)
{
    // Determine employee code
    $emp_code = isset($user->emp_code) ? $user->emp_code : $user->tr_no;

    // Fetch all employee data
    $this->fetch_emp_data('all_emp');
    $this->fetch_api_data('all_location', 'location');
    $location = collect(session('all_location'));
    // dd($location);
    // dd($this->fetch_emp_data('all_emp'));
    // dd($allEmployeesFromSession);
    // Optionally fetch profile pic if needed
    // $this->fetch_profile_pic($emp_code);

    // Check user type from RegisteredUser model
    // $userType = RegisteredUser::where('emp_code', $emp_code)
    //     ->value('user_type');

    // // Redirect based on user type
    // if ($userType == 1) {
    //     return redirect()->route('reports'); // USER
    // }

    return redirect()->route('home'); // ADMIN
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
                ]
            ];
            $api_data = \fetchdata_api('api_data', $payload);
            
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