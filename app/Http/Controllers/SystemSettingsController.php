<?php

namespace App\Http\Controllers;

use App\Models\SystemStatus;

class SystemSettingsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,superadmin']);
    }

    public function index()
    {
        $systemStatus = SystemStatus::current();
        return view('pages.system_settings', compact('systemStatus'));
    }
}