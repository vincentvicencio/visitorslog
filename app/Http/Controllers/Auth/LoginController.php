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
<<<<<<< HEAD

// use function fetchdata_api;
=======
use function PHPUnit\Framework\isNull;

>>>>>>> 055741908302b342b9f36e0f2272701dbeba42bf
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
    protected $redirectTo = '/visitorlog';

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
    return redirect()->route('visitorlog.index'); // ADMIN
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