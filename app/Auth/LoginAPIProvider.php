<?php

namespace App\Auth;

use Illuminate\Contracts\Auth\UserProvider;
use Illuminate\Contracts\Auth\Authenticatable;
use Illuminate\Auth\GenericUser;
use Illuminate\Auth\EloquentUserProvider;
use Illuminate\Support\Facades\Http;
use App\Models\RegisteredUser;
use Hash;
use Log;
use Illuminate\Support\Str;
class LoginAPIProvider implements UserProvider
{
    protected $eloquentProvider;
    protected $apiUrl;
    protected $appName;

    public function __construct($hasher, $model)
    {
        // fallback Eloquent provider
        $this->eloquentProvider = new EloquentUserProvider($hasher, $model);
        $this->apiUrl    = env('CENTRALHUB_API') . '/' . 'login_api';
        $this->appName   = env('APP_NAME', 'MyApp');
    }

    public function retrieveById($identifier)
    {
 
        return RegisteredUser::find($identifier);

    }

    public function retrieveByToken($identifier, $token)
    {
        return $this->eloquentProvider->retrieveByToken($identifier, $token);
    }

    public function updateRememberToken(Authenticatable $user, $token)
    {
        return $this->eloquentProvider->updateRememberToken($user, $token);
    }

    public function retrieveByCredentials(array $credentials)
    {
        $reg = RegisteredUser::withoutTrashed()
            ->where('user_name', $credentials['emp_code'])
            ->first();
        if (! $reg) {
            return null;
        }

        // 1) user_type != 3 → authenticate via API
        if ($reg->user_type != 3) {
            $resp = Http::post($this->apiUrl, [
                'emp_code' => $credentials['emp_code'],
                'password' => $credentials['password'],
                'app_name' => $this->appName,
            ]);

            if ($resp->successful() && data_get($resp->json(), 'result') === 200) {
                // API says OK → return the local RegisteredUser model
                $body  = $resp->json();
                $token = data_get($body, 'token');

                session(['auth_token' => $token]);

                return $reg;
            }

            return null;
        }

        // 2) user_type == 3 → local DB check, let validateCredentials handle it
        return $reg;
    }

    public function validateCredentials(Authenticatable $user, array $credentials)
    {
        $plain  = $credentials['password'];
        $stored = $user->getAuthPassword();

        // 1) API‐users (user_type != 3) already validated above
        if ($user->user_type != 3) {
            return true;
        }

        // 2) Bcrypt/Argon2?
        if (Str::startsWith($stored, ['$2y$', '$2b$', '$argon2'])) {
            return Hash::check($plain, $stored);
        }

        // 3) Legacy MD5?
        if (md5($plain) === $stored) {
            // upgrade to Bcrypt on‐the‐fly
            $user->password = Hash::make($plain);
            $user->save();
            return true;
        }

        // 4) Plain‐text fallback
        return $stored === $plain;
    }

    public function rehashPasswordIfRequired(Authenticatable $user, array $credentials, bool $force = false)
    {
        return false;
    }
}
