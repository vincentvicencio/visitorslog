<html>

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>
        @php
            $titles = [
                'visitorslog' => 'Log Sheet',
                'idtype' => 'ID Type',
                'usertypes' => 'User Type',
                'registeruser' => 'User',
                'visitortype' => 'Visitor Type',
                'registerid' => 'ID Numbers',
                'reports' => 'Reports',
                'about' => 'About',
                'visitorslog.form' => 'Visitor Log Form',
                'employeeslog.form' => 'Employee Log Form',
                'viewemp.page' => 'View Employee',
                'view.page' => 'View Visitor',
            ];

            $route = strtolower(Route::currentRouteName());
        @endphp

        {{ $titles[$route] ?? ucwords(str_replace(['.', '-', '_'], ' ', $route)) }} | Magellan Solutions
    </title>

        <link rel="icon" type="image/png" href="/images/magellan logo.png">

    @vite(['resources/sass/app.scss', 'resources/js/app.js'])

    @yield('header-styles')


</head>