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

    /**
     * Parse a full name into first_name, last_name, and mi.
     * Handles Filipino names: "Juan Dela Cruz" → first: Juan, last: Dela Cruz
     * Also handles: "Maria Santos" → first: Maria, last: Santos
     *               "Jose P. Rizal" → first: Jose, mi: P., last: Rizal
     */
    public function parseRecipientName(?string $fullName): array
    {
        $trimmed = trim((string) $fullName);

        if ($trimmed === '') {
            return ['first_name' => '', 'mi' => null, 'last_name' => ''];
        }

        $parts = preg_split('/\s+/', $trimmed);

        if (count($parts) === 1) {
            return ['first_name' => $trimmed, 'mi' => null, 'last_name' => ''];
        }

        // Last part is always last name
        $lastName  = array_pop($parts);

        // Check if second-to-last is a middle initial (1-2 chars ending with optional period)
        $mi = null;
        if (count($parts) >= 2) {
            $possibleMi = end($parts);
            if (preg_match('/^[A-Za-z]{1,2}\.?$/', $possibleMi)) {
                $mi = array_pop($parts) . (str_ends_with($possibleMi, '.') ? '' : '.');
            }
        }

        $firstName = implode(' ', $parts);

        return [
            'first_name' => $firstName,
            'mi'         => $mi,
            'last_name'  => $lastName,
        ];
    }

    public function index(Request $request)
    {
        $status   = $request->get('status', 'all');
        $authUser = auth()->user();

        $query = ConsumableRequest::with(['items.consumable', 'campus', 'requester', 'reviewer'])
            ->when($status !== 'all', fn($q) => $q->where('status', $status));

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
            'items'                 => 'required|array|min:1',
            'items.*.consumable_id' => 'required|exists:consumables,id',
            'items.*.quantity'      => 'required|integer|min:1',
            'items.*.purpose'       => 'required|string|max:255',
        ]);

        $authUser      = auth()->user();
        $autoApprove   = in_array($authUser->role, ['admin', 'superadmin']);
        $parsed        = $this->parseRecipientName($authUser->name);

        $consumableRequest = ConsumableRequest::create([
            'reference_no'         => ConsumableRequest::generateReferenceNo(),
            'recipient_first_name' => $parsed['first_name'],
            'recipient_mi'         => $parsed['mi'],
            'recipient_last_name'  => $parsed['last_name'],
            'campus_id'            => $authUser->campus_id,
            'department'           => $authUser->department->department_name ?? 'N/A',
            'request_date'         => now(),
            'approved_by'          => 'REYNALDO H. CARANDANG JR.',
            'supply_officer'       => 'MARVIN Z. GERVACIO',
            'status'               => $autoApprove ? 'approved' : 'pending',
            'requested_by'         => $authUser->id,
            'reviewed_by'          => $autoApprove ? $authUser->id : null,
            'reviewed_at'          => $autoApprove ? now() : null,
            'source'               => 'ims',
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
            "Submitted consumable request {$consumableRequest->reference_no}" .
            ($autoApprove ? ' (auto-approved)' : ' (pending review)') . ' [source: IMS]'
        );

        return back()->with('success',
            "Request {$consumableRequest->reference_no} submitted" .
            ($autoApprove ? ' and approved automatically.' : ' and is pending review.')
        );
    }

    public function show(ConsumableRequest $consumableRequest)
    {
        $consumableRequest->load(['items.consumable', 'campus', 'requester', 'reviewer']);
        return response()->json($consumableRequest);
    }

    public function review(Request $request, ConsumableRequest $consumableRequest)
    {
        $request->validate([
            'items'                    => 'required|array',
            'items.*.id'               => 'required|exists:consumable_request_items,id',
            'items.*.decision'         => 'required|in:approved,rejected',
            'items.*.rejection_reason' => 'nullable|string|max:500',
        ]);

        $authUser    = auth()->user();
        $allApproved = true;
        $allRejected = true;

        foreach ($request->items as $itemData) {
            $item = ConsumableRequestItem::find($itemData['id']);

            $item->update([
                'status'           => $itemData['decision'],
                'rejection_reason' => $itemData['decision'] === 'rejected'
                    ? ($itemData['rejection_reason'] ?? null)
                    : null,
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

        ActivityLog::record('review', 'Consumables',
            "Reviewed request {$consumableRequest->reference_no}: {$consumableRequest->status}"
        );

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
            'items.*.id'           => 'nullable|exists:consumable_request_items,id',
            'items.*.consumable_id'=> 'required|exists:consumables,id',
            'items.*.quantity'     => 'required|integer|min:1',
            'items.*.purpose'      => 'nullable|string|max:255',
            'items.*.status'       => 'required|in:pending,approved,rejected',
        ]);

        $authUser = auth()->user();

        $consumableRequest->update($request->only([
            'recipient_last_name', 'recipient_first_name', 'recipient_mi',
            'department', 'approved_by', 'supply_officer',
        ]));

        foreach ($request->items as $itemData) {
            if (empty($itemData['id'])) continue;

            $item = ConsumableRequestItem::find($itemData['id']);
            if (!$item || $item->consumable_request_id !== $consumableRequest->id) continue;

            $wasApproved   = $item->status === 'approved';
            $isNowApproved = $itemData['status'] === 'approved';

            // Quantity change on already-approved item → adjust stock
            if ($wasApproved && $isNowApproved && $item->quantity != $itemData['quantity']) {
                $diff       = $itemData['quantity'] - $item->quantity;
                $consumable = $item->consumable;
                $previous   = $consumable->current_stock;
                $newTotal   = max(0, $previous - $diff);

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

            // Newly approved → deduct stock
            if (!$wasApproved && $isNowApproved) {
                $item->quantity = $itemData['quantity'];
                $this->deductStock($item, $authUser);
            }

            // Was approved, now un-approved → restock
            if ($wasApproved && !$isNowApproved) {
                $consumable = $item->consumable;
                $previous   = $consumable->current_stock;
                $newTotal   = $previous + $item->quantity;

                $consumable->update(['current_stock' => $newTotal]);
                ConsumableStockLog::create([
                    'consumable_id'  => $consumable->id,
                    'action'         => 'adjustment',
                    'change_amount'  => $item->quantity,
                    'previous_total' => $previous,
                    'new_total'      => $newTotal,
                    'user_id'        => $authUser->id,
                ]);
            }

            $item->update([
                'consumable_id' => $itemData['consumable_id'],
                'quantity'      => $itemData['quantity'],
                'purpose'       => $itemData['purpose'] ?? null,
                'status'        => $itemData['status'],
            ]);
        }

        $items       = $consumableRequest->fresh()->items;
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

        ActivityLog::record('update', 'Consumables',
            "Edited request {$consumableRequest->reference_no}"
        );

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
        $payload = $this->buildReportPayload($consumableRequest, false);
        $pdf = \PDF::loadView('pdf.consumable_release_report', $payload);
        return $pdf->stream('Release-Report-' . $consumableRequest->reference_no . '.pdf');
    }

    public function blankReport()
    {
        $payload = $this->buildReportPayload(null, true);
        $pdf = \PDF::loadView('pdf.consumable_release_report', $payload);
        return $pdf->stream('Blank-Consumable-Release-Receipt.pdf');
    }

    private function buildReportPayload(?ConsumableRequest $consumableRequest, bool $blankReceipt): array
    {
        $publicRoot       = dirname(__DIR__, 3) . '/public';
        $headerLogoPath   = $publicRoot . '/images/ucc.png';
        $headerLogoBase64 = file_exists($headerLogoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($headerLogoPath))
            : null;

        $footerLogoPath   = $publicRoot . '/images/caloocannewlogo.png';
        $footerLogoBase64 = file_exists($footerLogoPath)
            ? 'data:image/png;base64,' . base64_encode(file_get_contents($footerLogoPath))
            : null;

        $request = $consumableRequest;

        if ($blankReceipt) {
            $request = new class {
                public $reference_no          = '';
                public $recipient_first_name  = '';
                public $recipient_last_name   = '';
                public $recipient_mi          = '';
                public $department            = '';
                public $approved_by           = '';
                public $supply_officer        = '';
                public $request_date;
                public $items;
                public $campus;
                public $requester;

                public function __construct()
                {
                    $this->request_date = now();
                    $this->items        = collect([]);
                    $this->campus       = null;
                    $this->requester    = new class { public $name = ''; };
                }

                public function getRecipientNameAttribute(): string
                {
                    return trim("{$this->recipient_first_name} {$this->recipient_mi} {$this->recipient_last_name}");
                }
            };
        } else {
            $request->loadMissing(['items.consumable', 'campus', 'requester']);
        }

        return [
            'consumableRequest' => $request,
            'headerLogoBase64'  => $headerLogoBase64,
            'footerLogoBase64'  => $footerLogoBase64,
            'blankReceipt'      => $blankReceipt,
            'referenceNo'       => $blankReceipt ? '' : ($request->reference_no ?? ''),
            'recipientName'     => $blankReceipt ? '' : ($request->recipient_name ?? ''),
        ];
    }

    public function availableItems()
    {
        $items = Consumable::orderBy('item_name')->get(['id', 'item_name', 'unit', 'current_stock']);
        return response()->json($items);
    }
}