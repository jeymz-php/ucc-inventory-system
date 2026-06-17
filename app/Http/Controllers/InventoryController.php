<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\Equipment;
use App\Models\Location;
use App\Models\LocationType;
use Illuminate\Http\Request;

class InventoryController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,superadmin']);
    }

    // Main Inventory page — shows location types as cards
    public function index(Request $request)
    {
        $campusId = $request->get('campus_id');
        $search   = $request->get('search');

        $campuses = Campus::where('is_active', true)->get();

        $query = LocationType::with('campus')
            ->withCount('locations')
            ->when($campusId, fn($q) => $q->where('campus_id', $campusId))
            ->when($search, fn($q) => $q->where('type_name', 'like', "%$search%"));

        $locationTypes = $query->get()->map(function ($type) {
            $type->equipment_count = Equipment::whereIn('location_id', $type->locations()->pluck('id'))->count();
            return $type;
        });

        $stats = [
            'categories' => LocationType::count(),
            'rooms'      => Location::count(),
            'equipment'  => Equipment::count(),
        ];

        return view('pages.inventory', compact('locationTypes', 'campuses', 'stats', 'campusId', 'search'));
    }

    // Show rooms within a location type
    public function show(LocationType $locationType)
    {
        $locations = $locationType->locations()
            ->withCount('equipment')
            ->orderBy('location_name')
            ->get();

        return view('pages.inventory_detail', compact('locationType', 'locations'));
    }
}