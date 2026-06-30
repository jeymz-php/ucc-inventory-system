<?php

namespace App\Http\Controllers;

use App\Models\ActivityLog;
use App\Models\Consumable;
use App\Models\ConsumableRequest;
use App\Models\ConsumableRequestItem;
use App\Models\ConsumableStockLog;
use Illuminate\Http\Request;

class ConsumableRequestController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $status = $request->get('status', 'all');
        $authUser = auth()->user();

        $query = ConsumableRequest::with(['items.consumable', 'campus', 'requester', 'reviewer'])
            ->when($status !== 'all', fn($q) => $q->where('status', $status));

        // Regular users only see their own requests
        if ($authUser->role === 'user') {
            $query->where('requested_by', $authUser->id);
        }

        $requests = $query->latest()->paginate(15)->withQueryString();

        if ($authUser->role === 'user') {
            return view('pages.user.my_requests', compact('requests', 'status'));
        }

        return view('pages.consumable_requests', compact('requests', 'status'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'items'                => 'required|array|min:1',
            'items.*.consumable_id'=> 'required|exists:consumables,id',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.purpose'      => 'required|string|max:255',
        ]);

        $authUser    = auth()->user();
        $autoApprove = in_array($authUser->role, ['admin', 'superadmin']);

        $consumableRequest = ConsumableRequest::create([
            'reference_no'         => ConsumableRequest::generateReferenceNo(),
            'recipient_last_name'  => explode(' ', $authUser->name)[count(explode(' ', $authUser->name)) - 1] ?? $authUser->name,
            'recipient_first_name' => explode(' ', $authUser->name)[0] ?? $authUser->name,
            'recipient_mi'         => null,
            'campus_id'            => $authUser->campus_id,
            'department'           => $authUser->department->department_name ?? 'N/A',
            'request_date'         => now(),
            'approved_by'          => 'REYNALDO H. CARANDANG JR.',
            'supply_officer'       => 'MARVIN Z. GERVACIO',
            'status'               => $autoApprove ? 'approved' : 'pending',
            'requested_by'         => $authUser->id,
            'reviewed_by'          => $autoApprove ? $authUser->id : null,
            'reviewed_at'          => $autoApprove ? now() : null,
        ]);

        foreach ($request->items as $itemData) {
            $item = ConsumableRequestItem::create([
                'consumable_request_id' => $consumableRequest->id,
                'consumable_id'         => $itemData['consumable_id'],
                'quantity'              => $itemData['quantity'],
                'purpose'               => $itemData['purpose'],
                'status'                => $autoApprove ? 'approved' : 'pending',
            ]);

            if ($autoApprove) {
                $this->deductStock($item, $authUser);
            }
        }

        ActivityLog::record(
            $autoApprove ? 'create_approved' : 'create',
            'Consumables',
            "Submitted consumable request {$consumableRequest->reference_no}" . ($autoApprove ? ' (auto-approved)' : ' (pending review)')
        );

        return back()->with('success', "Request {$consumableRequest->reference_no} submitted" . ($autoApprove ? ' and approved automatically.' : ' and is pending review.'));
    }

    public function show(ConsumableRequest $consumableRequest)
    {
        $consumableRequest->load(['items.consumable', 'campus', 'requester', 'reviewer']);
        return response()->json($consumableRequest);
    }

    // Admin/Super Admin reviews each item: approve or reject
    public function review(Request $request, ConsumableRequest $consumableRequest)
    {
        $request->validate([
            'items'                  => 'required|array',
            'items.*.id'             => 'required|exists:consumable_request_items,id',
            'items.*.decision'       => 'required|in:approved,rejected',
            'items.*.rejection_reason' => 'nullable|string|max:500',
        ]);

        $authUser = auth()->user();
        $allApproved = true;
        $allRejected = true;

        foreach ($request->items as $itemData) {
            $item = ConsumableRequestItem::find($itemData['id']);

            $item->update([
                'status'           => $itemData['decision'],
                'rejection_reason' => $itemData['decision'] === 'rejected' ? ($itemData['rejection_reason'] ?? null) : null,
            ]);

            if ($itemData['decision'] === 'approved') {
                $this->deductStock($item, $authUser);
                $allRejected = false;
            } else {
                $allApproved = false;
            }
        }

        $consumableRequest->update([
            'status'      => $allApproved ? 'approved' : ($allRejected ? 'rejected' : 'partial'),
            'reviewed_by' => $authUser->id,
            'reviewed_at' => now(),
        ]);

        ActivityLog::record('review', 'Consumables', "Reviewed request {$consumableRequest->reference_no}: {$consumableRequest->status}");

        return back()->with('success', 'Request reviewed successfully.');
    }

    public function update(Request $request, ConsumableRequest $consumableRequest)
    {
        $request->validate([
            'recipient_last_name'  => 'required|string|max:100',
            'recipient_first_name' => 'required|string|max:100',
            'department'           => 'required|string|max:150',
            'approved_by'          => 'nullable|string|max:150',
            'supply_officer'       => 'nullable|string|max:150',
            'status'               => 'required|in:pending,approved,rejected,partial',
            'items'                => 'required|array|min:1',
            'items.*.id'           => 'required|exists:consumable_request_items,id',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.purpose'      => 'required|string|max:255',
            'items.*.status'       => 'required|in:pending,approved,rejected',  
        ]);

        $authUser = auth()->user();

        $consumableRequest->update($request->only([
            'recipient_last_name', 'recipient_first_name', 'recipient_mi',
            'department', 'approved_by', 'supply_officer',
        ]));

        foreach ($request->items as $itemData) {
            $item = ConsumableRequestItem::find($itemData['id]']);
            if (!$item || $item->consumable_request_id !== $consumableRequest->id) continue;

            $wasApproved = $item->status === 'approved';
            $isNowApproved = $itemData['status'] === 'approved';

            if ($wasApproved && $isNowApproved && $item->quantity != $itemData['quantity']) {
                $diff = $itemData['quantity'] - $item->quantity;
                $consumable = $item->consumable;
                $previous = $consumable->current_stock;
                $newTotal = max(0, $previous - $diff);

                $consumable->update(['current_stock' => $newTotal]);

                ConsumableStockLog::create([
                    'consumable_id'  => $consumable->id,
                    'action'         => 'adjustment',
                    'change_amount'  => -$diff,
                    'previous_total' => $previous,
                    'new_total'      => $newTotal,
                    'user_id'        => $authUser->id,
                ]);
            }

            if (!$wasApproved && $isNowApproved) {
                $item->quantity = $itemData['quantity'];
                $this->deductStock($item, $authUser);
            }

            if (!$wasApproved && $isNowApproved) {
                $consumable = $item->consumable;
                $previous = $consumable->current_stock;
                $newTotal = max(0, $previous - $itemData['quantity']);

                $consumable->update(['current_stock' => $newTotal]);

                ConsumableStockLog::create([
                    'consumable_id'  => $consumable->id,
                    'action'         => 'deduction',
                    'change_amount'  => -$itemData['quantity'],
                    'previous_total' => $previous,
                    'new_total'      => $newTotal,
                    'user_id'        => $authUser->id,
                ]);
            }

            $item->update([
                'consumable_id'   => $itemData['consumable_id'],
                'quantity'        => $itemData['quantity'],
                'purpose'         => $itemData['purpose'],
                'status'          => $itemData['status'],
            ]);
        }

        $items = $consumableRequest->fresh()->items;
        $allApproved = $items->every(fn($i) => $i->status === 'approved');
        $allRejected = $items->every(fn($i) => $i->status === 'rejected');

        $finalStatus = $request->status;
        if ($finalStatus === 'approved' && !$allApproved && !$allRejected) {
            $finalStatus = 'partial';
        }

        $consumableRequest->update([
            'status'      => $finalStatus,
            'reviewed_by' => $authUser->id,
            'reviewed_at' => now(),
        ]);

        ActivityLog::record('update', 'Consumables', "Updated request {$consumableRequest->reference_no}"); 

        return back()->with('success', 'Request updated successfully.');
    }

    private function deductStock(ConsumableRequestItem $item, $user)
    {
        $consumable = $item->consumable;
        $previous   = $consumable->current_stock;
        $newTotal   = max(0, $previous - $item->quantity);

        $consumable->update(['current_stock' => $newTotal]);

        ConsumableStockLog::create([
            'consumable_id'  => $consumable->id,
            'action'         => 'deduction',
            'change_amount'  => -$item->quantity,
            'previous_total' => $previous,
            'new_total'      => $newTotal,
            'user_id'        => $user->id,
        ]);
    }

    public function report(ConsumableRequest $consumableRequest)
    {
        $consumableRequest->load(['items.consumable', 'campus', 'requester']);

        $headerLogoPath = public_path('images/ucc.png');
        $headerLogoBase64 = file_exists($headerLogoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($headerLogoPath)) : null;

        $footerLogoPath = public_path('images/caloocannewlogo.png');
        $footerLogoBase64 = file_exists($footerLogoPath) ? 'data:image/png;base64,' . base64_encode(file_get_contents($footerLogoPath)) : null;

        $pdf = \PDF::loadView('pdf.consumable_release_report', compact('consumableRequest', 'headerLogoBase64', 'footerLogoBase64'));

        return $pdf->stream('Release-Report-' . $consumableRequest->reference_no . '.pdf');
    }

    public function availableItems()
    {
        $items = Consumable::orderBy('item_name')->get(['id', 'item_name', 'unit', 'current_stock']);
        return response()->json($items);
    }
}