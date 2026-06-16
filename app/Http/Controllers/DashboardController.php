<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index()
    {
        $user = auth()->user();

        // Stats — we'll connect real data later when we build each module
        $stats = [
            'total_equipment'  => 0,
            'active_locations' => 0,
            'active_users'     => 0,
            'condemned'        => 0,
        ];

        return view('dashboard', compact('user', 'stats'));
    }
}