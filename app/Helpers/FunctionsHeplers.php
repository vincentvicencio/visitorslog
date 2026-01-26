<?php

use Illuminate\Support\Facades\Http;

if (!function_exists("fetchdata_api")) {
    function fetchdata_api($endpoint, $payload)
    {
        $token = session('auth_token');
        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token, // Attach token
            'Accept' => 'application/json',
        ])->post(env('CENTRALHUB_API') . '/' . $endpoint, $payload);
       
        return $response->successful() ? $response->json() : null;
    }
}
