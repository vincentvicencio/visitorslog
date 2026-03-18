<?php

use Illuminate\Support\Facades\Http;

if (!function_exists('fetchdata_api')) {
    function fetchdata_api($endpoint, $payload)
    {
        $token = session('auth_token');

        $response = Http::withHeaders([
            'Authorization' => 'Bearer ' . $token,
            'Accept' => 'application/json',
        ])->post(env('CENTRALHUB_API') . '/' . $endpoint, $payload);

        return $response->successful() ? $response->json() : null;
    }
}


if (!function_exists('fetch_profile_pic')) {
    function fetch_profile_pic($emp_code)
    {
        try {
            if (empty($emp_code)) {
                return ['image' => null, 'error' => 'No emp_code'];
            }

            $token = session('auth_token');
            $apiUrl = env('CENTRALHUB_API');
            if (empty($apiUrl)) {
                return ['image' => null, 'error' => 'No API URL'];
            }

            $payload = ['emp_code' => $emp_code];

            $response = Http::withHeaders([
                'Authorization' => 'Bearer ' . $token,
                'Accept' => 'application/json',
            ])->post(rtrim($apiUrl, '/') . '/fetch_profile_pic', $payload);

            if (!$response->successful()) {
                return ['image' => null, 'error' => 'Failed API'];
            }

            $imageData = $response->body();
            if (empty($imageData)) {
                return ['image' => null, 'error' => 'Empty image'];
            }

            $finfo = new \finfo(FILEINFO_MIME_TYPE);
            $mimeType = $finfo->buffer($imageData);
            if (empty($mimeType)) {
                return ['image' => null, 'error' => 'Invalid mime'];
            }

            $base64Image = base64_encode($imageData);
            $fullImageString = 'data:' . $mimeType . ';base64,' . $base64Image;

            session(['profile_pic' => $fullImageString]);

            return ['image' => $fullImageString];
        } catch (\Throwable $e) {
            return ['image' => null, 'error' => $e->getMessage()];
        }
    }
}