<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\Consumable;
use App\Models\ConsumableCategory;
use App\Models\ConsumableRequest;
use App\Models\ConsumableStockLog;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class ConsumablesController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $search      = $request->get('search');
        $categoryId  = $request->get('category_id');
        $stockStatus = $request->get('stock_status');

        $query = Consumable::with('category')
            ->when($search, fn($q) => $q->where('item_name', 'like', "%$search%")
                ->orWhere('brand', 'like', "%$search%"))
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId));

        $items = $query->orderBy('item_name')->get();

        if ($stockStatus) {
            $items = $items->filter(fn($i) => $i->status === $stockStatus)->values();
        }

        $allItems = Consumable::all();
        $stats    = [
            'total'            => $allItems->count(),
            'available'        => $allItems->filter(fn($i) => $i->status === 'available')->count(),
            'low'              => $allItems->filter(fn($i) => $i->status === 'low')->count(),
            'critical'         => $allItems->filter(fn($i) => $i->status === 'critical')->count(),
            'out_of_stock'     => $allItems->filter(fn($i) => $i->current_stock <= 0)->count(),
            'categories'       => ConsumableCategory::count(),
            'pending_requests' => ConsumableRequest::where('status', 'pending')->count(),
        ];

        $categories = ConsumableCategory::orderBy('name')->get();
        $campuses   = Campus::where('is_active', true)->get();

        // Fetch total deductions per consumable item
        $deductionTotals = ConsumableStockLog::where('action', 'deduction')
            ->selectRaw('consumable_id, SUM(ABS(change_amount)) as total_deducted')
            ->groupBy('consumable_id')
            ->pluck('total_deducted', 'consumable_id');

        // Route to user view
        if (auth()->user()->role === 'user') {
            return view('pages.user.consumables', compact(
                'items', 'stats', 'categories'
            ));
        }

        return view('pages.consumables', compact(
            'items', 'stats', 'categories', 'campuses',
            'search', 'categoryId', 'stockStatus', 'deductionTotals'
        ));
    }

    public function show(Consumable $consumable)
    {
        $consumable->load('category');

        $logs = ConsumableStockLog::with('user')
            ->where('consumable_id', $consumable->id)
            ->latest()
            ->take(10)
            ->get()
            ->map(fn($l) => [
                'action'   => $l->action,
                'change'   => $l->change_amount,
                'previous' => $l->previous_total,
                'new'      => $l->new_total,
                'by'       => $l->user->name ?? 'System',
                'date'     => $l->created_at->format('M d, Y h:i A'),
            ]);

        return response()->json(['item' => $consumable, 'logs' => $logs]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'items'                  => 'required|array|min:1',
            'items.*.item_name'      => 'required|string|max:200',
            'items.*.quantity'       => 'required|integer|min:0',
            'items.*.unit'           => 'required|string|max:50',
        ]);

        foreach ($request->items as $itemData) {
            $category = null;
            if (!empty($itemData['category'])) {
                $category = ConsumableCategory::firstOrCreate(['name' => $itemData['category']]);
            }

            $idCode = 'CS-' . strtoupper(Str::random(6));

            $consumable = Consumable::create([
                'item_name'          => $itemData['item_name'],
                'category_id'        => $category?->id,
                'current_stock'      => $itemData['quantity'],
                'unit'               => $itemData['unit'],
                'brand'              => $itemData['brand'] ?? null,
                'id_code'            => $idCode,
                'critical_threshold' => 10,
                'low_threshold'      => 30,
            ]);

            if ($itemData['quantity'] > 0) {
                ConsumableStockLog::create([
                    'consumable_id'  => $consumable->id,
                    'action'         => 'initial',
                    'change_amount'  => $itemData['quantity'],
                    'previous_total' => 0,
                    'new_total'      => $itemData['quantity'],
                    'user_id'        => auth()->id(),
                ]);
            }
        }

        return back()->with('success', count($request->items) . ' item(s) added successfully.');
    }

    public function update(Request $request, Consumable $consumable)
    {
        $request->validate([
            'item_name' => 'required|string|max:200',
            'max_stock' => 'nullable|integer|min:0',
            'brand'     => 'nullable|string|max:100',
        ]);

        $category = null;
        if ($request->filled('category')) {
            $category = ConsumableCategory::firstOrCreate(['name' => $request->category]);
        }

        $consumable->update([
            'item_name'   => $request->item_name,
            'category_id' => $category?->id ?? $consumable->category_id,
            'max_stock'   => $request->max_stock,
            'brand'       => $request->brand,
        ]);

        return back()->with('success', 'Item updated successfully.');
    }

    public function destroy(Consumable $consumable)
    {
        $consumable->delete();
        return back()->with('success', 'Consumable item deleted.');
    }

    public function refill(Request $request, Consumable $consumable)
    {
        $request->validate(['amount' => 'required|integer|min:1']);

        $previous = $consumable->current_stock;
        $newTotal = $previous + $request->amount;

        $consumable->update(['current_stock' => $newTotal]);

        ConsumableStockLog::create([
            'consumable_id'  => $consumable->id,
            'action'         => 'refill',
            'change_amount'  => $request->amount,
            'previous_total' => $previous,
            'new_total'      => $newTotal,
            'user_id'        => auth()->id(),
        ]);

        return back()->with('success', "Refilled {$request->amount} {$consumable->unit} of {$consumable->item_name}.");
    }

    public function deduct(Request $request, Consumable $consumable)
    {
        $request->validate([
            'amount' => 'required|integer|min:1|max:' . $consumable->current_stock,
            'reason' => 'nullable|string|max:255',
        ]);

        $previous = $consumable->current_stock;
        $newTotal = max(0, $previous - $request->amount);

        $consumable->update(['current_stock' => $newTotal]);

        ConsumableStockLog::create([
            'consumable_id'  => $consumable->id,
            'action'         => 'deduction',
            'change_amount'  => -$request->amount,
            'previous_total' => $previous,
            'new_total'      => $newTotal,
            'user_id'        => auth()->id(),
        ]);

        return back()->with('success', "Deducted {$request->amount} {$consumable->unit} from {$consumable->item_name}.");
    }
}