@extends('layouts.app')
@section('title', 'Manage Campuses')
@section('page-title', 'Managing System')

@section('content')

<a href="{{ route('manage.index') }}"
   style="display:inline-flex; align-items:center; gap:6px; font-size:13px;
          color:var(--text-secondary); text-decoration:none; margin-bottom:1rem;">
    <i class="ti ti-arrow-left"></i> Back to Managing System
</a>

{{-- Hero --}}
<div class="hero-banner" style="background:linear-gradient(135deg, #3b82f6, #1d4ed8); margin-bottom:1.25rem;">
    <div class="hero-left">
        <div class="hero-greeting"><i class="ti ti-map-pin"></i> Campuses</div>
        <p class="hero-sub">Add, edit, or deactivate campuses used across UCC-IMS and UCC-CS.</p>
        <div class="hero-chips">
            <div class="hero-chip"><span>Total</span>{{ $total }}</div>
            <div class="hero-chip"><span>Active</span>{{ $active }}</div>
            <div class="hero-chip"><span>Inactive</span>{{ $total - $active }}</div>
        </div>
    </div>
    <div class="hero-right">
        <button class="btn-add"
                onclick="document.getElementById('add-campus-modal').classList.add('open');">
            <i class="ti ti-plus"></i> Add Campus
        </button>
    </div>
</div>

