<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\ComputerInventory;
use App\Models\GeneralEquipment;
use App\Models\KitchenEquipment;
use App\Models\LabEquipment;
use App\Models\Location;
use App\Models\OfficeEquipment;
use Illuminate\Http\Request;

class EquipmentActionController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,superadmin']);
    }

    private function modelMap(): array
    {
        return [
            'computer' => ComputerInventory::class,
            'kitchen'  => KitchenEquipment::class,
            'office'   => OfficeEquipment::class,
            'lab'      => LabEquipment::class,
            'general'  => GeneralEquipment::class,
        ];
    }

    private function resolveModel(string $type, int $id)
    {
        $map = $this->modelMap();
        if (!isset($map[$type])) abort(404);

        return $map[$type]::with(['location', 'campus', 'assignedUser', 'condemnedByUser'])
            ->findOrFail($id);
    }

    private function displayName($item, string $type): string
    {
        return $type === 'computer' ? $item->computer_set_description
             : ($type === 'general' ? $item->article : $item->equipment_name);
    }

    public function show(string $type, int $id)
    {
        $item = $this->resolveModel($type, $id);
        $name = $this->displayName($item, $type);

        return view('pages.equipment_show', compact('item', 'type', 'name'));
    }

    public function edit(string $type, int $id)
    {
        $item      = $this->resolveModel($type, $id);
        $name      = $this->displayName($item, $type);
        $campuses  = Campus::where('is_active', true)->get();
        $locations = Location::where('campus_id', $item->campus_id)->orderBy('location_name')->get();

        return view('pages.equipment_edit', compact('item', 'type', 'name', 'campuses', 'locations'));
    }

    public function update(Request $request, string $type, int $id)
    {
        $item = $this->resolveModel($type, $id);

        $request->validate([
            'condition_status' => 'required|string',
            'campus_id'         => 'required|exists:campuses,id',
            'location_id'       => 'nullable|exists:locations,id',
        ]);

        $data = $request->only([
            'condition_status', 'campus_id', 'location_id',
            'brand', 'model', 'serial_number', 'property_no', 'cost',
            'description', 'unit', 'purchase_date', 'warranty_expiry',
            'processor', 'ram', 'storage', 'operating_system',
            'serial_number_monitor', 'serial_number_system',
            'calibration_date', 'next_calibration_date',
        ]);

        // Only keep keys that exist as columns on this model's table to avoid SQL errors
        $fillableColumns = \Illuminate\Support\Facades\Schema::getColumnListing($item->getTable());
        $data = array_intersect_key($data, array_flip($fillableColumns));

        $last  = $request->input('acc_last');
        $first = $request->input('acc_first');
        $mi    = $request->input('acc_mi');
        if ($last || $first) {
            $data['remarks'] = trim("{$last}, {$first} " . ($mi ? $mi . '.' : ''));
        }

        $data['status'] = $request->location_id ? 'assigned' : 'available';

        $item->update($data);

        \App\Models\ActivityLog::record('update', 'Equipment', "Updated equipment: {$this->displayName($item, $type)}", $type, $item->id);

        return redirect()->route('equipment.show', [$type, $id])->with('success', 'Equipment updated successfully.');
    }

    public function condemn(Request $request, string $type, int $id)
    {
        $item = $this->resolveModel($type, $id);

        $request->validate([
            'condemned_reason' => 'nullable|string|max:1000',
        ]);

        $item->update([
            'is_condemned'     => true,
            'condemned_date'   => now(),
            'condemned_reason' => $request->condemned_reason,
            'condemned_by'     => auth()->id(),
            'status'           => $type === 'computer' ? 'retired' : 'condemned',
        ]);

        \App\Models\ActivityLog::record('condemn', 'Equipment', "Condemned equipment: {$this->displayName($item, $type)}" . ($request->condemned_reason ? " — Reason: {$request->condemned_reason}" : ''), $type, $item->id);

        return back()->with('success', 'Equipment has been marked as condemned.');
    }

    public function destroy(Request $request, string $type, int $id)
    {
        $item = $this->resolveModel($type, $id);
        $name = $this->displayName($item, $type);

        $request->validate([
            'confirmation_text' => 'required|string',
        ]);

        $expected = 'Delete ' . $name;
        if ($request->confirmation_text !== $expected) {
            return back()->with('error', 'Confirmation text did not match. Equipment was not deleted.');
        }

        $item->delete();

        \App\Models\ActivityLog::record('delete', 'Equipment', "Permanently deleted equipment: {$name}", $type, $id);

        return redirect()->route('equipment')->with('success', "\"{$name}\" has been permanently deleted.");
    }

    public function report(string $type, int $id)
    {
        $item = $this->resolveModel($type, $id);
        $name = $this->displayName($item, $type);

        $logoPath = public_path('images/ucc.png');
        $logoBase64 = null;

        if (file_exists($logoPath)) {
            $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
        }

        $pdf = \PDF::loadView('pdf.equipment_report', compact('item', 'type', 'name', 'logoBase64'));

        return $pdf->stream('Equipment-Report-' . str_replace(' ', '-', $name) . '.pdf');
    }

    public function restore(string $type, int $id)
    {
        $item = $this->resolveModel($type, $id);

        if ($item->is_wasted) {
            return back()->with('error', 'This item has been transferred to waste and cannot be restored.');
        }

        $item->update([
            'is_condemned'     => false,
            'condemned_date'   => null,
            'condemned_reason' => null,
            'condemned_by'     => null,
            'status'           => $item->location_id ? 'assigned' : 'available',
        ]);

        \App\Models\ActivityLog::record(
            'restore', 'Equipment',
            "Restored equipment from condemned: {$this->displayName($item, $type)}",
            $type, $item->id
        );

        return back()->with('success', 'Equipment has been restored and returned to active inventory.');
    }

    public function transferToWaste(Request $request, string $type, int $id)
    {
        $item = $this->resolveModel($type, $id);

        if (!$item->is_condemned) {
            return back()->with('error', 'Only condemned items can be transferred to waste.');
        }

        $item->update([
            'is_wasted'   => true,
            'wasted_date' => now(),
            'wasted_by'   => auth()->id(),
            'status'      => 'retired',
        ]);

        \App\Models\ActivityLog::record(
            'waste', 'Equipment',
            "Transferred to waste (permanent): {$this->displayName($item, $type)}",
            $type, $item->id
        );

        return back()->with('success', 'Equipment has been permanently transferred to waste.');
    }
}