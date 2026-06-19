<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\Location;
use App\Models\LocationType;
use Illuminate\Http\Request;

class LocationsController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,superadmin']);
    }

    public function index(Request $request)
    {
        $search       = $request->get('search');
        $campusId     = $request->get('campus_id');
        $typeId       = $request->get('location_type_id');

        $query = Location::with(['campus', 'locationType', 'facilitator'])
            ->withCount('equipment')
            ->when($search, fn($q) => $q->where('location_name', 'like', "%$search%")
                ->orWhere('description', 'like', "%$search%"))
            ->when($campusId, fn($q) => $q->where('campus_id', $campusId))
            ->when($typeId, fn($q) => $q->where('location_type_id', $typeId));

        $locations   = $query->orderBy('location_name')->paginate(15)->withQueryString();
        $campuses    = Campus::where('is_active', true)->get();
        $locationTypes = LocationType::when($campusId, fn($q) => $q->where('campus_id', $campusId))->get();

        return view('pages.locations', compact('locations', 'campuses', 'locationTypes', 'search', 'campusId', 'typeId'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'location_name'    => 'required|string|max:150',
            'location_type_id' => 'required|exists:location_types,id',
            'campus_id'        => 'required|exists:campuses,id',
            'capacity'         => 'nullable|integer|min:0',
            'description'      => 'nullable|string',
        ]);

        \App\Models\Location::create([
            'location_name'    => $request->location_name,
            'location_type_id' => $request->location_type_id,
            'campus_id'        => $request->campus_id,
            'capacity'         => $request->capacity ?? 0,
            'description'      => $request->description,
            'is_active'        => true,
        ]);

        return back()->with('success', 'New location added successfully.');
    }

    public function update(Request $request, \App\Models\Location $location)
    {
        $request->validate([
            'location_name'    => 'required|string|max:150',
            'location_type_id' => 'required|exists:location_types,id',
            'campus_id'        => 'required|exists:campuses,id',
            'capacity'         => 'nullable|integer|min:0',
            'description'      => 'nullable|string',
        ]);

        $location->update($request->only(['location_name', 'location_type_id', 'campus_id', 'capacity', 'description']));

        return back()->with('success', 'Location updated successfully.');
    }

    public function archive(\App\Models\Location $location)
    {
        $location->update(['is_active' => !$location->is_active]);
        $status = $location->is_active ? 'restored' : 'archived';

        return back()->with('success', "Location {$status} successfully.");
    }
}