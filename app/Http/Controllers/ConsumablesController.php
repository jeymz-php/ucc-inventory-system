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
        $search       = $request->get('search');
        $categoryId   = $request->get('category_id');
        $stockStatus  = $request->get('stock_status');

        $query = Consumable::with('category')
            ->when($search, fn($q) => $q->where('item_name', 'like', "%$search%"))
            ->when($categoryId, fn($q) => $q->where('category_id', $categoryId));

        $items = $query->orderBy('item_name')->get();

        if ($stockStatus) {
            $items = $items->filter(fn($i) => $i->status === $stockStatus)->values();
        }

        $allItems = Consumable::all();
        $stats = [
            'total'    => $allItems->count(),
            'available'=> $allItems->filter(fn($i) => $i->status === 'available')->count(),
            'low'      => $allItems->filter(fn($i) => $i->status === 'low')->count(),
            'critical' => $allItems->filter(fn($i) => $i->status === 'critical')->count(),
            'categories' => ConsumableCategory::count(),
            'pending_requests' => ConsumableRequest::where('status', 'pending')->count(),
        ];

        $categories = ConsumableCategory::orderBy('name')->get();
        $campuses   = Campus::where('is_active', true)->get();

        return view('pages.consumables', compact(
            'items', 'stats', 'categories', 'campuses',
            'search', 'categoryId', 'stockStatus'
        ));
    }

    public function show(Consumable $consumable)
    {
        $logs = $consumable->stockLogs()->with('user')->take(10)->get();
        return response()->json([
            'item' => $consumable->load('category'),
            'logs' => $logs->map(fn($l) => [
                'date'     => $l->created_at->format('M d, Y h:i A'),
                'action'   => $l->action,
                'change'   => $l->change_amount,
                'previous' => $l->previous_total,
                'new'      => $l->new_total,
                'by'       => $l->user->name ?? 'System',
            ]),
        ]);
    }

    public function store(Request $request)
    {
        $request->validate([
            'items'             => 'required|array|min:1',
            'items.*.item_name' => 'required|string|max:200',
            'items.*.category'  => 'nullable|string|max:100',
            'items.*.quantity'  => 'required|integer|min:0',
            'items.*.max_stock' => 'nullable|integer|min:0',
            'items.*.unit'      => 'required|string|max:50',
            'items.*.brand'     => 'nullable|string|max:100',
        ]);

        foreach ($request->items as $itemData) {
            $category = null;
            if (!empty($itemData['category'])) {
                $category = ConsumableCategory::firstOrCreate(['name' => $itemData['category']]);
            }

            $consumable = Consumable::create([
                'item_name'    => $itemData['item_name'],
                'category_id'  => $category->id ?? null,
                'brand'        => $itemData['brand'] ?? null,
                'unit'         => $itemData['unit'],
                'current_stock'=> $itemData['quantity'],
                'max_stock'    => $itemData['max_stock'] ?? null,
                'id_code'      => Consumable::generateIdCode($itemData['item_name']),
                'campus_id'    => auth()->user()->campus_id,
            ]);

            ConsumableStockLog::create([
                'consumable_id'   => $consumable->id,
                'action'          => 'initial',
                'change_amount'   => $itemData['quantity'],
                'previous_total'  => 0,
                'new_total'       => $itemData['quantity'],
                'user_id'         => auth()->id(),
            ]);
        }

        \App\Models\ActivityLog::record('create', 'Consumables', count($request->items) . ' new consumable item(s) added to inventory.');

        return back()->with('success', count($request->items) . ' item(s) added successfully.');
    }

    public function update(Request $request, Consumable $consumable)
    {
        $request->validate([
            'item_name' => 'required|string|max:200',
            'category'  => 'nullable|string|max:100',
            'max_stock' => 'nullable|integer|min:0',
            'brand'     => 'nullable|string|max:100',
        ]);

        $category = null;
        if (!empty($request->category)) {
            $category = ConsumableCategory::firstOrCreate(['name' => $request->category]);
        }

        $consumable->update([
            'item_name'   => $request->item_name,
            'category_id' => $category->id ?? null,
            'max_stock'   => $request->max_stock,
            'brand'       => $request->brand,
        ]);

        return back()->with('success', 'Item updated successfully.');
    }

    public function destroy(Consumable $consumable)
    {
        $name = $consumable->item_name;
        $consumable->delete();

        \App\Models\ActivityLog::record('delete', 'Consumables', "Deleted consumable item: {$name}");

        return back()->with('success', 'Item deleted successfully.');
    }

    public function refill(Request $request, Consumable $consumable)
    {
        $request->validate(['amount' => 'required|integer|min:1']);

        $previous = $consumable->current_stock;
        $consumable->increment('current_stock', $request->amount);

        ConsumableStockLog::create([
            'consumable_id'  => $consumable->id,
            'action'         => 'refill',
            'change_amount'  => $request->amount,
            'previous_total' => $previous,
            'new_total'      => $consumable->current_stock,
            'user_id'        => auth()->id(),
        ]);

        \App\Models\ActivityLog::record('update', 'Consumables', "Refilled {$consumable->item_name} by {$request->amount} {$consumable->unit}");

        return back()->with('success', 'Item refilled successfully.');
    }
}