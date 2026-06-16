@extends('layouts.app')

@section('title', 'Users')
@section('page-title', 'User Management')

@section('content')

@php $authRole = auth()->user()->role; @endphp

{{-- Stats row --}}
<div class="stats-grid" style="grid-template-columns: repeat(4,1fr); margin-bottom:1.25rem;">
    <div class="stat-card">
        <div class="stat-icon green"><i class="ti ti-users"></i></div>
        <div>
            <div class="stat-value">{{ $users->total() }}</div>
            <div class="stat-label">Total Users</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon blue"><i class="ti ti-user-check"></i></div>
        <div>
            <div class="stat-value">{{ $users->getCollection()->where('is_active', true)->count() }}</div>
            <div class="stat-label">Active</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="ti ti-shield"></i></div>
        <div>
            <div class="stat-value">{{ $users->getCollection()->whereIn('role', ['admin','superadmin'])->count() }}</div>
            <div class="stat-label">Admins</div>
        </div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="ti ti-user-off"></i></div>
        <div>
            <div class="stat-value">{{ $users->getCollection()->where('is_active', false)->count() }}</div>
            <div class="stat-label">Archived</div>
        </div>
    </div>
</div>

{{-- Table Card --}}
<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-users"></i> User Accounts</div>

        {{-- Filters --}}
        <div style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">
            <form method="GET" action="{{ route('users') }}" style="display:flex; gap:8px;">
                <input type="text" name="search" value="{{ request('search') }}"
                       placeholder="Search name or email..."
                       style="padding:7px 12px; border:1.5px solid var(--border); border-radius:8px;
                              font-size:13px; font-family:inherit; outline:none; width:200px;">

                <select name="role" style="padding:7px 12px; border:1.5px solid var(--border);
                        border-radius:8px; font-size:13px; font-family:inherit; outline:none;">
                    <option value="">All Roles</option>
                    <option value="user"       {{ request('role') === 'user'       ? 'selected' : '' }}>User</option>
                    <option value="admin"      {{ request('role') === 'admin'      ? 'selected' : '' }}>Admin</option>
                    @if($authRole === 'superadmin')
                    <option value="superadmin" {{ request('role') === 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                    @endif
                </select>

                <select name="status" style="padding:7px 12px; border:1.5px solid var(--border);
                        border-radius:8px; font-size:13px; font-family:inherit; outline:none;">
                    <option value="">All Status</option>
                    <option value="1" {{ request('status') === '1' ? 'selected' : '' }}>Active</option>
                    <option value="0" {{ request('status') === '0' ? 'selected' : '' }}>Archived</option>
                </select>

                <button type="submit" class="btn-table-action green">
                    <i class="ti ti-search"></i> Search
                </button>
            </form>

            {{-- Add User: superadmin only --}}
            @if($authRole === 'superadmin')
            <button class="btn-table-action green" onclick="openAddModal()">
                <i class="ti ti-user-plus"></i> Add User
            </button>
            @endif
        </div>
    </div>

    <div style="overflow-x:auto;">
        <table class="users-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Campus</th>
                    <th>Department</th>
                    <th>Status</th>
                    <th>Joined</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($users as $index => $user)
                <tr class="{{ !$user->is_active ? 'row-archived' : '' }}">
                    <td style="color:var(--text-muted); font-size:12px;">{{ $users->firstItem() + $index }}</td>
                    <td>
                        <div style="display:flex; align-items:center; gap:8px;">
                            <div class="user-table-avatar">{{ strtoupper(substr($user->name, 0, 1)) }}</div>
                            <span style="font-weight:500; font-size:13px;">{{ $user->name }}</span>
                        </div>
                    </td>
                    <td style="font-size:13px; color:var(--text-secondary);">{{ $user->email }}</td>
                    <td>
                        @if($user->role === 'superadmin')
                            <span class="role-badge badge-sa">Super Admin</span>
                        @elseif($user->role === 'admin')
                            <span class="role-badge badge-ad">Admin</span>
                        @else
                            <span class="role-badge badge-us">User</span>
                        @endif
                    </td>
                    <td style="font-size:12px;">{{ $user->campus->name ?? '—' }}</td>
                    <td style="font-size:12px;">{{ $user->department->department_name ?? '—' }}</td>
                    <td>
                        @if($user->is_active)
                            <span class="status-badge active">Active</span>
                        @else
                            <span class="status-badge archived">Archived</span>
                        @endif
                    </td>
                    <td style="font-size:12px; color:var(--text-muted);">
                        {{ $user->created_at->format('M d, Y') }}
                    </td>
                    <td>
                        <div style="display:flex; gap:4px;">

                            {{-- Edit --}}
                            @if(!($authRole === 'admin' && $user->role === 'superadmin'))
                            <button class="btn-icon-action blue"
                                    onclick="openEditModal({{ $user->id }}, '{{ addslashes($user->name) }}', '{{ $user->email }}', '{{ $user->role }}', '{{ $user->campus_id }}', '{{ $user->department_id }}', '{{ $user->phone }}')"
                                    title="Edit">
                                <i class="ti ti-edit"></i>
                            </button>
                            @endif

                            {{-- Archive/Restore --}}
                            @if($user->id !== auth()->id() && !($authRole === 'admin' && $user->role === 'superadmin'))
                            <form method="POST" action="{{ route('users.archive', $user) }}" style="display:inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-icon-action {{ $user->is_active ? 'orange' : 'green' }}"
                                        title="{{ $user->is_active ? 'Archive' : 'Restore' }}"
                                        onclick="return confirm('{{ $user->is_active ? 'Archive' : 'Restore' }} this user account?')">
                                    <i class="ti ti-{{ $user->is_active ? 'archive' : 'archive-off' }}"></i>
                                </button>
                            </form>
                            @endif

                            {{-- Delete: superadmin only --}}
                            @if($authRole === 'superadmin' && $user->id !== auth()->id())
                            <form method="POST" action="{{ route('users.destroy', $user) }}" style="display:inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="btn-icon-action red"
                                        title="Delete"
                                        onclick="return confirm('Permanently delete this account? This cannot be undone.')">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                            @endif

                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="9">
                        <div class="empty-state">
                            <i class="ti ti-users"></i>
                            <p>No users found.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    {{-- Pagination --}}
    @if($users->hasPages())
    <div style="padding:1rem 1.25rem; border-top:1px solid var(--border);">
        {{ $users->withQueryString()->links() }}
    </div>
    @endif
</div>

{{-- ADD USER MODAL (superadmin only) --}}
@if($authRole === 'superadmin')
<div class="modal-overlay" id="add-user-modal">
    <div class="modal-box-lg">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-user-plus"></i> Add User Account</div>
            <button class="modal-close" onclick="closeAddModal()"><i class="ti ti-x"></i></button>
        </div>

        <form method="POST" action="{{ route('users.store') }}">
            @csrf
            <div class="modal-grid">
                <div class="modal-form-group">
                    <div class="modal-label">Full Name *</div>
                    <div class="modal-input-wrap">
                        <i class="ti ti-user modal-input-icon"></i>
                        <input type="text" name="name" class="modal-input" placeholder="e.g., Juan Dela Cruz" required>
                    </div>
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Email Address *</div>
                    <div class="modal-input-wrap">
                        <i class="ti ti-mail modal-input-icon"></i>
                        <input type="email" name="email" class="modal-input" placeholder="email@ucc.edu.ph" required>
                    </div>
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Role *</div>
                    <div class="modal-input-wrap">
                        <i class="ti ti-shield modal-input-icon"></i>
                        <select name="role" class="modal-input" required>
                            <option value="">-- Select Role --</option>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                            <option value="superadmin">Super Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Campus</div>
                    <div class="modal-input-wrap">
                        <i class="ti ti-building modal-input-icon"></i>
                        <select name="campus_id" class="modal-input">
                            <option value="">-- Select Campus --</option>
                            @foreach($campuses as $campus)
                            <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Department</div>
                    <div class="modal-input-wrap">
                        <i class="ti ti-briefcase modal-input-icon"></i>
                        <select name="department_id" class="modal-input">
                            <option value="">-- Select Department --</option>
                            @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->department_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Phone (Optional)</div>
                    <div class="modal-input-wrap">
                        <i class="ti ti-phone modal-input-icon"></i>
                        <input type="text" name="phone" class="modal-input" placeholder="09XXXXXXXXX">
                    </div>
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Password *</div>
                    <div class="modal-input-wrap">
                        <i class="ti ti-lock modal-input-icon"></i>
                        <input type="password" name="password" class="modal-input" placeholder="Minimum 8 characters" required id="add-pass">
                        <i class="ti ti-eye modal-input-right" onclick="toggleModalPass('add-pass', this)"></i>
                    </div>
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Confirm Password *</div>
                    <div class="modal-input-wrap">
                        <i class="ti ti-lock-check modal-input-icon"></i>
                        <input type="password" name="password_confirmation" class="modal-input" placeholder="Re-enter password" required id="add-conf">
                        <i class="ti ti-eye modal-input-right" onclick="toggleModalPass('add-conf', this)"></i>
                    </div>
                </div>
            </div>
            <button type="submit" class="modal-btn-primary">
                <i class="ti ti-user-plus"></i> Create Account
            </button>
        </form>
    </div>
</div>
@endif

{{-- EDIT USER MODAL --}}
<div class="modal-overlay" id="edit-user-modal">
    <div class="modal-box-lg">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-edit"></i> Edit User Account</div>
            <button class="modal-close" onclick="closeEditModal()"><i class="ti ti-x"></i></button>
        </div>

        <form method="POST" id="edit-user-form">
            @csrf @method('PUT')
            <div class="modal-grid">
                <div class="modal-form-group">
                    <div class="modal-label">Full Name *</div>
                    <div class="modal-input-wrap">
                        <i class="ti ti-user modal-input-icon"></i>
                        <input type="text" name="name" id="edit-name" class="modal-input" required>
                    </div>
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Email Address *</div>
                    <div class="modal-input-wrap">
                        <i class="ti ti-mail modal-input-icon"></i>
                        <input type="email" name="email" id="edit-email" class="modal-input" required>
                    </div>
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Role *</div>
                    <div class="modal-input-wrap">
                        <i class="ti ti-shield modal-input-icon"></i>
                        <select name="role" id="edit-role" class="modal-input" required>
                            <option value="user">User</option>
                            @if($authRole === 'superadmin')
                            <option value="admin">Admin</option>
                            <option value="superadmin">Super Admin</option>
                            @endif
                        </select>
                    </div>
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Campus</div>
                    <div class="modal-input-wrap">
                        <i class="ti ti-building modal-input-icon"></i>
                        <select name="campus_id" id="edit-campus" class="modal-input">
                            <option value="">-- Select Campus --</option>
                            @foreach($campuses as $campus)
                            <option value="{{ $campus->id }}">{{ $campus->name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Department</div>
                    <div class="modal-input-wrap">
                        <i class="ti ti-briefcase modal-input-icon"></i>
                        <select name="department_id" id="edit-dept" class="modal-input">
                            <option value="">-- Select Department --</option>
                            @foreach($departments as $dept)
                            <option value="{{ $dept->id }}">{{ $dept->department_name }}</option>
                            @endforeach
                        </select>
                    </div>
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Phone</div>
                    <div class="modal-input-wrap">
                        <i class="ti ti-phone modal-input-icon"></i>
                        <input type="text" name="phone" id="edit-phone" class="modal-input">
                    </div>
                </div>
            </div>
            <button type="submit" class="modal-btn-primary">
                <i class="ti ti-check"></i> Save Changes
            </button>
        </form>
    </div>
</div>

<style>
.users-table { width:100%; border-collapse:collapse; }
.users-table th {
    padding: 10px 14px; text-align:left;
    font-size: 11px; font-weight:700;
    text-transform:uppercase; letter-spacing:0.8px;
    color: var(--text-muted);
    background: #fafafa;
    border-bottom: 1px solid var(--border);
}
.users-table td {
    padding: 12px 14px;
    border-bottom: 1px solid var(--border);
    vertical-align: middle;
}
.users-table tr:last-child td { border-bottom: none; }
.users-table tr:hover td { background: #fafefe; }
.row-archived td { opacity: 0.55; }

.user-table-avatar {
    width: 30px; height: 30px; border-radius: 50%;
    background: var(--green-dark); color: #fff;
    display: flex; align-items: center; justify-content: center;
    font-size: 12px; font-weight: 700; flex-shrink: 0;
}

.role-badge {
    font-size: 10px; font-weight: 700;
    padding: 3px 8px; border-radius: 6px;
    text-transform: uppercase; letter-spacing: 0.5px;
}
.badge-sa { background: rgba(239,159,39,0.15); color: #b87800; }
.badge-ad { background: rgba(59,130,246,0.12); color: #2563eb; }
.badge-us { background: rgba(26,107,58,0.1);   color: var(--green-dark); }

.status-badge {
    font-size: 11px; font-weight: 600;
    padding: 3px 10px; border-radius: 20px;
}
.status-badge.active   { background: #f0faf4; color: var(--green-dark); }
.status-badge.archived { background: #f5f5f5; color: #999; }

.btn-table-action {
    display: flex; align-items: center; gap: 6px;
    padding: 7px 14px; border-radius: 8px;
    font-size: 12px; font-weight: 600;
    border: none; cursor: pointer;
    font-family: 'Inter', sans-serif;
    transition: opacity 0.15s;
    text-decoration: none;
}
.btn-table-action.green { background: var(--green-dark); color: #fff; }
.btn-table-action.green:hover { opacity: 0.88; }

.btn-icon-action {
    width: 30px; height: 30px; border-radius: 6px;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; border: none; cursor: pointer;
    transition: opacity 0.15s;
}
.btn-icon-action.blue   { background: #eff6ff; color: #3b82f6; }
.btn-icon-action.orange { background: #fff8f0; color: #ef9f27; }
.btn-icon-action.red    { background: #fff5f5; color: var(--red); }
.btn-icon-action.green  { background: #f0faf4; color: var(--green-dark); }
.btn-icon-action:hover  { opacity: 0.75; }

.modal-box-lg {
    background: #fff; border-radius: 14px;
    padding: 1.5rem; width: 100%; max-width: 640px;
    box-shadow: 0 24px 64px rgba(0,0,0,0.18);
    animation: dropIn 0.2s ease;
    max-height: 90vh; overflow-y: auto;
}

.modal-grid {
    display: grid; grid-template-columns: 1fr 1fr;
    gap: 0.75rem; margin-bottom: 0.5rem;
}

@media(max-width:560px) { .modal-grid { grid-template-columns: 1fr; } }
</style>

@endsection

@push('scripts')
<script>
function openAddModal() {
    document.getElementById('add-user-modal').classList.add('open');
}
function closeAddModal() {
    document.getElementById('add-user-modal').classList.remove('open');
}

function openEditModal(id, name, email, role, campusId, deptId, phone) {
    document.getElementById('edit-name').value  = name;
    document.getElementById('edit-email').value = email;
    document.getElementById('edit-role').value  = role;
    document.getElementById('edit-campus').value = campusId || '';
    document.getElementById('edit-dept').value   = deptId || '';
    document.getElementById('edit-phone').value  = phone !== 'null' ? phone : '';
    document.getElementById('edit-user-form').action = `/users/${id}`;
    document.getElementById('edit-user-modal').classList.add('open');
}
function closeEditModal() {
    document.getElementById('edit-user-modal').classList.remove('open');
}

// Close modals on overlay click
document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('open');
    });
});
</script>
@endpush