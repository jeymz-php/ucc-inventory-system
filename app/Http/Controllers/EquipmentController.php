<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\ComputerInventory;
use App\Models\GeneralEquipment;
use App\Models\KitchenEquipment;
use App\Models\LabEquipment;
use App\Models\Location;
use App\Models\OfficeEquipment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;

class EquipmentController extends Controller
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
        $locId    = $request->get('location_id');
        $status   = $request->get('status', 'all');
        $search   = $request->get('search');

        $allItems = collect();

        foreach ($this->models() as $label => $modelClass) {
            if ($type !== 'all' && $type !== $label) continue;

            $query = $modelClass::with(['location', 'campus', 'assignedUser'])
                ->when($campusId, fn($q) => $q->where('campus_id', $campusId))
                ->when($locId, fn($q) => $q->where('location_id', $locId))
                ->when($status !== 'all', fn($q) => $q->where('status', $status))
                ->when($search, function ($q) use ($search, $label) {
                    $nameCol = $label === 'Computer' ? 'computer_set_description'
                             : ($label === 'General' ? 'article' : 'equipment_name');
                    $q->where(function ($sub) use ($search, $nameCol) {
                        $sub->where($nameCol, 'like', "%$search%")
                            ->orWhere('serial_number', 'like', "%$search%")
                            ->orWhere('property_no', 'like', "%$search%")
                            ->orWhere('item_number', 'like', "%$search%");
                    });
                });

            $allItems = $allItems->merge($query->get());
        }

        $allItems = $allItems->sortByDesc('updated_at')->values();

        $perPage     = 50;
        $currentPage = $request->get('page', 1);
        $paged       = $allItems->slice(($currentPage - 1) * $perPage, $perPage)->values();

        $paginator = new \Illuminate\Pagination\LengthAwarePaginator(
            $paged, $allItems->count(), $perPage, $currentPage,
            ['path' => $request->url(), 'query' => $request->query()]
        );

        $stats = [
            'total'       => 0,
            'assigned'    => 0,
            'unassigned'  => 0,
            'maintenance' => 0,
        ];
        foreach ($this->models() as $modelClass) {
            $stats['total']       += $modelClass::count();
            $stats['assigned']    += $modelClass::where('status', 'assigned')->count();
            $stats['unassigned']  += $modelClass::whereNull('location_id')->count();
            $stats['maintenance'] += $modelClass::where('status', 'maintenance')->count();
        }

        $campuses  = Campus::where('is_active', true)->get();
        $locations = Location::orderBy('location_name')->get(['id', 'location_name']);

        $equipModels = [
            \App\Models\ComputerInventory::class,
            \App\Models\KitchenEquipment::class,
            \App\Models\OfficeEquipment::class,
            \App\Models\LabEquipment::class,
            \App\Models\GeneralEquipment::class,
        ];

        $accountablePersons = collect();
        foreach ($equipModels as $modelClass) {
            $accountablePersons = $accountablePersons->merge(
                $modelClass::whereNotNull('remarks')->distinct()->pluck('remarks')
            );
        }
        $accountablePersons = $accountablePersons->unique()->filter()->sort()->values();

        // Fetch IMS users and attach a pre-computed remarks_format (Lastname, Firstname)
        // so the PAR modal dropdown can use it directly without Blade-side computation
        $imsUsers = User::orderBy('name')
            ->where('source', 'ims')
            ->get(['id', 'name'])
            ->map(function ($user) {
                $fullName = trim($user->name);

                // Handle special accounts
                if (in_array($fullName, ['System Administrator', 'Administrator'])) {
                    $user->remarks_format = $fullName;
                    return $user;
                }

                $nameParts = explode(' ', $fullName);

                if (count($nameParts) > 1) {
                    $lastName  = array_pop($nameParts);
                    $firstName = implode(' ', $nameParts);
                    $user->remarks_format = $lastName . ', ' . $firstName;
                } else {
                    $user->remarks_format = $fullName;
                }

                return $user;
            });

        // Remove IMS users whose remarks_format already appears in $accountablePersons
        // to avoid duplicates in the PAR dropdown
        $imsUserRemarks = $imsUsers->pluck('remarks_format')->toArray();
        $accountablePersons = $accountablePersons->filter(function ($person) use ($imsUserRemarks) {
            return !in_array($person, $imsUserRemarks);
        })->values();

        return view('pages.equipment', compact(
            'paginator', 'stats', 'campuses', 'locations',
            'type', 'campusId', 'locId', 'status', 'search',
            'accountablePersons', 'imsUsers'
        ));
    }

    /**
     * Store Computer Equipment Form Submission
     */
    public function storeComputer(Request $request)
    {
        $accountable = $this->resolveAccountablePerson($request, 'Computer');

        $serialNumber = $request->input('article') === 'Computer Package' ? null : $request->input('serial_number');

        ComputerInventory::create([
            'computer_set_description' => $request->input('description'),
            'article'                  => $request->input('article'),
            'serial_number'            => $serialNumber,
            'serial_number_monitor'    => $request->input('serial_number_monitor'),
            'serial_number_system'     => $request->input('serial_number_system'),
            'processor'                => $request->input('processor'),
            'ram'                      => $request->input('ram'),
            'storage'                  => $request->input('storage'),
            'unit'                     => $request->input('unit'),
            'campus_id'                => $request->input('campus_id'),
            'location_id'              => $request->input('location_id'),
            'has_purchase_date'        => $request->input('has_purchase_date', 0),
            'purchase_date'            => $request->input('purchase_date'),
            'operating_system'         => $request->input('operating_system'),
            'property_no'              => $request->input('property_no'),
            'cost'                     => $request->input('cost') ?? 0.00,
            'status'                   => $request->input('location_id') ? 'assigned' : 'available',
            'condition_status'         => $request->input('condition_status'),
            'remarks'                  => $accountable['remarks'],
            'assigned_to'              => $accountable['assigned_to'],
        ]);

        return redirect()->route('equipment')->with('success', 'Computer equipment added successfully.');
    }

    /**
     * Store Kitchen Equipment Form Submission
     */
    public function storeKitchen(Request $request)
    {
        $accountable = $this->resolveAccountablePerson($request, 'Kitchen');

        KitchenEquipment::create([
            'equipment_name'    => $request->input('article'),
            'description'       => $request->input('description'),
            'brand'             => $request->input('brand'),
            'model'             => $request->input('model'),
            'unit'              => $request->input('unit'),
            'serial_number'     => $request->input('serial_number'),
            'property_no'       => $request->input('property_no'),
            'cost'              => $request->input('cost') ?? 0.00,
            'campus_id'         => $request->input('campus_id'),
            'location_id'       => $request->input('location_id'),
            'has_purchase_date' => $request->input('has_purchase_date', 0),
            'purchase_date'     => $request->input('purchase_date'),
            'status'            => $request->input('location_id') ? 'assigned' : 'available',
            'condition_status'  => $request->input('condition_status'),
            'remarks'           => $accountable['remarks'],
            'assigned_to'       => $accountable['assigned_to'],
        ]);

        return redirect()->route('equipment')->with('success', 'Kitchen equipment added successfully.');
    }

    /**
     * Store Office Equipment Form Submission
     */
    public function storeOffice(Request $request)
    {
        $accountable = $this->resolveAccountablePerson($request, 'Office');

        OfficeEquipment::create([
            'equipment_name'    => $request->input('article'),
            'description'       => $request->input('description'),
            'brand'             => $request->input('brand'),
            'model'             => $request->input('model'),
            'unit'              => $request->input('unit'),
            'serial_number'     => $request->input('serial_number'),
            'property_no'       => $request->input('property_no'),
            'cost'              => $request->input('cost') ?? 0.00,
            'campus_id'         => $request->input('campus_id'),
            'location_id'       => $request->input('location_id'),
            'has_purchase_date' => $request->input('has_purchase_date', 0),
            'purchase_date'     => $request->input('purchase_date'),
            'status'            => $request->input('location_id') ? 'assigned' : 'available',
            'condition_status'  => $request->input('condition_status'),
            'remarks'           => $accountable['remarks'],
            'assigned_to'       => $accountable['assigned_to'],
        ]);

        return redirect()->route('equipment')->with('success', 'Office equipment added successfully.');
    }

    /**
     * Store Laboratory Equipment Form Submission
     */
    public function storeLab(Request $request)
    {
        $accountable = $this->resolveAccountablePerson($request, 'Lab');

        LabEquipment::create([
            'equipment_name'    => $request->input('article'),
            'description'       => $request->input('description'),
            'brand'             => $request->input('brand'),
            'model'             => $request->input('model'),
            'unit'              => $request->input('unit'),
            'serial_number'     => $request->input('serial_number'),
            'property_no'       => $request->input('property_no'),
            'cost'              => $request->input('cost') ?? 0.00,
            'campus_id'         => $request->input('campus_id'),
            'location_id'       => $request->input('location_id'),
            'has_purchase_date' => $request->input('has_purchase_date', 0),
            'purchase_date'     => $request->input('purchase_date'),
            'calibration_date'  => $request->input('calibration_date'),
            'status'            => $request->input('location_id') ? 'assigned' : 'available',
            'condition_status'  => $request->input('condition_status'),
            'remarks'           => $accountable['remarks'],
            'assigned_to'       => $accountable['assigned_to'],
        ]);

        return redirect()->route('equipment')->with('success', 'Laboratory equipment added successfully.');
    }

    /**
     * Store General Equipment Form Submission
     */
    public function storeGeneral(Request $request)
    {
        $accountable = $this->resolveAccountablePerson($request, 'General');

        GeneralEquipment::create([
            'article'           => $request->input('article'),
            'description'       => $request->input('description'),
            'brand'             => $request->input('brand'),
            'model'             => $request->input('model'),
            'unit'              => $request->input('unit'),
            'serial_number'     => $request->input('serial_number'),
            'property_no'       => $request->input('property_no'),
            'cost'              => $request->input('cost') ?? 0.00,
            'campus_id'         => $request->input('campus_id'),
            'location_id'       => $request->input('location_id'),
            'has_purchase_date' => $request->input('has_purchase_date', 0),
            'purchase_date'     => $request->input('purchase_date'),
            'status'            => $request->input('location_id') ? 'assigned' : 'available',
            'condition_status'  => $request->input('condition_status'),
            'remarks'           => $accountable['remarks'],
            'assigned_to'       => $accountable['assigned_to'],
        ]);

        return redirect()->route('equipment')->with('success', 'General equipment added successfully.');
    }

    /**
     * Helper to determine, format, and split accountable user strings
     * while mapping them directly to database schema columns.
     */
    private function resolveAccountablePerson(Request $request, string $equipmentType): array
    {
        $typeKey         = 'accountable_type_' . $equipmentType;
        $accountableType = $request->input($typeKey, 'existing');

        $remarks    = null;
        $assignedTo = null;

        if ($accountableType === 'existing' && $request->filled('user_id')) {
            $assignedTo = $request->input('user_id');
            $user       = User::find($assignedTo);

            if ($user && !empty($user->name)) {
                $fullName = trim($user->name);

                if (in_array($fullName, ['System Administrator', 'Administrator'])) {
                    $remarks = $fullName;
                } else {
                    $nameParts = explode(' ', $fullName);
                    if (count($nameParts) > 1) {
                        $lastName  = array_pop($nameParts);
                        $firstName = implode(' ', $nameParts);
                        $remarks   = $lastName . ', ' . $firstName;
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
}