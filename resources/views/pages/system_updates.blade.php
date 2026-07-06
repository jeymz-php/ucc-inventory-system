@extends('layouts.app')
@section('title', 'Version History & Updates')
@section('page-title', 'Version History & Updates')

@section('content')

<a href="{{ route('system.settings') }}" style="display:inline-flex; align-items:center; gap:6px; font-size:13px; color:var(--text-secondary); text-decoration:none; margin-bottom:1rem;">
    <i class="ti ti-arrow-left"></i> Back to System Settings
</a>

<div class="hero-banner" style="margin-bottom:1.25rem;">
    <div class="hero-left">
        <div class="hero-greeting"><i class="ti ti-git-branch"></i> Version History & Updates</div>
        <p class="hero-sub">Publish system update notes for UCC-IMS and UCC-CS. Toggle the login modal to notify users of new features.</p>
    </div>
    <div class="hero-right">
        <button class="btn-add" onclick="document.getElementById('add-update-modal').classList.add('open');">
            <i class="ti ti-plus"></i> New Update
        </button>
    </div>
</div>

{{-- Updates Table --}}
<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-history"></i> Published Updates ({{ $updates->total() }})</div>
    </div>
    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Version</th>
                    <th>Title</th>
                    <th>System</th>
                    <th>Show Modal</th>
                    <th>Published By</th>
                    <th>Date</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($updates as $update)
                <tr>
                    <td>
                        <span class="chip-badge chip-campus" style="font-family:monospace; font-size:12px;">
                            {{ $update->version }}
                        </span>
                    </td>
                    <td>
                        <div class="cell-primary">{{ $update->title }}</div>
                        <div class="cell-secondary">{{ Str::limit($update->content, 60) }}</div>
                    </td>
                    <td>
                        @if($update->system === 'ims')
                            <span class="chip-badge chip-type"><i class="ti ti-device-desktop" style="font-size:10px;"></i> IMS</span>
                        @elseif($update->system === 'cs')
                            <span class="chip-badge" style="background:#eff6ff; color:#1a56db;"><i class="ti ti-package" style="font-size:10px;"></i> CS</span>
                        @else
                            <span class="chip-badge chip-campus"><i class="ti ti-stack" style="font-size:10px;"></i> Both</span>
                        @endif
                    </td>
                    <td>
                        <button class="toggle-modal-btn {{ $update->show_modal ? 'toggle-on' : 'toggle-off' }}"
                                onclick="toggleModal({{ $update->id }}, this)"
                                title="{{ $update->show_modal ? 'Click to disable modal' : 'Click to enable modal' }}">
                            <span class="toggle-dot"></span>
                            <span class="toggle-label">{{ $update->show_modal ? 'ON' : 'OFF' }}</span>
                        </button>
                    </td>
                    <td style="font-size:12px;">{{ $update->author->name ?? '—' }}</td>
                    <td style="font-size:11.5px; color:var(--text-muted);">{{ $update->created_at->format('M d, Y') }}</td>
                    <td>
                        <div class="table-actions">
                            <button class="table-icon-btn view" title="Preview"
                                    onclick="previewUpdate({{ $update->id }}, '{{ addslashes($update->version) }}', '{{ addslashes($update->title) }}', {{ json_encode($update->content) }}, '{{ $update->system }}')">
                                <i class="ti ti-eye"></i>
                            </button>
                            <button class="table-icon-btn edit" title="Edit"
                                    onclick="openEditModal({{ $update->id }}, '{{ addslashes($update->version) }}', '{{ addslashes($update->title) }}', '{{ $update->system }}', {{ json_encode($update->content) }}, {{ $update->show_modal ? 'true' : 'false' }})">
                                <i class="ti ti-edit"></i>
                            </button>
                            <form method="POST" action="{{ route('system.updates.destroy', $update) }}" style="display:inline;"
                                  onsubmit="return confirm('Delete this version entry?')">
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
                            <i class="ti ti-git-branch-deleted"></i>
                            <p>No updates published yet. Click "New Update" to add one.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
    @if($updates->hasPages())
    <div class="pagination-wrap">
        <div class="pagination-info">Showing {{ $updates->firstItem() }} to {{ $updates->lastItem() }} of {{ $updates->total() }} results</div>
        {{ $updates->onEachSide(1)->links() }}
    </div>
    @endif
</div>

