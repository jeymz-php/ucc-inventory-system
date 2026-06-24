<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\ConsumableCategory;
use App\Models\ConsumableRequestItem;
use App\Models\Consumable;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ConsumableReportController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    private function buildQuery(Request $request)
    {
        $year       = $request->get('year', now()->year);
        $month      = $request->get('month');
        $categoryId = $request->get('category_id');
        $department = $request->get('department');

        return ConsumableRequestItem::with(['consumable.category', 'request'])
            ->where('status', 'approved')
            ->whereHas('request', function ($q) use ($year, $month, $department) {
                $q->whereYear('request_date', $year);
                if ($month) $q->whereMonth('request_date', $month);
                if ($department) $q->where('department', $department);
            })
            ->when($categoryId, fn($q) => $q->whereHas('consumable', fn($qq) => $qq->where('category_id', $categoryId)));
    }

    public function index(Request $request)
    {
        $items = $this->buildQuery($request)->get();

        $year       = $request->get('year', now()->year);
        $month      = $request->get('month');
        $categoryId = $request->get('category_id');
        $department = $request->get('department');

        // Monthly trend (Jan-Dec totals for selected year)
        $monthlyTrend = array_fill(1, 12, 0);
        foreach ($items as $item) {
            $m = $item->request->request_date->month;
            $monthlyTrend[$m] += $item->quantity;
        }

        // Top requested items
        $topItems = $items->groupBy(fn($i) => $i->consumable->item_name ?? 'Unknown')
            ->map(fn($group) => $group->sum('quantity'))
            ->sortDesc()
            ->take(8);

        // Grouped breakdown: Month > Category > Department > Items
        $breakdown = $items->groupBy(fn($i) => $i->consumable->category->name ?? 'Uncategorized')
            ->map(function ($categoryGroup) {
                return $categoryGroup->groupBy(fn($i) => $i->request->department)
                    ->map(function ($deptGroup) {
                        return [
                            'items' => $deptGroup->groupBy(fn($i) => $i->consumable->item_name ?? 'Unknown')
                                ->map(fn($g) => $g->sum('quantity')),
                            'requests' => $deptGroup->pluck('consumable_request_id')->unique()->count(),
                            'total_qty' => $deptGroup->sum('quantity'),
                        ];
                    });
            });

        $stats = [
            'total_consumed'  => $items->sum('quantity'),
            'total_requests'  => $items->pluck('consumable_request_id')->unique()->count(),
            'active_categories' => $items->pluck('consumable.category.name')->filter()->unique()->count(),
            'departments_served' => $items->pluck('request.department')->filter()->unique()->count(),
        ];

        $categories  = ConsumableCategory::orderBy('name')->get();
        $departments = \App\Models\Department::orderBy('department_name')->pluck('department_name');
        $years       = range(now()->year, now()->year - 3);

        // Stock health (overall, not date-filtered)
        $allConsumables = Consumable::with('category')->get();
        $stockByCategory = $allConsumables->groupBy(fn($c) => $c->category->name ?? 'Uncategorized')
            ->map(function ($group) {
                return [
                    'total_items'    => $group->count(),
                    'total_stock'    => $group->sum('current_stock'),
                    'avg_stock'      => round($group->avg('current_stock'), 1),
                    'in_stock'       => $group->filter(fn($c) => $c->status === 'available')->count(),
                    'low_stock'      => $group->filter(fn($c) => $c->status === 'low')->count(),
                    'critical_stock' => $group->filter(fn($c) => $c->status === 'critical')->count(),
                    'unique_brands'  => $group->pluck('brand')->filter()->unique()->count(),
                ];
            });

        return view('pages.consumable_reports', compact(
            'monthlyTrend', 'topItems', 'breakdown', 'stats',
            'categories', 'departments', 'years',
            'year', 'month', 'categoryId', 'department',
            'stockByCategory'
        ));
    }

    public function exportExcel(Request $request)
    {
        $items = $this->buildQuery($request)->get();

        return \Maatwebsite\Excel\Facades\Excel::download(
            new \App\Exports\ConsumptionReportExport($items),
            'Consumption-Report-' . now()->format('Y-m-d') . '.xlsx'
        );
    }

    public function exportPdf(Request $request)
    {
        $items = $this->buildQuery($request)->get();

        $year  = $request->get('year', now()->year);
        $month = $request->get('month');

        $breakdown = $items->groupBy(fn($i) => $i->consumable->category->name ?? 'Uncategorized')
            ->map(function ($categoryGroup) {
                return $categoryGroup->groupBy(fn($i) => $i->request->department)
                    ->map(function ($deptGroup) {
                        return [
                            'items'     => $deptGroup->groupBy(fn($i) => $i->consumable->item_name ?? 'Unknown')->map(fn($g) => $g->sum('quantity')),
                            'requests'  => $deptGroup->pluck('consumable_request_id')->unique()->count(),
                            'total_qty' => $deptGroup->sum('quantity'),
                        ];
                    });
            });

        $totalConsumed = $items->sum('quantity');

        $logoPath = public_path('images/ucc-logo.png');
        $logoBase64 = file_exists($logoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath)) : null;

        $pdf = \PDF::loadView('pdf.consumption_report', compact('breakdown', 'totalConsumed', 'year', 'month', 'logoBase64'));
        $pdf->setPaper('a4', 'landscape');

        return $pdf->stream('Consumption-Report-' . now()->format('Y-m-d') . '.pdf');
    }
}