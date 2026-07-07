@extends('layouts.app')
@section('title', 'Manage Departments')
@section('page-title', 'Managing System')

@section('content')

<a href="{{ route('manage.index') }}"
   style="display:inline-flex; align-items:center; gap:6px; font-size:13px;
          color:var(--text-secondary); text-decoration:none; margin-bottom:1rem;">
    <i class="ti ti-arrow-left"></i> Back to Managing System
</a>

{{-- Hero --}}
<div class="hero-banner" style="background:linear-gradient(135deg, #ef9f27, #d4830f); margin-bottom:1.25rem;">
    <div class="hero-left">
        <div class="hero-greeting"><i class="ti ti-building-community"></i> Departments</div>
        <p class="hero-sub">Add, edit, or deactivate departments used across UCC-IMS and UCC-CS.</p>
        <div class="hero-chips">
            <div class="hero-chip"><span>Total</span>{{ $total }}</div>
            <div class="hero-chip"><span>Active</span>{{ $active }}</div>
            <div class="hero-chip"><span>Inactive</span>{{ $total - $active }}</div>
        </div>
    </div>
    <div class="hero-right">
        <button class="btn-add"
                onclick="document.getElementById('add-dept-modal').classList.add('open');">
            <i class="ti ti-plus"></i> Add Department
        </button>
    </div>
</div>

{{-- Search --}}
<div class="card" style="margin-bottom:1.25rem;">
    <div class="card-body" style="padding:1rem 1.25rem;">
        <form method="GET" action="{{ route('manage.departments') }}"
              style="display:flex; gap:10px; align-items:center;">
            <div style="flex:1; position:relative;">
                <i class="ti ti-search"
                   style="position:absolute; left:12px; top:50%; transform:translateY(-50%); color:#aaa; font-size:15px;"></i>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Search departments..."
                       style="width:100%; padding:9px 14px 9px 36px; border:1.5px solid var(--border);
                              border-radius:8px; font-size:13px; font-family:inherit; outline:none;">
            </div>
            <button type="submit" class="btn-table-action green">
                <i class="ti ti-search"></i> Search
            </button>
            @if($search)
            <a href="{{ route('manage.departments') }}" class="btn-table-action"
               style="background:#f5f5f5; color:#666;">
                <i class="ti ti-x"></i> Clear
            </a>
            @endif
        </form>
    </div>
</div>

