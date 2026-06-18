<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\ComputerInventory;
use App\Models\GeneralEquipment;
use App\Models\KitchenEquipment;
use App\Models\LabEquipment;
use App\Models\Location;
use App\Models\OfficeEquipment;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class EquipmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,superadmin']);
    }

    private function models(): array
    {
        return [
            'Computer' => ComputerInventory::class,
            'General'  => GeneralEquipment::class,
            'Kitchen'  => KitchenEquipment::class,
            'Lab'      => LabEquipment::class,
            'Office'   => OfficeEquipment::class,
        ];
    }

    public function index(Request $request)
    {
        $type     = $request->get('type', 'all');
        $campusId = $request->get('campus_id');
        $locId    = $request->get('location_id');
        $status   = $request->get('status', 'all');
        $search   = $request->get('search');

        $allItems = collect();

        foreach ($this->models() as $label => $modelClass) {
            if ($type !== 'all' && $type !== $label) continue;

            $query = $modelClass::with(['location', 'campus', 'assignedUser'])
                ->when($campusId, fn($q) => $q->where('campus_id', $campusId))
                ->when($locId, fn($q) => $q->where('location_id', $locId))
                ->when($status !== 'all', fn($q) => $q->where('status', $status))
                ->when($search, function ($q) use ($search, $label) {
                    $nameCol = $label === 'Computer' ? 'computer_set_description'
                             : ($label === 'General' ? 'article' : 'equipment_name');
                    $q->where(function ($sub) use ($search, $nameCol) {
                        $sub->where($nameCol, 'like', "%$search%")
                            ->orWhere('serial_number', 'like', "%$search%")
                            ->orWhere('property_no', 'like', "%$search%")
                            ->orWhere('item_number', 'like', "%$search%");
                    });
                });

            $allItems = $allItems->merge($query->get());
        }

        // Sort merged collection by latest updated
        $allItems = $allItems->sortByDesc('updated_at')->values();

        // Manual pagination since we merged multiple Eloquent collections
        $perPage     = 50;
        $currentPage = $request->get('page', 1);
        $paged       = $allItems->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paged, $allItems->count(), $perPage, $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        // Stats
        $stats = [
            'total'       => 0,
            'assigned'    => 0,
            'unassigned'  => 0,
            'maintenance' => 0,
        ];
        foreach ($this->models() as $modelClass) {
            $stats['total']       += $modelClass::count();
            $stats['assigned']    += $modelClass::where('status', 'assigned')->count();
            $stats['unassigned']  += $modelClass::whereNull('location_id')->count();
            $stats['maintenance'] += $modelClass::where('status', 'maintenance')->count();
        }

        $campuses  = Campus::where('is_active', true)->get();
        $locations = Location::orderBy('location_name')->get(['id', 'location_name']);

        return view('pages.equipment', compact(
            'paginator', 'stats', 'campuses', 'locations',
            'type', 'campusId', 'locId', 'status', 'search'
        ));
    }
}