{{-- Search --}}
<div class="card" style="margin-bottom:1.25rem;">
    <div class="card-body" style="padding:1rem 1.25rem;">
        <form method="GET" action="{{ route('manage.campuses') }}"
              style="display:flex; gap:10px; align-items:center;">
            <div style="flex:1; position:relative;">
                <i class="ti ti-search"
                   style="position:absolute; left:12px; top:50%; transform:translateY(-50%);
                          color:#aaa; font-size:15px;"></i>
                <input type="text" name="search" value="{{ $search }}"
                       placeholder="Search campus name or code..."
                       style="width:100%; padding:9px 14px 9px 36px; border:1.5px solid var(--border);
                              border-radius:8px; font-size:13px; font-family:inherit; outline:none;">
            </div>
            <button type="submit" class="btn-table-action green">
                <i class="ti ti-search"></i> Search
            </button>
            @if($search)
            <a href="{{ route('manage.campuses') }}" class="btn-table-action"
               style="background:#f5f5f5; color:#666; text-decoration:none;">
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
            <i class="ti ti-map-pin"></i> Campuses
            <span class="chip-badge chip-campus">{{ $campuses->total() }} total</span>
        </div>
    </div>
    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th style="width:50px;">#</th>
                    <th>Campus Name</th>
                    <th style="width:120px;">Code</th>
                    <th>Users</th>
                    <th>Status</th>
                    <th>Date Added</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($campuses as $campus)
                @php $userCount = \App\Models\User::where('campus_id', $campus->id)->count(); @endphp
                <tr>
                    <td style="font-size:12px; color:var(--text-muted);">{{ $campus->id }}</td>
                    <td>
                        <div class="cell-primary">{{ $campus->name }}</div>
                    </td>
                    <td>
                        <span class="chip-badge chip-campus"
                              style="font-family:monospace; letter-spacing:0.5px;">
                            {{ $campus->code }}
                        </span>
                    </td>
                    <td>
                        @if($userCount > 0)
                            <span class="chip-badge chip-status-active">{{ $userCount }} user(s)</span>
                        @else
                            <span style="font-size:12px; color:#ccc;">—</span>
                        @endif
                    </td>
                    <td>
                        @if($campus->is_active)
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
                        {{ $campus->created_at->format('M d, Y') }}
                    </td>
                    <td>
                        <div class="table-actions">
                            {{-- Edit --}}
                            <button class="table-icon-btn edit" title="Edit"
                                    onclick="openEditCampusModal({{ $campus->id }}, '{{ addslashes($campus->name) }}', '{{ $campus->code }}')">
                                <i class="ti ti-edit"></i>
                            </button>
                            {{-- Toggle Active --}}
                            <form method="POST"
                                  action="{{ route('manage.campuses.toggle', $campus) }}"
                                  style="display:inline;">
                                @csrf @method('PATCH')
                                <button type="submit"
                                        class="table-icon-btn"
                                        style="background:{{ $campus->is_active ? '#fff8f0' : '#f0faf4' }};
                                               color:{{ $campus->is_active ? '#ef9f27' : 'var(--green-dark)' }};"
                                        title="{{ $campus->is_active ? 'Deactivate' : 'Activate' }}">
                                    <i class="ti ti-{{ $campus->is_active ? 'eye-off' : 'eye' }}"></i>
                                </button>
                            </form>
                            {{-- Delete --}}
                            <form method="POST"
                                  action="{{ route('manage.campuses.destroy', $campus) }}"
                                  style="display:inline;"
                                  onsubmit="return confirm('Delete campus {{ addslashes($campus->name) }}? This cannot be undone.')">
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
                            <i class="ti ti-map-pin-off"></i>
                            <p>No campuses found. {{ $search ? 'Try a different search.' : 'Click "Add Campus" to create one.' }}</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($campuses->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">
            Showing {{ $campuses->firstItem() }} to {{ $campuses->lastItem() }}
            of {{ $campuses->total() }} results
        </div>
        {{ $campuses->onEachSide(1)->links() }}
    </div>
    @endif
</div>

{{-- ADD CAMPUS MODAL --}}
<div class="modal-overlay" id="add-campus-modal">
    <div class="modal-box-sm">
        <div class="modal-header-row">
            <div class="modal-title-sm">
                <i class="ti ti-plus" style="color:#3b82f6;"></i> Add Campus
            </div>
            <button class="modal-close"
                    onclick="document.getElementById('add-campus-modal').classList.remove('open');">
                <i class="ti ti-x"></i>
            </button>
        </div>
        <form method="POST" action="{{ route('manage.campuses.store') }}">
            @csrf
            <div class="modal-form-group">
                <div class="modal-label">Campus Name *</div>
                <input type="text" name="name" class="modal-input" required
                       placeholder="e.g. South Extension Campus" autofocus>
            </div>
            <div class="modal-form-group">
                <div class="modal-label">Campus Code *</div>
                <input type="text" name="code" class="modal-input" required
                       placeholder="e.g. SOUTH"
                       style="text-transform:uppercase; font-family:monospace; letter-spacing:1px;"
                       oninput="this.value = this.value.toUpperCase()">
                <div style="font-size:11px; color:var(--text-muted); margin-top:4px;">
                    Short unique identifier. Will be stored in uppercase.
                </div>
            </div>
            <button type="submit" class="modal-btn-primary" style="background:#3b82f6;">
                <i class="ti ti-plus"></i> Add Campus
            </button>
        </form>
    </div>
</div>

{{-- EDIT CAMPUS MODAL --}}
<div class="modal-overlay" id="edit-campus-modal">
    <div class="modal-box-sm">
        <div class="modal-header-row">
            <div class="modal-title-sm">
                <i class="ti ti-edit" style="color:#7c3aed;"></i> Edit Campus
            </div>
            <button class="modal-close"
                    onclick="document.getElementById('edit-campus-modal').classList.remove('open');">
                <i class="ti ti-x"></i>
            </button>
        </div>
        <form method="POST" id="edit-campus-form">
            @csrf @method('PUT')
            <div class="modal-form-group">
                <div class="modal-label">Campus Name *</div>
                <input type="text" name="name" id="edit-campus-name"
                       class="modal-input" required>
            </div>
            <div class="modal-form-group">
                <div class="modal-label">Campus Code *</div>
                <input type="text" name="code" id="edit-campus-code"
                       class="modal-input" required
                       style="text-transform:uppercase; font-family:monospace; letter-spacing:1px;"
                       oninput="this.value = this.value.toUpperCase()">
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
function openEditCampusModal(id, name, code) {
    document.getElementById('edit-campus-name').value = name;
    document.getElementById('edit-campus-code').value = code;
    document.getElementById('edit-campus-form').action = `/manage/campuses/${id}`;
    document.getElementById('edit-campus-modal').classList.add('open');
}

document.querySelectorAll('.modal-overlay').forEach(o => {
    o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
});
</script>
@endpush