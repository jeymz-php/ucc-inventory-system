<?php

namespace App\Http\Controllers;

use App\Models\ComputerInventory;
use App\Models\GeneralEquipment;
use App\Models\KitchenEquipment;
use App\Models\LabEquipment;
use App\Models\OfficeEquipment;
use Illuminate\Http\Request;

class MyEquipmentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function modelMap(): array
    {
        return [
            'Computer' => ComputerInventory::class,
            'Kitchen'  => KitchenEquipment::class,
            'Office'   => OfficeEquipment::class,
            'Lab'      => LabEquipment::class,
            'General'  => GeneralEquipment::class,
        ];
    }

    private function displayName($item, string $type): string
    {
        return $type === 'Computer'
            ? $item->computer_set_description
            : ($type === 'General' ? $item->article : $item->equipment_name);
    }

    public function index(Request $request)
    {
        if (auth()->user()->role !== 'user') {
            return redirect()->route('equipment');
        }

        $type   = $request->get('type', 'all');
        $status = $request->get('status', 'all');
        $search = $request->get('search');

        $allItems = collect();
        $stats = [
            'total'       => 0,
            'assigned'    => 0,
            'maintenance' => 0,
            'by_type'     => [],
        ];

        foreach ($this->modelMap() as $label => $modelClass) {
            if ($type !== 'all' && $type !== $label) {
                continue;
            }

            $query = $modelClass::with(['location', 'campus'])
                ->where('assigned_to', auth()->id())
                ->where('is_condemned', false)
                ->when($status !== 'all', fn ($q) => $q->where('status', $status))
                ->when($search, function ($q) use ($search, $label) {
                    $nameCol = $label === 'Computer' ? 'computer_set_description'
                             : ($label === 'General' ? 'article' : 'equipment_name');
                    $q->where(function ($sub) use ($search, $nameCol) {
                        $sub->where($nameCol, 'like', "%{$search}%")
                            ->orWhere('serial_number', 'like', "%{$search}%")
                            ->orWhere('property_no', 'like', "%{$search}%");
                    });
                });

            $items = $query->get()->each(function ($item) use ($label) {
                $item->equipment_type = $label;
                $item->type_slug = strtolower($label);
            });

            $allItems = $allItems->merge($items);

            $baseQuery = $modelClass::where('assigned_to', auth()->id())->where('is_condemned', false);
            $stats['total']       += (clone $baseQuery)->count();
            $stats['assigned']    += (clone $baseQuery)->where('status', 'assigned')->count();
            $stats['maintenance'] += (clone $baseQuery)->where('status', 'maintenance')->count();
            $stats['by_type'][$label] = (clone $baseQuery)->count();
        }

        $allItems = $allItems->sortByDesc('updated_at')->values();

        $perPage     = 25;
        $currentPage = $request->get('page', 1);
        $paged       = $allItems->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paged,
            $allItems->count(),
            $perPage,
            $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        return view('pages.user.my_equipment', compact('paginator', 'stats', 'type', 'status', 'search'));
    }

    private function labelFromSlug(string $slug): ?string
    {
        $map = [
            'computer' => 'Computer',
            'kitchen'  => 'Kitchen',
            'office'   => 'Office',
            'lab'      => 'Lab',
            'general'  => 'General',
        ];

        return $map[$slug] ?? null;
    }

    public function show(string $type, int $id)
    {
        if (auth()->user()->role !== 'user') {
            return redirect()->route('equipment.show', [$type, $id]);
        }

        $label = $this->labelFromSlug($type);
        if (!$label) {
            abort(404);
        }

        $modelClass = $this->modelMap()[$label];
        $item = $modelClass::with(['location', 'campus', 'assignedUser'])
            ->where('assigned_to', auth()->id())
            ->findOrFail($id);

        $name = $this->displayName($item, $label);

        return view('pages.user.my_equipment_show', compact('item', 'type', 'name', 'label'));
    }
}
