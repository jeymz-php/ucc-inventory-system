<?php

namespace App\Http\Controllers;

use App\Models\ComputerInventory;
use App\Models\GeneralEquipment;
use App\Models\KitchenEquipment;
use App\Models\LabEquipment;
use App\Models\Location;
use App\Models\OfficeEquipment;
use App\Models\User;
use Illuminate\Http\Request;

class EquipmentStoreController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,superadmin']);
    }

    // AJAX: get locations filtered by campus
    public function locationsByCampus(Request $request)
    {
        $locations = Location::where('campus_id', $request->campus_id)
            ->orderBy('location_name')
            ->get(['id', 'location_name']);

        return response()->json($locations);
    }

    private function resolveAccountablePerson(Request $request, string $equipmentType): array
    {
        $typeKey = 'accountable_type_' . $equipmentType;
        $accountableType = $request->input($typeKey, 'existing');

        $remarks = null;
        $assignedTo = null;

        if ($accountableType === 'existing' && $request->filled('user_id')) {
            $assignedTo = $request->input('user_id');
            $user = User::find($assignedTo);

            if ($user && !empty($user->name)) {
                $fullName = trim($user->name);

                if ($fullName === 'System Administrator' || $fullName === 'Administrator') {
                    $remarks = $fullName;
                } else {
                    $nameParts = explode(' ', $fullName);
                    if (count($nameParts) > 1) {
                        $lastName = array_pop($nameParts);
                        $firstName = implode(' ', $nameParts);
                        $remarks = $lastName . ', ' . $firstName;
                    } else {
                        $remarks = $fullName;
                    }
                }
            }
        } elseif ($accountableType === 'manual' && $request->filled('acc_first') && $request->filled('acc_last')) {
            $lastName  = trim($request->input('acc_last'));
            $firstName = trim($request->input('acc_first'));
            $mi        = trim($request->input('acc_mi'));

            $remarks = $lastName . ', ' . $firstName;
            if (!empty($mi)) {
                $remarks .= ' ' . rtrim($mi, '.') . '.';
            }
        }

        return [
            'remarks'     => $remarks,
            'assigned_to' => $assignedTo,
        ];
    }

    public function storeComputer(Request $request)
    {
        $request->validate([
            'article'        => 'required|string',
            'description'    => 'required|string',
            'processor'      => 'required|string',
            'ram'            => 'required|string',
            'storage'        => 'required|string',
            'unit'           => 'required|in:unit,box,pcs,lot',
            'campus_id'      => 'required|exists:campuses,id',
            'location_id'    => 'nullable|exists:locations,id',
            'condition_status' => 'required|string',
            'property_no'    => 'required|string',
        ]);

        $isPackage = $request->article === 'Computer Package';
        $accountable = $this->resolveAccountablePerson($request, 'Computer');

        ComputerInventory::create([
            'article'                  => $request->article,
            'computer_set_description' => $request->description,
            'description'              => $request->description,
            'serial_number'            => $isPackage ? null : $request->serial_number,
            'serial_number_monitor'    => $isPackage ? $request->serial_number_monitor : null,
            'serial_number_system'     => $isPackage ? $request->serial_number_system  : null,
            'processor'                => $request->processor,
            'ram'                      => $request->ram,
            'storage'                  => $request->storage,
            'unit'                     => $request->unit,
            'device_type'              => $isPackage ? 'Desktop' : ($request->article ?? 'Desktop'),
            'operating_system'         => $request->operating_system,
            'property_no'              => $request->property_no,
            'purchase_date'            => $request->has_purchase_date ? $request->purchase_date : null,
            'condition_status'         => $request->condition_status,
            'campus_id'                => $request->campus_id,
            'location_id'              => $request->location_id,
            'remarks'                  => $accountable['remarks'],
            'assigned_to'              => $accountable['assigned_to'],
            'cost'                     => $request->cost ?? 0,
            'status'                   => $request->location_id ? 'assigned' : 'available',
        ]);

        \App\Models\ActivityLog::record(
            'create', 'Equipment',
            "Added computer equipment: {$request->description}",
            'computer_inventory', null
        );

        return back()->with('success', 'Computer equipment added to inventory.');
    }

    public function storeKitchen(Request $request)
    {
        \App\Models\ActivityLog::record(
            'create', 'Equipment',
            "Added kitchen equipment: {$request->equipment_name}",
            'kitchen_equipment', null
        );

        $this->storeGeneric(KitchenEquipment::class, $request, 'equipment_name', 'Kitchen');
        return back()->with('success', 'Kitchen equipment added to inventory.');
    }

    public function storeOffice(Request $request)
    {
        \App\Models\ActivityLog::record(
            'create', 'Equipment',
            "Added office equipment: {$request->equipment_name}",
            'office_equipment', null
        );

        $this->storeGeneric(OfficeEquipment::class, $request, 'equipment_name', 'Office');
        return back()->with('success', 'Office equipment added to inventory.');
    }

    public function storeLab(Request $request)
    {
        $request->validate([
            'article' => 'required|string',
            'unit'    => 'required|in:unit,box,pcs,lot',
            'campus_id' => 'required|exists:campuses,id',
            'condition_status' => 'required|string',
        ]);

        $accountable = $this->resolveAccountablePerson($request, 'Lab');

        LabEquipment::create([
            'article'              => $request->article,
            'equipment_name'       => $request->article,
            'description'          => $request->description,
            'brand'                => $request->brand,
            'model'                => $request->model,
            'unit'                 => $request->unit,
            'serial_number'        => $request->serial_number,
            'property_no'          => $request->property_no,
            'condition_status'     => $request->condition_status,
            'campus_id'            => $request->campus_id,
            'location_id'          => $request->location_id,
            'calibration_date'     => $request->calibration_date,
            'purchase_date'        => $request->has_purchase_date ? $request->purchase_date : null,
            'remarks'              => $accountable['remarks'],
            'assigned_to'          => $accountable['assigned_to'],
            'cost'                 => $request->cost ?? 0,
            'status'               => $request->location_id ? 'assigned' : 'available',
        ]);

        \App\Models\ActivityLog::record(
            'create', 'Equipment',
            "Added laboratory equipment: {$request->article}",
            'laboratory_equipment', null
        );

        return back()->with('success', 'Laboratory equipment added to inventory.');
    }

    public function storeGeneral(Request $request)
    {
        $request->validate([
            'article'     => 'required|string',
            'unit'        => 'required|in:unit,box,pcs,lot',
            'campus_id'   => 'required|exists:campuses,id',
            'condition_status' => 'required|string',
            'property_no' => 'required|string',
        ]);

        $accountable = $this->resolveAccountablePerson($request, 'General');

        GeneralEquipment::create([
            'article'           => $request->article,
            'description'       => $request->description,
            'brand'             => $request->brand,
            'model'             => $request->model,
            'unit'              => $request->unit,
            'serial_number'     => $request->serial_number,
            'property_no'       => $request->property_no,
            'condition_status'  => $request->condition_status,
            'campus_id'         => $request->campus_id,
            'location_id'       => $request->location_id,
            'purchase_date'     => $request->has_purchase_date ? $request->purchase_date : null,
            'remarks'           => $accountable['remarks'],
            'assigned_to'       => $accountable['assigned_to'],
            'cost'              => $request->cost ?? 0,
            'status'            => $request->location_id ? 'assigned' : 'available',
        ]);

        \App\Models\ActivityLog::record(
            'create', 'Equipment',
            "Added general equipment: {$request->article}",
            'general_equipment', null
        );

        return back()->with('success', 'General equipment added to inventory.');
    }

    private function storeGeneric(string $modelClass, Request $request, string $nameField, string $equipmentType)
    {
        $request->validate([
            'article'   => 'required|string',
            'unit'      => 'required|in:unit,box,pcs,lot',
            'campus_id' => 'required|exists:campuses,id',
            'condition_status' => 'required|string',
        ]);

        $accountable = $this->resolveAccountablePerson($request, $equipmentType);

        $modelClass::create([
            'article'           => $request->article,
            $nameField          => $request->article,
            'description'       => $request->description,
            'brand'             => $request->brand,
            'model'             => $request->model,
            'unit'              => $request->unit,
            'serial_number'     => $request->serial_number,
            'property_no'       => $request->property_no,
            'condition_status'  => $request->condition_status,
            'campus_id'         => $request->campus_id,
            'location_id'       => $request->location_id,
            'purchase_date'     => $request->has_purchase_date ? $request->purchase_date : null,
            'remarks'           => $accountable['remarks'],
            'assigned_to'       => $accountable['assigned_to'],
            'cost'              => $request->cost ?? 0,
            'status'            => $request->location_id ? 'assigned' : 'available',
        ]);
    }
}