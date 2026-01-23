<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Visitor;
use App\Models\VisitorType;
use App\Models\RegisteredID;
use Illuminate\Support\Facades\Redirect;

class TryController extends Controller
{
    public function show()
    {
        $visitors = Visitor::where('status', 1)
                   ->orderBy('id', 'asc')
                   ->get();
         $visitorTypes = VisitorType::orderBy('id', 'asc')->get();

        return view('pages.visitorlog', compact('visitors', 'visitorTypes'));
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
        // Get all registered IDs, latest first
        $visitorTypes = VisitorType::orderBy('id', 'asc')->get();
        // Pass to the view
        return view('pages.visitortype', compact('visitorTypes'));
    }
    public function show_id()
    {
        $registeredIds = RegisteredID::where('deleted_at', null)
                   ->orderBy('visitor_type', 'asc')
                   ->get();
        $visitorTypes = VisitorType::orderBy('id', 'asc')->get();
        return view('pages.id', compact('registeredIds', 'visitorTypes'));
    }
    public function show_report()
    {
        return view('pages.report');
    }
}