{{-- ADD UPDATE MODAL --}}
<div class="modal-overlay" id="add-update-modal">
    <div class="modal-box-lg" style="max-width:620px;">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-plus"></i> Publish New Update</div>
            <button class="modal-close" onclick="document.getElementById('add-update-modal').classList.remove('open');"><i class="ti ti-x"></i></button>
        </div>
        <form method="POST" action="{{ route('system.updates.store') }}">
            @csrf
            <div class="modal-grid">
                <div class="modal-form-group">
                    <div class="modal-label">Version Number *</div>
                    <input type="text" name="version" class="modal-input"
                           value="{{ $nextVersion }}" required
                           placeholder="e.g. v2.1.0" style="font-family:monospace;">
                    <div class="modal-hint" style="color:#888;">Auto-generated. Edit if needed.</div>
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Applies To *</div>
                    <select name="system" class="modal-input" required>
                        <option value="both">Both (IMS + CS)</option>
                        <option value="ims">IMS Only</option>
                        <option value="cs">CS Only</option>
                    </select>
                </div>
            </div>
            <div class="modal-form-group">
                <div class="modal-label">Update Title *</div>
                <input type="text" name="title" class="modal-input" required
                       placeholder="e.g. July 2026 Feature Update">
            </div>
            <div class="modal-form-group">
                <div class="modal-label">Update Notes *</div>
                <textarea name="content" class="modal-input" rows="7" required
                          style="padding-top:10px; resize:vertical; font-size:13px;"
                          placeholder="• Added new consumable request tracking&#10;• Fixed equipment transfer bug&#10;• Improved mobile responsiveness&#10;• Added Source column to Users page"></textarea>
                <div class="modal-hint" style="color:#888;">Use bullet points (•) for each improvement.</div>
            </div>
            <div style="display:flex; align-items:center; gap:10px; padding:12px 14px; background:var(--green-light); border-radius:10px; margin-bottom:1rem;">
                <input type="checkbox" name="show_modal" id="add-show-modal" value="1"
                       style="width:18px; height:18px; accent-color:var(--green-dark); cursor:pointer; flex-shrink:0;">
                <label for="add-show-modal" style="font-size:13px; color:var(--text-primary); cursor:pointer; line-height:1.4;">
                    <strong>Show "What's New" modal on login</strong><br>
                    <span style="font-size:11.5px; color:var(--text-muted);">Users will see this update as a popup the next time they log in. Only one update can show per system at a time.</span>
                </label>
            </div>
            <button type="submit" class="modal-btn-primary">
                <i class="ti ti-send"></i> Publish Update
            </button>
        </form>
    </div>
</div>

{{-- EDIT UPDATE MODAL --}}
<div class="modal-overlay" id="edit-update-modal">
    <div class="modal-box-lg" style="max-width:620px;">
        <div class="modal-header-row">
            <div class="modal-title-sm"><i class="ti ti-edit"></i> Edit Update</div>
            <button class="modal-close" onclick="document.getElementById('edit-update-modal').classList.remove('open');"><i class="ti ti-x"></i></button>
        </div>
        <form method="POST" id="edit-update-form">
            @csrf @method('PUT')
            <div class="modal-grid">
                <div class="modal-form-group">
                    <div class="modal-label">Version Number *</div>
                    <input type="text" name="version" id="edit-version" class="modal-input"
                           required style="font-family:monospace;">
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Applies To *</div>
                    <select name="system" id="edit-system" class="modal-input" required>
                        <option value="both">Both (IMS + CS)</option>
                        <option value="ims">IMS Only</option>
                        <option value="cs">CS Only</option>
                    </select>
                </div>
            </div>
            <div class="modal-form-group">
                <div class="modal-label">Update Title *</div>
                <input type="text" name="title" id="edit-title" class="modal-input" required>
            </div>
            <div class="modal-form-group">
                <div class="modal-label">Update Notes *</div>
                <textarea name="content" id="edit-content" class="modal-input" rows="7" required
                          style="padding-top:10px; resize:vertical; font-size:13px;"></textarea>
            </div>
            <div style="display:flex; align-items:center; gap:10px; padding:12px 14px; background:var(--green-light); border-radius:10px; margin-bottom:1rem;">
                <input type="checkbox" name="show_modal" id="edit-show-modal" value="1"
                       style="width:18px; height:18px; accent-color:var(--green-dark); cursor:pointer; flex-shrink:0;">
                <label for="edit-show-modal" style="font-size:13px; color:var(--text-primary); cursor:pointer; line-height:1.4;">
                    <strong>Show "What's New" modal on login</strong><br>
                    <span style="font-size:11.5px; color:var(--text-muted);">Users will see this update as a popup the next time they log in.</span>
                </label>
            </div>
            <button type="submit" class="modal-btn-primary">
                <i class="ti ti-device-floppy"></i> Save Changes
            </button>
        </form>
    </div>
</div>

