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
}