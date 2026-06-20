<?php

namespace App\Http\Controllers;

use App\Models\ComputerInventory;
use App\Models\GeneralEquipment;
use App\Models\KitchenEquipment;
use App\Models\LabEquipment;
use App\Models\OfficeEquipment;
use Illuminate\Http\Request;

class CondemnedStoreController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,superadmin']);
    }

    private function models(): array
    {
        return [
            'Computer' => ComputerInventory::class,
            'Kitchen'  => KitchenEquipment::class,
            'Office'   => OfficeEquipment::class,
            'Lab'      => LabEquipment::class,
            'General'  => GeneralEquipment::class,
        ];
    }

    public function store(Request $request)
    {
        $request->validate([
            'equipment_type'   => 'required|in:Computer,Kitchen,Office,Lab,General',
            'name'             => 'required|string|max:255',
            'campus_id'        => 'required|exists:campuses,id',
            'condemned_reason' => 'nullable|string|max:1000',
        ]);

        $modelClass = $this->models()[$request->equipment_type];

        $data = [
            'unit'              => 'unit',
            'condition_status'  => 'Damaged',
            'campus_id'         => $request->campus_id,
            'location_id'       => $request->location_id,
            'is_condemned'      => true,
            'condemned_date'    => now(),
            'condemned_reason'  => $request->condemned_reason,
            'condemned_by'      => auth()->id(),
            'status'            => 'condemned',
            'cost'              => $request->cost ?? 0,
        ];

        if ($request->equipment_type === 'Computer') {
            $data['computer_set_description'] = $request->name;
            $data['description']  = $request->name;
            $data['article']      = 'General';
            $data['device_type']  = 'Desktop';
            $data['processor']    = 'N/A';
            $data['ram']          = 'N/A';
            $data['storage']      = 'N/A';
            $data['serial_number'] = $request->serial_number ?? 'N/A';
            $data['status']        = 'retired';
        } elseif ($request->equipment_type === 'General') {
            $data['article']     = $request->name;
            $data['serial_number'] = $request->serial_number;
        } else {
            $data['article']        = $request->name;
            $data['equipment_name'] = $request->name;
            $data['serial_number']  = $request->serial_number;
        }

        $item = $modelClass::create($data);

        \App\Models\ActivityLog::record(
            'create', 'Equipment',
            "Added pre-condemned equipment: {$request->name}" . ($request->condemned_reason ? " — Reason: {$request->condemned_reason}" : ''),
            strtolower($request->equipment_type), $item->id
        );

        return back()->with('success', 'Condemned equipment item added successfully.');
    }
}