{{-- PREVIEW MODAL --}}
<div class="modal-overlay" id="preview-update-modal">
    <div class="modal-box-lg" style="max-width:520px;">
        <div class="modal-header-row" style="background:var(--green-dark); margin:-1.5rem -1.5rem 1.5rem; padding:1.25rem 1.5rem; border-radius:14px 14px 0 0;">
            <div class="modal-title-sm" style="color:#fff;">
                <i class="ti ti-sparkles"></i>
                <span id="preview-version" style="font-family:monospace; margin-right:8px;"></span>
                <span id="preview-title"></span>
            </div>
            <button class="modal-close" onclick="document.getElementById('preview-update-modal').classList.remove('open');" style="background:rgba(255,255,255,0.15); color:#fff; border-color:transparent;"><i class="ti ti-x"></i></button>
        </div>
        <div id="preview-system-badge" style="margin-bottom:1rem;"></div>
        <div style="font-size:13px; line-height:1.85; color:var(--text-primary); white-space:pre-wrap; background:#fafafa; border-radius:10px; padding:1rem 1.2rem; max-height:360px; overflow-y:auto;" id="preview-content"></div>
        <button type="button" class="modal-btn-primary" style="margin-top:1rem;" onclick="document.getElementById('preview-update-modal').classList.remove('open');">
            <i class="ti ti-check"></i> Close Preview
        </button>
    </div>
</div>

<style>
.toggle-modal-btn {
    display: inline-flex; align-items: center; gap: 6px;
    padding: 5px 10px; border-radius: 20px;
    border: 2px solid; cursor: pointer;
    font-size: 11px; font-weight: 700;
    font-family: inherit; transition: all 0.2s;
}
.toggle-modal-btn.toggle-on {
    background: var(--green-light); border-color: var(--green-dark); color: var(--green-dark);
}
.toggle-modal-btn.toggle-off {
    background: #f5f5f5; border-color: #ccc; color: #999;
}
.toggle-dot {
    width: 8px; height: 8px; border-radius: 50%;
    background: currentColor; flex-shrink: 0;
}
</style>

@endsection

@push('scripts')
<script>
async function toggleModal(id, btn) {
    try {
        const res  = await fetch(`/settings/updates/${id}/toggle`, {
            method: 'PATCH',
            headers: { 'X-CSRF-TOKEN': '{{ csrf_token() }}' }
        });
        const data = await res.json();

        if (data.show_modal) {
            btn.classList.remove('toggle-off');
            btn.classList.add('toggle-on');
            btn.innerHTML = '<span class="toggle-dot"></span><span class="toggle-label">ON</span>';
            btn.title = 'Click to disable modal';
        } else {
            btn.classList.remove('toggle-on');
            btn.classList.add('toggle-off');
            btn.innerHTML = '<span class="toggle-dot"></span><span class="toggle-label">OFF</span>';
            btn.title = 'Click to enable modal';
        }

        // Reset all other toggles in the same system to OFF
        document.querySelectorAll('.toggle-modal-btn.toggle-on').forEach(b => {
            if (b !== btn) {
                b.classList.remove('toggle-on');
                b.classList.add('toggle-off');
                b.innerHTML = '<span class="toggle-dot"></span><span class="toggle-label">OFF</span>';
            }
        });
    } catch (e) {
        alert('Failed to toggle modal status.');
    }
}

function openEditModal(id, version, title, system, content, showModal) {
    document.getElementById('edit-version').value      = version;
    document.getElementById('edit-title').value        = title;
    document.getElementById('edit-system').value       = system;
    document.getElementById('edit-content').value      = content;
    document.getElementById('edit-show-modal').checked = showModal;
    document.getElementById('edit-update-form').action = `/settings/updates/${id}`;
    document.getElementById('edit-update-modal').classList.add('open');
}

function previewUpdate(id, version, title, content, system) {
    const systemLabels = {
        ims:  '<span class="chip-badge chip-type"><i class="ti ti-device-desktop" style="font-size:10px;"></i> IMS Only</span>',
        cs:   '<span class="chip-badge" style="background:#eff6ff;color:#1a56db;"><i class="ti ti-package" style="font-size:10px;"></i> CS Only</span>',
        both: '<span class="chip-badge chip-campus"><i class="ti ti-stack" style="font-size:10px;"></i> Both Systems</span>',
    };
    document.getElementById('preview-version').textContent = version;
    document.getElementById('preview-title').textContent   = title;
    document.getElementById('preview-content').textContent = content;
    document.getElementById('preview-system-badge').innerHTML = systemLabels[system] || '';
    document.getElementById('preview-update-modal').classList.add('open');
}

document.querySelectorAll('.modal-overlay').forEach(o => {
    o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
});
</script>
@endpush