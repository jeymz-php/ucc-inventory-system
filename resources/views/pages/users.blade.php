@extends('layouts.app')

@section('title', 'Users')
@section('page-title', 'User Management')

@section('content')

@php $authRole = auth()->user()->role; @endphp

{{-- Stats row --}}
<div class="stats-grid" style="grid-template-columns: repeat(5,1fr); margin-bottom:1.25rem;">
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
        <div class="stat-icon orange"><i class="ti ti-clock"></i></div>
        <div>
            <div class="stat-value">{{ $users->getCollection()->where('status', 'pending')->count() }}</div>
            <div class="stat-label">Pending Approval</div>
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
    <div class="card-header" style="flex-wrap:wrap; gap:0.75rem;">
        <div class="card-title"><i class="ti ti-users"></i> User Accounts</div>

        {{-- Add User --}}
        @if($authRole === 'superadmin')
        <button class="btn-table-action green" onclick="openAddModal()">
            <i class="ti ti-user-plus"></i> Add User
        </button>
        @endif
    </div>

    {{-- Filters --}}
    <div class="card-body" style="padding:0.85rem 1.25rem; border-bottom:1px solid var(--border);">
        <form method="GET" action="{{ route('users') }}"
              style="display:flex; gap:8px; align-items:center; flex-wrap:wrap;">

            {{-- Search --}}
            <div style="position:relative; flex:1; min-width:180px;">
                <i class="ti ti-search"
                   style="position:absolute; left:10px; top:50%; transform:translateY(-50%);
                          color:#aaa; font-size:14px; pointer-events:none;"></i>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Search name or email..."
                       style="width:100%; padding:8px 12px 8px 32px; border:1.5px solid var(--border);
                              border-radius:8px; font-size:13px; font-family:inherit; outline:none;">
            </div>

            {{-- Role --}}
            <select name="role"
                    style="padding:8px 12px; border:1.5px solid var(--border);
                           border-radius:8px; font-size:13px; font-family:inherit; outline:none; min-width:120px;">
                <option value="">All Roles</option>
                <option value="user"       {{ $roleFilter === 'user'       ? 'selected' : '' }}>User</option>
                <option value="admin"      {{ $roleFilter === 'admin'      ? 'selected' : '' }}>Admin</option>
                @if($authRole === 'superadmin')
                <option value="superadmin" {{ $roleFilter === 'superadmin' ? 'selected' : '' }}>Super Admin</option>
                @endif
            </select>

            {{-- Source (IMS / CS) --}}
            <select name="source"
                    style="padding:8px 12px; border:1.5px solid var(--border);
                           border-radius:8px; font-size:13px; font-family:inherit; outline:none; min-width:120px;">
                <option value="">All Sources</option>
                <option value="ims" {{ ($sourceFilter ?? '') === 'ims' ? 'selected' : '' }}>IMS</option>
                <option value="cs"  {{ ($sourceFilter ?? '') === 'cs'  ? 'selected' : '' }}>CS</option>
            </select>

            {{-- Campus --}}
            <select name="campus_id"
                    style="padding:8px 12px; border:1.5px solid var(--border);
                           border-radius:8px; font-size:13px; font-family:inherit; outline:none; min-width:160px;">
                <option value="">All Campuses</option>
                @foreach($campuses as $campus)
                <option value="{{ $campus->id }}" {{ ($campusFilter ?? '') == $campus->id ? 'selected' : '' }}>
                    {{ $campus->name }}
                </option>
                @endforeach
            </select>

            {{-- Status --}}
            <select name="status"
                    style="padding:8px 12px; border:1.5px solid var(--border);
                           border-radius:8px; font-size:13px; font-family:inherit; outline:none; min-width:120px;">
                <option value="">All Status</option>
                <option value="active"  {{ ($statusFilter ?? '') === 'active'  ? 'selected' : '' }}>Active</option>
                <option value="pending" {{ ($statusFilter ?? '') === 'pending' ? 'selected' : '' }}>Pending</option>
                <option value="0"       {{ ($statusFilter ?? '') === '0'       ? 'selected' : '' }}>Archived</option>
            </select>

            <button type="submit" class="btn-table-action green">
                <i class="ti ti-search"></i> Filter
            </button>

            @if($search || $roleFilter || $sourceFilter || $campusFilter || $statusFilter)
            <a href="{{ route('users') }}" class="btn-table-action"
               style="background:#f5f5f5; color:#666; text-decoration:none;">
                <i class="ti ti-x"></i> Clear
            </a>
            @endif

        </form>
    </div>

    <div style="overflow-x:auto;">
        <table class="users-table">
            <thead>
                <tr>
                    <th>#</th>
                    <th>Name</th>
                    <th>Email</th>
                    <th>Role</th>
                    <th>Source</th>
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
                    <td>
                        @if($user->source === 'cs')
                            <span class="chip-badge chip-campus" style="gap:4px;">
                                <i class="ti ti-package" style="font-size:10px;"></i> CS
                            </span>
                        @else
                            <span class="chip-badge chip-type" style="gap:4px;">
                                <i class="ti ti-device-desktop" style="font-size:10px;"></i> IMS
                            </span>
                        @endif
                    </td>
                    <td style="font-size:12px;">{{ $user->campus->name ?? '—' }}</td>
                    <td style="font-size:12px;">{{ $user->department->department_name ?? '—' }}</td>
                    <td>
                        @if($user->status === 'pending')
                            <span class="chip-badge" style="background:#fff8f0; color:#ef9f27;">
                                <i class="ti ti-clock" style="font-size:10px;"></i> Pending
                            </span>
                        @elseif($user->is_active)
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

                            {{-- View --}}
                            <a href="{{ route('users.show', $user) }}" class="btn-icon-action blue" title="View">
                                <i class="ti ti-eye"></i>
                            </a>

                            {{-- Approve --}}
                            @if($user->status === 'pending')
                            <form method="POST" action="{{ route('users.approve', $user) }}" style="display:inline">
                                @csrf @method('PATCH')
                                <button type="submit" class="btn-icon-action green" title="Approve Account"
                                        onclick="return confirm('Approve this account?')">
                                    <i class="ti ti-check"></i>
                                </button>
                            </form>
                            @endif

                            {{-- Edit --}}
                            @if(!($authRole === 'admin' && $user->role === 'superadmin'))
                            <button class="btn-icon-action blue"
                                    onclick="openEditModal(
                                        {{ $user->id }},
                                        '{{ addslashes($user->name) }}',
                                        '{{ $user->email }}',
                                        '{{ $user->role }}',
                                        '{{ $user->source ?? 'ims' }}',
                                        '{{ $user->campus_id }}',
                                        '{{ $user->department_id }}',
                                        '{{ $user->phone }}'
                                    )"
                                    title="Edit">
                                <i class="ti ti-edit"></i>
                            </button>
                            @endif

                            {{-- Archive / Restore --}}
                            @if($user->id !== auth()->id() && !($authRole === 'admin' && $user->role === 'superadmin'))
                            <form method="POST" action="{{ route('users.archive', $user) }}" style="display:inline">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="btn-icon-action {{ $user->is_active ? 'orange' : 'green' }}"
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
                                <button type="submit" class="btn-icon-action red" title="Delete"
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
                    <td colspan="10">
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

    @if($users->hasPages())
    <div style="padding:1rem 1.25rem; border-top:1px solid var(--border);">
        {{ $users->withQueryString()->links() }}
    </div>
    @endif
