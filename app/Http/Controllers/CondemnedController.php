<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\ComputerInventory;
use App\Models\GeneralEquipment;
use App\Models\KitchenEquipment;
use App\Models\LabEquipment;
use App\Models\OfficeEquipment;
use Illuminate\Http\Request;

class CondemnedController extends Controller
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
        $search   = $request->get('search');

        $allItems = collect();

        foreach ($this->models() as $label => $modelClass) {
            if ($type !== 'all' && $type !== $label) continue;

            $query = $modelClass::with(['location', 'campus', 'condemnedByUser'])
                ->where('is_condemned', true)
                ->when($campusId, fn($q) => $q->where('campus_id', $campusId))
                ->when($search, function ($q) use ($search, $label) {
                    $nameCol = $label === 'Computer' ? 'computer_set_description'
                             : ($label === 'General' ? 'article' : 'equipment_name');
                    $q->where(function ($sub) use ($search, $nameCol) {
                        $sub->where($nameCol, 'like', "%$search%")
                            ->orWhere('serial_number', 'like', "%$search%")
                            ->orWhere('condemned_reason', 'like', "%$search%");
                    });
                });

            $allItems = $allItems->merge($query->get());
        }

        $allItems = $allItems->sortByDesc('condemned_date')->values();

        $perPage     = 50;
        $currentPage = $request->get('page', 1);
        $paged       = $allItems->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paged, $allItems->count(), $perPage, $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $totalCondemned = 0;
        foreach ($this->models() as $modelClass) {
            $totalCondemned += $modelClass::where('is_condemned', true)->count();
        }

        $campuses = Campus::where('is_active', true)->get();

        return view('pages.condemned', compact('paginator', 'totalCondemned', 'campuses', 'type', 'campusId', 'search'));
    }
}