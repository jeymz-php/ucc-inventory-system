<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use Illuminate\Http\Request;

class HistoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,superadmin']);
    }

    public function index(Request $request)
    {
        $module = $request->get('module');
        $action = $request->get('action');
        $search = $request->get('search');

        $logs = ActivityLog::with('user')
            ->when($module, fn($q) => $q->where('module', $module))
            ->when($action, fn($q) => $q->where('action', $action))
            ->when($search, fn($q) => $q->where('description', 'like', "%$search%"))
            ->latest()
            ->paginate(20)
            ->withQueryString();

        $stats = [
            'total'   => ActivityLog::count(),
            'today'   => ActivityLog::whereDate('created_at', today())->count(),
            'creates' => ActivityLog::where('action', 'create')->count(),
            'deletes' => ActivityLog::where('action', 'delete')->count(),
        ];

        return view('pages.history', compact('logs', 'stats', 'module', 'action', 'search'));
    }
}