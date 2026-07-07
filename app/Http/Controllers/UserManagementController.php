<?php

namespace App\Http\Controllers;

use App\Models\Campus;
use App\Models\Department;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserManagementController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
    }

    public function index(Request $request)
    {
        $authUser     = auth()->user();
        $search       = $request->get('search');
        $roleFilter   = $request->get('role');
        $statusFilter = $request->get('status');
        $sourceFilter = $request->get('source');
        $campusFilter = $request->get('campus_id');

        $query = User::with(['campus', 'department'])
            ->when($search, fn($q) => $q->where('name', 'like', "%$search%")
                ->orWhere('email', 'like', "%$search%"))
            ->when($roleFilter, fn($q) => $q->where('role', $roleFilter))
            ->when($sourceFilter, fn($q) => $q->where('source', $sourceFilter))
            ->when($campusFilter, fn($q) => $q->where('campus_id', $campusFilter))
            ->when($statusFilter !== null && $statusFilter !== '',
                fn($q) => $q->where('is_active', $statusFilter));

        // Admin cannot see superadmin accounts
        if ($authUser->role === 'admin') {
            $query->whereIn('role', ['user', 'admin']);
        }

        $users       = $query->latest()->paginate(10)->withQueryString();
        $campuses    = Campus::where('is_active', true)->get();
        $departments = Department::where('is_active', true)->orderBy('department_name')->get();

        return view('pages.users', compact(
            'users', 'campuses', 'departments', 'authUser',
            'search', 'roleFilter', 'statusFilter', 'sourceFilter', 'campusFilter'
        ));
    }

    public function show(User $user)
    {
        $authUser = auth()->user();

        if ($authUser->role === 'admin' && $user->role === 'superadmin') {
            abort(403, 'You cannot view a Super Admin account.');
        }

        $pendingDeletion = \App\Models\AccountDeletionRequest::where('user_id', $user->id)
            ->where('status', 'pending')
            ->first();

        $deletionHistory = \App\Models\AccountDeletionRequest::where('user_id', $user->id)
            ->latest()
            ->get();

        return view('pages.user_show', compact('user', 'pendingDeletion', 'deletionHistory'));
    }

    public function store(Request $request)
    {
        $authUser = auth()->user();

        $allowedRoles = ['user'];
        if ($authUser->role === 'superadmin') {
            $allowedRoles = ['user', 'admin', 'superadmin'];
        }

        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email',
            'password'      => 'required|min:8|confirmed',
            'role'          => 'required|in:' . implode(',', $allowedRoles),
            'source'        => 'required|in:ims,cs',
            'campus_id'     => 'nullable|exists:campuses,id',
            'department_id' => 'nullable|exists:departments,id',
            'phone'         => 'nullable|string|max:20',
        ]);

        User::create([
            'name'          => $request->name,
            'email'         => $request->email,
            'password'      => Hash::make($request->password),
            'role'          => $request->role,
            'source'        => $request->source,
            'campus_id'     => $request->campus_id,
            'department_id' => $request->department_id,
            'phone'         => $request->phone,
            'is_active'     => true,
            'status'        => 'active',
        ]);

        return back()->with('success', 'User account created successfully.');
    }

    public function update(Request $request, User $user)
    {
        $authUser = auth()->user();

        if ($authUser->role === 'admin' && $user->role === 'superadmin') {
            return back()->with('error', 'You cannot edit a Super Admin account.');
        }

        $allowedRoles = $authUser->role === 'superadmin'
            ? ['user', 'admin', 'superadmin']
            : ['user'];

        $request->validate([
            'name'          => 'required|string|max:255',
            'email'         => 'required|email|unique:users,email,' . $user->id,
            'role'          => 'required|in:' . implode(',', $allowedRoles),
            'source'        => 'required|in:ims,cs',
            'campus_id'     => 'nullable|exists:campuses,id',
            'department_id' => 'nullable|exists:departments,id',
            'phone'         => 'nullable|string|max:20',
        ]);

        $user->update([
            'name'          => $request->name,
            'email'         => $request->email,
            'role'          => $request->role,
            'source'        => $request->source,
            'campus_id'     => $request->campus_id,
            'department_id' => $request->department_id,
            'phone'         => $request->phone,
        ]);

        return back()->with('success', 'User account updated successfully.');
    }

    public function approve(User $user)
    {
        $user->update(['status' => 'active']);
        return back()->with('success', "Account for {$user->name} approved.");
    }

    public function archive(User $user)
    {
        $authUser = auth()->user();

        if ($authUser->role === 'admin' && $user->role === 'superadmin') {
            return back()->with('error', 'You cannot archive a Super Admin account.');
        }

        if ($user->id === $authUser->id) {
            return back()->with('error', 'You cannot archive your own account.');
        }

        $user->update(['is_active' => !$user->is_active]);
        $status = $user->fresh()->is_active ? 'activated' : 'archived';

        return back()->with('success', "User account {$status} successfully.");
    }

    public function destroy(User $user)
    {
        $authUser = auth()->user();

        if ($authUser->role !== 'superadmin') {
            return back()->with('error', 'Only Super Admins can delete user accounts.');
        }

        if ($user->id === $authUser->id) {
            return back()->with('error', 'You cannot delete your own account.');
        }

        $user->delete();

        return back()->with('success', 'User account deleted permanently.');
    }
}