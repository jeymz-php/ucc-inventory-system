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
        $currentIms  = SystemStatus::current('ims');
        $currentCs   = SystemStatus::current('cs');

        $historyIms  = SystemStatus::where('system', 'ims')->with('changedBy')->latest()->take(5)->get();
        $historyCs   = SystemStatus::where('system', 'cs')->with('changedBy')->latest()->take(5)->get();

        $logs         = SystemLog::with('user')->latest()->paginate(20);
        $errorCount   = SystemLog::where('type', 'error')->where('is_resolved', false)->count();
        $warningCount = SystemLog::where('type', 'warning')->where('is_resolved', false)->count();
        $totalLogs    = SystemLog::count();

        return view('pages.system_status', compact(
            'currentIms', 'currentCs',
            'historyIms', 'historyCs',
            'logs', 'errorCount', 'warningCount', 'totalLogs'
        ));
    }

    public function toggle(Request $request)
    {
        $request->validate([
            'reason' => 'required|string|max:500',
            'system' => 'required|in:ims,cs',
        ]);

        $system    = $request->system;
        $current   = SystemStatus::current($system);
        $newStatus = $current?->status === 'up' ? 'down' : 'up';

        SystemStatus::create([
            'system'     => $system,
            'status'     => $newStatus,
            'reason'     => $request->reason,
            'changed_by' => auth()->id(),
            'changed_at' => now(),
        ]);

        SystemLog::create([
            'type'       => 'info',
            'title'      => strtoupper($system) . ' System Status Changed to ' . strtoupper($newStatus),
            'message'    => "Reason: {$request->reason}",
            'url'        => request()->fullUrl(),
            'method'     => 'POST',
            'user_id'    => auth()->id(),
            'user_role'  => auth()->user()->role,
            'ip_address' => request()->ip(),
        ]);

        $label = $system === 'cs' ? 'UCC-CS (Consumable System)' : 'UCC-IMS (Inventory System)';
        $msg   = $newStatus === 'down'
            ? "{$label} is now DOWN. Users will see the maintenance page."
            : "{$label} is back UP. All users can now access the system.";

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