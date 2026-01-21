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
}
