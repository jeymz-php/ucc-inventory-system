<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\ComputerInventory;
use App\Models\GeneralEquipment;
use App\Models\KitchenEquipment;
use App\Models\LabEquipment;
use App\Models\Location;
use App\Models\OfficeEquipment;
use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function equipmentModels(): array
    {
        return [
            ComputerInventory::class,
            KitchenEquipment::class,
            OfficeEquipment::class,
            LabEquipment::class,
            GeneralEquipment::class,
        ];
    }

    public function index()
    {
        $user = auth()->user();
        $role = $user->role;

        $totalEquipment    = 0;
        $condemnedItems    = 0;
        $assignedEquipment = 0;
        $myAssignedCount   = 0;

        foreach ($this->equipmentModels() as $modelClass) {
            $totalEquipment += $modelClass::count();
            $condemnedItems += $modelClass::where('is_condemned', true)->where('is_wasted', false)->count();
            $assignedEquipment += $modelClass::where('status', 'assigned')->count();

            if ($role === 'user' && $user->department_id) {
                // For regular users: count equipment located in rooms tied to their campus (best available signal)
                $myAssignedCount += $modelClass::where('campus_id', $user->campus_id)
                    ->where('status', 'assigned')->count();
            }
        }

        $stats = [
            'total_equipment'  => $totalEquipment,
            'active_locations' => Location::where('is_active', true)->count(),
            'active_users'     => User::where('is_active', true)->count(),
            'condemned'        => $condemnedItems,
            'my_equipment'     => $myAssignedCount,
        ];

        // Recent activity (admin/superadmin only — pulled from real ActivityLog)
        $recentActivity = collect();
        if (in_array($role, ['admin', 'superadmin'])) {
            $recentActivity = ActivityLog::with('user')->latest()->take(6)->get();
        }

        // Recent condemned items for the side panel (admin/superadmin only)
        $recentCondemned = collect();
        if (in_array($role, ['admin', 'superadmin'])) {
            foreach ($this->equipmentModels() as $modelClass) {
                $items = $modelClass::with('condemnedByUser')
                    ->where('is_condemned', true)
                    ->latest('condemned_date')
                    ->take(5)
                    ->get();
                $recentCondemned = $recentCondemned->merge($items);
            }
            $recentCondemned = $recentCondemned->sortByDesc('condemned_date')->take(5)->values();
        }

        return view('dashboard', compact('user', 'stats', 'recentActivity', 'recentCondemned'));
    }
}