</div>

{{-- ADD USER MODAL --}}
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
                        <select name="role" id="add-role" class="modal-input" required>
                            <option value="">-- Select Role --</option>
                            <option value="user">User</option>
                            <option value="admin">Admin</option>
                            <option value="superadmin">Super Admin</option>
                        </select>
                    </div>
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Source *</div>
                    <div class="modal-input-wrap">
                        <i class="ti ti-layers-intersect modal-input-icon"></i>
                        <select name="source" id="add-source" class="modal-input" required>
                            <option value="">-- Select Source --</option>
                            <option value="ims">IMS — Inventory Management System</option>
                            <option value="cs">CS — Consumable System</option>
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
                    <div class="modal-label">Phone</div>
                    <div class="modal-input-wrap">
                        <i class="ti ti-phone modal-input-icon"></i>
                        <input type="text" name="phone" class="modal-input" placeholder="09XXXXXXXXX">
                    </div>
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Password *</div>
                    <div class="modal-input-wrap has-right">
                        <i class="ti ti-lock modal-input-icon"></i>
                        <input type="password" name="password" class="modal-input"
                               placeholder="Minimum 8 characters" required id="add-pass">
                        <i class="ti ti-eye modal-input-right" onclick="toggleModalPass('add-pass', this)"></i>
                    </div>
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Confirm Password *</div>
                    <div class="modal-input-wrap has-right">
                        <i class="ti ti-lock-check modal-input-icon"></i>
                        <input type="password" name="password_confirmation" class="modal-input"
                               placeholder="Re-enter password" required id="add-conf">
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
                    <div class="modal-label">Source *</div>
                    <div class="modal-input-wrap">
                        <i class="ti ti-layers-intersect modal-input-icon"></i>
                        <select name="source" id="edit-source" class="modal-input" required>
                            <option value="ims">IMS — Inventory Management System</option>
                            <option value="cs">CS — Consumable System</option>
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
    color: var(--text-muted); background: #fafafa;
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

