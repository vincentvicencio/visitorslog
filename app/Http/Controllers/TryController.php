<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Redirect;

class TryController extends Controller
{
    public function show()
    {
        return view('pages.visitorlog');
    }
    public function show_usertype()
    {
        return view('pages.usertype');
    }
    public function show_user()
    {
        return view('pages.users');
    }
    public function show_visitortype()
    {
        return view('pages.visitortype');
    }
    public function show_id()
    {
        return view('pages.id');
    }
    public function show_report()
    {
        return view('pages.report');
    }
}
