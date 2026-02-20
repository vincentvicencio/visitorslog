<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Centralized Login API URL
    |--------------------------------------------------------------------------
    |
    | This value determines the API endpoint used for centralized login
    | authentication requests. It can be set in your app's .env file
    | using the CENTRALHUB_API variable.
    |
    */

    'api_url' => env('CENTRALHUB_API', 'http://127.0.0.1:8000/api'),

];