.btn-icon-action {
    width: 30px; height: 30px; border-radius: 6px;
    display: flex; align-items: center; justify-content: center;
    font-size: 14px; border: none; cursor: pointer;
    transition: opacity 0.15s; text-decoration: none;
}
.btn-icon-action.blue   { background: #eff6ff; color: #3b82f6; }
.btn-icon-action.orange { background: #fff8f0; color: #ef9f27; }
.btn-icon-action.red    { background: #fff5f5; color: var(--red); }
.btn-icon-action.green  { background: #f0faf4; color: var(--green-dark); }
.btn-icon-action:hover  { opacity: 0.75; }
</style>

@endsection

@push('scripts')
<script>
function openAddModal() {
    document.getElementById('add-user-modal').classList.add('open');
    updateRoleOptions('add');
}
function closeAddModal() {
    document.getElementById('add-user-modal').classList.remove('open');
}

function openEditModal(id, name, email, role, source, campusId, deptId, phone) {
    document.getElementById('edit-name').value    = name;
    document.getElementById('edit-email').value   = email;
    document.getElementById('edit-source').value  = source || 'ims';
    document.getElementById('edit-campus').value  = campusId || '';
    document.getElementById('edit-dept').value    = deptId   || '';
    document.getElementById('edit-phone').value   = (phone && phone !== 'null') ? phone : '';
    document.getElementById('edit-user-form').action = `/users/${id}`;

    // Set role options first, then select the current role
    updateRoleOptions('edit');
    document.getElementById('edit-role').value = role;

    document.getElementById('edit-user-modal').classList.add('open');
}
function closeEditModal() {
    document.getElementById('edit-user-modal').classList.remove('open');
}

// ── ROLE OPTIONS BASED ON SOURCE ──
function updateRoleOptions(prefix) {
    const sourceEl = document.getElementById(`${prefix}-source`);
    const roleEl   = document.getElementById(`${prefix}-role`);
    if (!sourceEl || !roleEl) return;

    const source = sourceEl.value;

    if (source === 'cs') {
        // CS only allows User role
        roleEl.innerHTML = `<option value="user">User</option>`;
        roleEl.value     = 'user';
        roleEl.disabled  = true;
    } else {
        // IMS allows all roles (based on auth role)
        const isSuperAdmin = {{ $authRole === 'superadmin' ? 'true' : 'false' }};
        roleEl.disabled  = false;

        if (isSuperAdmin) {
            roleEl.innerHTML = `
                <option value="user">User</option>
                <option value="admin">Admin</option>
                <option value="superadmin">Super Admin</option>
            `;
        } else {
            roleEl.innerHTML = `<option value="user">User</option>`;
        }
    }
}

// Attach source change listeners on page load
document.addEventListener('DOMContentLoaded', function() {
    const addSource  = document.getElementById('add-source');
    const editSource = document.getElementById('edit-source');

    if (addSource)  addSource.addEventListener('change',  () => updateRoleOptions('add'));
    if (editSource) editSource.addEventListener('change', () => updateRoleOptions('edit'));
});

document.querySelectorAll('.modal-overlay').forEach(overlay => {
    overlay.addEventListener('click', function(e) {
        if (e.target === this) this.classList.remove('open');
    });
});
</script>
@endpush