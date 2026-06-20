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

        $locationTypes = $query->get();

        $models = [
            \App\Models\ComputerInventory::class,
            \App\Models\KitchenEquipment::class,
            \App\Models\OfficeEquipment::class,
            \App\Models\LabEquipment::class,
            \App\Models\GeneralEquipment::class,
        ];

        foreach ($locationTypes as $type) {
            $locationIds = $type->locations()->pluck('id');

            $count = 0;
            foreach ($models as $modelClass) {
                $count += $modelClass::whereIn('location_id', $locationIds)->count();
            }
            $type->equipment_count = $count;
        }

        $totalEquipment = 0;
        foreach ($models as $modelClass) {
            $totalEquipment += $modelClass::count();
        }

        $stats = [
            'categories' => LocationType::count(),
            'rooms'      => Location::count(),
            'equipment'  => $totalEquipment,
        ];

        return view('pages.inventory', compact('locationTypes', 'campuses', 'stats', 'campusId', 'search'));
    }

    // Show rooms within a location type
    public function show(LocationType $locationType)
    {
        $locations = $locationType->locations()->orderBy('location_name')->get();

        $models = [
            \App\Models\ComputerInventory::class,
            \App\Models\KitchenEquipment::class,
            \App\Models\OfficeEquipment::class,
            \App\Models\LabEquipment::class,
            \App\Models\GeneralEquipment::class,
        ];

        foreach ($locations as $loc) {
            $count = 0;
            foreach ($models as $modelClass) {
                $count += $modelClass::where('location_id', $loc->id)->count();
            }
            $loc->equipment_count = $count;
        }

        return view('pages.inventory_detail', compact('locationType', 'locations'));
    }

    // New method: show equipment inside a specific room
    public function showLocation(\App\Models\Location $location)
    {
        $models = [
            'Computer' => \App\Models\ComputerInventory::class,
            'Kitchen'  => \App\Models\KitchenEquipment::class,
            'Office'   => \App\Models\OfficeEquipment::class,
            'Lab'      => \App\Models\LabEquipment::class,
            'General'  => \App\Models\GeneralEquipment::class,
        ];

        $equipment = collect();
        foreach ($models as $label => $modelClass) {
            $items = $modelClass::where('location_id', $location->id)->get();
            $equipment = $equipment->merge($items);
        }

        $equipment = $equipment->sortByDesc('updated_at')->values();

        return view('pages.location_equipment', compact('location', 'equipment'));
    }

    public function storeLocationType(Request $request)
    {
        $request->validate([
            'type_name'   => 'required|string|max:100',
            'type_code'   => 'required|string|max:50',
            'campus_id'   => 'required|exists:campuses,id',
            'description' => 'nullable|string',
            'icon_class'  => 'nullable|string',
        ]);

        \App\Models\LocationType::create([
            'type_name'       => $request->type_name,
            'type_code'       => $request->type_code,
            'campus_id'       => $request->campus_id,
            'description'     => $request->description,
            'icon_class'      => $request->icon_class ?? 'fa-building',
            'color_primary'   => '#1a6b3a',
            'color_secondary' => '#20c997',
            'equipment_label' => 'Equipment',
            'manager_title'   => 'Manager',
            'is_active'       => true,
        ]);

        \App\Models\ActivityLog::record('create', 'Category', "Added new category: {$request->type_name}", 'location_type', null);

        return back()->with('success', 'New category added successfully.');
    }

    public function updateLocationType(Request $request, \App\Models\LocationType $locationType)
    {
        $request->validate([
            'type_name'   => 'required|string|max:100',
            'type_code'   => 'required|string|max:50',
            'campus_id'   => 'required|exists:campuses,id',
            'description' => 'nullable|string',
            'icon_class'  => 'nullable|string',
        ]);

        $locationType->update($request->only(['type_name', 'type_code', 'campus_id', 'description', 'icon_class']));

        \App\Models\ActivityLog::record('update', 'Category', "Updated category: {$locationType->type_name}", 'location_type', $locationType->id);

        return back()->with('success', 'Category updated successfully.');
    }

    public function toggleLocationType(\App\Models\LocationType $locationType)
    {
        $locationType->update(['is_active' => !$locationType->is_active]);
        $status = $locationType->is_active ? 'activated' : 'deactivated';

        \App\Models\ActivityLog::record($locationType->is_active ? 'activate' : 'deactivate', 'Category', "{$status} category: {$locationType->type_name}", 'location_type', $locationType->id);

        return back()->with('success', "Category {$status} successfully.");
    }

    public function destroyLocationType(\App\Models\LocationType $locationType)
    {
        if ($locationType->locations()->count() > 0) {
            return back()->with('error', 'Cannot delete this category — it still has rooms assigned to it. Remove or reassign the rooms first.');
        }

        $locationType->delete();

        \App\Models\ActivityLog::record('delete', 'Category', "Deleted category: {$locationType->type_name}", 'location_type', $locationType->id);

        return back()->with('success', 'Category removed successfully.');
    }

    public function locationTypesByCampus(Request $request)
    {
        $types = \App\Models\LocationType::where('campus_id', $request->campus_id)
            ->where('is_active', true)
            ->orderBy('type_name')
            ->get(['id', 'type_name']);

        return response()->json($types);
    }
}