{{-- Table --}}
<div class="card">
    <div class="card-header">
        <div class="card-title">
            <i class="ti ti-building-community"></i> Departments
            <span class="chip-badge chip-type">{{ $departments->total() }} total</span>
        </div>
    </div>
    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Department Name</th>
                    <th>Description</th>
                    <th>Users</th>
                    <th>Status</th>
                    <th>Date Added</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($departments as $dept)
                <tr>
                    <td style="font-size:12px; color:var(--text-muted);">{{ $dept->id }}</td>
                    <td>
                        <div class="cell-primary">{{ $dept->department_name }}</div>
                    </td>
                    <td style="font-size:12.5px; color:var(--text-muted); max-width:280px;">
                        {{ $dept->description ?: '—' }}
                    </td>
                    <td>
                        @php $userCount = \App\Models\User::where('department_id', $dept->id)->count(); @endphp
                        @if($userCount > 0)
                            <span class="chip-badge chip-status-active">{{ $userCount }} user(s)</span>
                        @else
                            <span style="font-size:12px; color:#ccc;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($dept->is_active)
                            <span class="chip-badge chip-status-active">
                                <i class="ti ti-circle-check" style="font-size:10px;"></i> Active
                            </span>
                        @else
                            <span class="chip-badge chip-status-inactive">
                                <i class="ti ti-circle-x" style="font-size:10px;"></i> Inactive
                            </span>
                        @endif
                    </td>
                    <td style="font-size:11.5px; color:var(--text-muted);">
                        {{ $dept->created_at->format('M d, Y') }}
                    </td>
                    <td>
                        <div class="table-actions">
                            {{-- Edit --}}
                            <button class="table-icon-btn edit" title="Edit"
                                    onclick="openEditDeptModal({{ $dept->id }}, '{{ addslashes($dept->department_name) }}', '{{ addslashes($dept->description ?? '') }}')">
                                <i class="ti ti-edit"></i>
                            </button>
                            {{-- Toggle Active --}}
                            <form method="POST"
                                  action="{{ route('manage.departments.toggle', $dept) }}"
                                  style="display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="table-icon-btn"
                                        style="background:{{ $dept->is_active ? '#fff8f0' : '#f0faf4' }};
                                               color:{{ $dept->is_active ? '#ef9f27' : 'var(--green-dark)' }};"
                                        title="{{ $dept->is_active ? 'Deactivate' : 'Activate' }}">
                                    <i class="ti ti-{{ $dept->is_active ? 'eye-off' : 'eye' }}"></i>
                                </button>
                            </form>
                            {{-- Delete --}}
                            <form method="POST"
                                  action="{{ route('manage.departments.destroy', $dept) }}"
                                  style="display:inline;"
                                  onsubmit="return confirm('Delete department {{ addslashes($dept->department_name) }}? This cannot be undone.')">
                                @csrf @method('DELETE')
                                <button type="submit" class="table-icon-btn delete" title="Delete">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="ti ti-building-off"></i>
                            <p>No departments found. {{ $search ? 'Try a different search.' : 'Click "Add Department" to create one.' }}</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($departments->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">
            Showing {{ $departments->firstItem() }} to {{ $departments->lastItem() }}
            of {{ $departments->total() }} results
        </div>
        {{ $departments->onEachSide(1)->links() }}
    </div>
    @endif
</div>

{{-- ADD DEPARTMENT MODAL --}}
<div class="modal-overlay" id="add-dept-modal">
    <div class="modal-box-sm">
        <div class="modal-header-row">
            <div class="modal-title-sm">
                <i class="ti ti-plus" style="color:#ef9f27;"></i> Add Department
            </div>
            <button class="modal-close"
                    onclick="document.getElementById('add-dept-modal').classList.remove('open');">
                <i class="ti ti-x"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('manage.departments.store') }}">
            @csrf
            <div class="modal-form-group">
                <div class="modal-label">Department Name *</div>
                <input type="text" name="department_name" class="modal-input" required
                       placeholder="e.g. Admission Office" autofocus>
            </div>
            <div class="modal-form-group">
                <div class="modal-label">Description <span style="font-weight:400; text-transform:none; font-size:10px;">(optional)</span></div>
                <textarea name="description" class="modal-input" rows="3"
                          style="padding-top:10px; resize:none;"
                          placeholder="Brief description of this department..."></textarea>
            </div>
            <button type="submit" class="modal-btn-primary" style="background:#ef9f27;">
                <i class="ti ti-plus"></i> Add Department
            </button>
        </form>
    </div>
</div>

{{-- EDIT DEPARTMENT MODAL --}}
<div class="modal-overlay" id="edit-dept-modal">
    <div class="modal-box-sm">
        <div class="modal-header-row">
            <div class="modal-title-sm">
                <i class="ti ti-edit" style="color:#7c3aed;"></i> Edit Department
            </div>
            <button class="modal-close"
                    onclick="document.getElementById('edit-dept-modal').classList.remove('open');">
                <i class="ti ti-x"></i>
            </button>
        </div>
        <form method="POST" id="edit-dept-form">
            @csrf @method('PUT')
            <div class="modal-form-group">
                <div class="modal-label">Department Name *</div>
                <input type="text" name="department_name" id="edit-dept-name"
                       class="modal-input" required>
            </div>
            <div class="modal-form-group">
                <div class="modal-label">Description <span style="font-weight:400; text-transform:none; font-size:10px;">(optional)</span></div>
                <textarea name="description" id="edit-dept-desc" class="modal-input"
                          rows="3" style="padding-top:10px; resize:none;"></textarea>
            </div>
            <button type="submit" class="modal-btn-primary" style="background:#7c3aed;">
                <i class="ti ti-device-floppy"></i> Save Changes
            </button>
        </form>
    </div>
</div>

@endsection

@push('scripts')
<script>
function openEditDeptModal(id, name, description) {
    document.getElementById('edit-dept-name').value = name;
    document.getElementById('edit-dept-desc').value = description;
    document.getElementById('edit-dept-form').action = `/manage/departments/${id}`;
    document.getElementById('edit-dept-modal').classList.add('open');
}

document.querySelectorAll('.modal-overlay').forEach(o => {
    o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
});
</script>
@endpush