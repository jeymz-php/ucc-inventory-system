<?php

namespace App\Http\Controllers;

use App\Models\Department;
use Illuminate\Http\Request;

class DepartmentController extends Controller
{
    public function __construct()
    {
        $this->middleware(['auth', 'role:admin,superadmin']);
    }

    public function index(Request $request)
    {
        $search = $request->get('search');

        $departments = Department::when($search, fn($q) =>
            $q->where('department_name', 'like', "%$search%")
              ->orWhere('description', 'like', "%$search%")
        )
        ->orderBy('department_name')
        ->paginate(20)
        ->withQueryString();

        $total  = Department::count();
        $active = Department::where('is_active', true)->count();

        return view('pages.manage.departments', compact('departments', 'search', 'total', 'active'));
    }

    public function store(Request $request)
    {
        $request->validate([
            'department_name' => 'required|string|max:100|unique:departments,department_name',
            'description'     => 'nullable|string|max:500',
        ]);

        Department::create([
            'department_name' => trim($request->department_name),
            'description'     => trim($request->description ?? ''),
            'is_active'       => true,
        ]);

        return back()->with('success', "Department \"{$request->department_name}\" added successfully.");
    }

    public function update(Request $request, Department $department)
    {
        $request->validate([
            'department_name' => 'required|string|max:100|unique:departments,department_name,' . $department->id,
            'description'     => 'nullable|string|max:500',
        ]);

        $department->update([
            'department_name' => trim($request->department_name),
            'description'     => trim($request->description ?? ''),
        ]);

        return back()->with('success', "Department updated successfully.");
    }

    public function toggleActive(Department $department)
    {
        $department->update(['is_active' => !$department->is_active]);

        $status = $department->fresh()->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "Department \"{$department->department_name}\" {$status}.");
    }

    public function destroy(Department $department)
    {
        // Check if any users are assigned to this department
        $userCount = \App\Models\User::where('department_id', $department->id)->count();
        if ($userCount > 0) {
            return back()->with('error', "Cannot delete \"{$department->department_name}\" — {$userCount} user(s) are assigned to it. Deactivate it instead.");
        }

        $name = $department->department_name;
        $department->delete();

        return back()->with('success', "Department \"{$name}\" deleted successfully.");
    }
}