<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\User;
use Illuminate\Http\Request;

class CampusController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,superadmin']);
    }

    public function index(Request $request)
    {
        $search = $request->get('search');

        $campuses = Campus::when($search, fn($q) =>
            $q->where('name', 'like', "%$search%")
              ->orWhere('code', 'like', "%$search%")
        )
        ->orderBy('name')
        ->paginate(20)
        ->withQueryString();

        $total  = Campus::count();
        $active = Campus::where('is_active', true)->count();

        return view('pages.manage.campuses', compact('campuses', 'search', 'total', 'active'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:campuses,name',
            'code' => 'required|string|max:20|unique:campuses,code',
        ]);

        Campus::create([
            'name'      => trim($request->name),
            'code'      => strtoupper(trim($request->code)),
            'is_active' => true,
        ]);

        return back()->with('success', "Campus \"{$request->name}\" added successfully.");
    }

    public function update(Request $request, Campus $campus)
    {
        $request->validate([
            'name' => 'required|string|max:100|unique:campuses,name,' . $campus->id,
            'code' => 'required|string|max:20|unique:campuses,code,' . $campus->id,
        ]);

        $campus->update([
            'name' => trim($request->name),
            'code' => strtoupper(trim($request->code)),
        ]);

        return back()->with('success', "Campus updated successfully.");
    }

    public function toggleActive(Campus $campus)
    {
        $campus->update(['is_active' => !$campus->is_active]);

        $status = $campus->fresh()->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Campus \"{$campus->name}\" {$status}.");
    }

    public function destroy(Campus $campus)
    {
        // Check if any users are assigned to this campus
        $userCount = User::where('campus_id', $campus->id)->count();
        if ($userCount > 0) {
            return back()->with('error', "Cannot delete \"{$campus->name}\" — {$userCount} user(s) are assigned to it. Deactivate it instead.");
        }

        $name = $campus->name;
        $campus->delete();

        return back()->with('success', "Campus \"{$name}\" deleted successfully.");
    }
}