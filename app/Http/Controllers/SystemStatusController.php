<?php

namespace App\Http\Controllers;

use App\Models\SystemLog;
use App\Models\SystemStatus;
use Illuminate\Http\Request;

class SystemStatusController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:superadmin']);
    }

    public function index()
    {
        $current  = SystemStatus::with('changedBy')->latest()->first();
        $history  = SystemStatus::with('changedBy')->latest()->take(10)->get();
        $logs     = SystemLog::with('user')->latest()->paginate(20);
        $errorCount   = SystemLog::where('type', 'error')->where('is_resolved', false)->count();
        $warningCount = SystemLog::where('type', 'warning')->where('is_resolved', false)->count();
        $totalLogs    = SystemLog::count();

        return view('pages.system_status', compact(
            'current', 'history', 'logs',
            'errorCount', 'warningCount', 'totalLogs'
        ));
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
        ]);

        $current   = SystemStatus::current();
        $newStatus = $current?->status === 'up' ? 'down' : 'up';

        SystemStatus::create([
            'status'     => $newStatus,
            'reason'     => $request->reason,
            'changed_by' => auth()->id(),
            'changed_at' => now(),
        ]);

        SystemLog::create([
            'type'       => 'info',
            'title'      => 'System Status Changed to ' . strtoupper($newStatus),
            'message'    => "Reason: {$request->reason}",
            'url'        => request()->fullUrl(),
            'method'     => 'POST',
            'user_id'    => auth()->id(),
            'user_role'  => auth()->user()->role,
            'ip_address' => request()->ip(),
        ]);

        $msg = $newStatus === 'down'
            ? 'System is now DOWN. Users will see the maintenance page.'
            : 'System is back UP. All users can now access the system.';

        return back()->with('success', $msg);
    }

    public function resolveLog(SystemLog $log)
    {
        $log->update(['is_resolved' => true]);
        return back()->with('success', 'Log marked as resolved.');
    }

    public function clearLogs()
    {
        SystemLog::where('is_resolved', true)->delete();
        return back()->with('success', 'Resolved logs cleared.');
    }
}