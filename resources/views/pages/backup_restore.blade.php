@extends('layouts.app')
@section('title', 'Backup & Restore')
@section('page-title', 'Backup & Restore Data')

@section('content')

<a href="{{ route('system.settings') }}" style="display:inline-flex; align-items:center; gap:6px; font-size:13px; color:var(--text-secondary); text-decoration:none; margin-bottom:1rem;">
    <i class="ti ti-arrow-left"></i> Back to System Settings
</a>

<div class="hero-banner" style="background: linear-gradient(135deg, #ef9f27, #f4b942);">
    <div class="hero-left">
        <div class="hero-greeting"><i class="ti ti-database"></i> Backup &amp; Restore Data</div>
        <p class="hero-sub">Create full or selective backups, restore from previous backups, or import data from the original UCC-IMS system.</p>
    </div>
</div>

<div class="two-col">

    {{-- FULL BACKUP --}}
    <div class="card">
        <div class="card-header"><div class="card-title"><i class="ti ti-database-export"></i> Full Database Backup</div></div>
        <div class="card-body">
            <p style="font-size:13px; color:#666; margin-bottom:1rem;">Backs up every table in the database into a single .sql file.</p>
            <form method="POST" action="{{ route('system.backup.full') }}" onsubmit="return confirm('Create a full database backup now?')">
                @csrf
                <button type="submit" class="modal-btn-primary" style="background:#ef9f27;"><i class="ti ti-database-export"></i> Create Full Backup</button>
            </form>
        </div>
    </div>

    {{-- SELECTIVE BACKUP --}}
    <div class="card">
        <div class="card-header"><div class="card-title"><i class="ti ti-table"></i> Selective Table Backup</div></div>
        <div class="card-body">
            <p style="font-size:13px; color:#666; margin-bottom:0.85rem;">Choose specific tables to include in the backup.</p>
            <form method="POST" action="{{ route('system.backup.selective') }}" onsubmit="return confirm('Create a backup of the selected tables?')">
                @csrf
                <div class="table-checklist">
                    @foreach($tables as $table)
                    <label class="table-check-item">
                        <input type="checkbox" name="tables[]" value="{{ $table }}">
                        {{ $table }}
                    </label>
                    @endforeach
                </div>
                <button type="submit" class="modal-btn-primary" style="background:#ef9f27; margin-top:0.85rem;"><i class="ti ti-table"></i> Backup Selected Tables</button>
            </form>
        </div>
    </div>

</div>

{{-- IMPORT SQL --}}
<div class="card" style="margin-top:1.25rem; border-color:#c3e0ff;">
    <div class="card-header"><div class="card-title" style="color:#3b82f6;"><i class="ti ti-upload"></i> Add Data — Import from Original UCC-IMS</div></div>
    <div class="card-body">
        <p style="font-size:13px; color:#666; margin-bottom:1rem;">
            Upload a .sql file exported from the original UCC-IMS (e.g., via phpMyAdmin export) to import its data directly into this system.
        </p>
        <form method="POST" action="{{ route('system.backup.import') }}" enctype="multipart/form-data" onsubmit="return confirm('Import this SQL file? This will run the SQL directly against your database.')">
            @csrf
            <div style="display:flex; gap:10px; align-items:center;">
                <input type="file" name="sql_file" accept=".sql" required class="modal-input" style="flex:1;">
                <button type="submit" class="modal-btn-primary" style="background:#3b82f6; width:auto; padding:11px 22px; margin:0;">
                    <i class="ti ti-upload"></i> Import
                </button>
            </div>
            <div class="modal-hint">Only .sql files are accepted. Max file size: 50MB.</div>
        </form>
    </div>
</div>

{{-- BACKUP HISTORY / RESTORE --}}
<div class="card" style="margin-top:1.25rem; border-color:#fdd;">
    <div class="card-header"><div class="card-title" style="color:var(--red);"><i class="ti ti-history"></i> Backup History &amp; Restore</div></div>

    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>File Name</th>
                    <th>Size</th>
                    <th>Created</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($files as $file)
                <tr>
                    <td class="cell-primary">{{ $file['name'] }}</td>
                    <td style="font-size:12px;">{{ $file['size'] }}</td>
                    <td style="font-size:12px; color:var(--text-muted);">{{ $file['date'] }}</td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('system.backup.download', $file['name']) }}" class="table-icon-btn view" title="Download">
                                <i class="ti ti-download"></i>
                            </a>
                            <button class="table-icon-btn" style="background:#fff8f0; color:#ef9f27;" title="Restore from this backup"
                                    onclick="confirmRestore('{{ $file['name'] }}')">
                                <i class="ti ti-rotate"></i>
                            </button>
                            <form method="POST" action="{{ route('system.backup.delete', $file['name']) }}" style="display:inline">
                                @csrf @method('DELETE')
                                <button type="submit" class="table-icon-btn delete" title="Delete"
                                        onclick="return confirm('Delete this backup file permanently?')">
                                    <i class="ti ti-trash"></i>
                                </button>
                            </form>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="4">
                        <div class="empty-state">
                            <i class="ti ti-database-off"></i>
                            <p>No backups created yet.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- RESTORE CONFIRM MODAL --}}
<div class="modal-overlay" id="restore-confirm-modal">
    <div class="modal-box-sm">
        <div class="modal-header-row">
            <div class="modal-title-sm" style="color:var(--red);"><i class="ti ti-alert-triangle"></i> Confirm Restore</div>
            <button class="modal-close" onclick="document.getElementById('restore-confirm-modal').classList.remove('open');"><i class="ti ti-x"></i></button>
        </div>
        <p style="font-size:13px; color:#666; margin-bottom:1rem; line-height:1.6;">
            Restoring <strong id="restore-filename"></strong> will overwrite existing data in matching tables. <strong style="color:var(--red);">This cannot be undone.</strong> Make sure you have a current backup before proceeding.
        </p>
        <form method="POST" action="{{ route('system.backup.restore') }}" id="restore-confirm-form">
            @csrf
            <input type="hidden" name="backup_file" id="restore-confirm-file">
            <button type="submit" class="modal-btn-primary" style="background:var(--red);"><i class="ti ti-rotate"></i> Confirm Restore</button>
        </form>
    </div>
</div>

<style>
.table-checklist {
    max-height: 220px; overflow-y: auto;
    border: 1px solid var(--border); border-radius: 8px;
    padding: 0.5rem;
}
.table-check-item {
    display: flex; align-items: center; gap: 8px;
    padding: 6px 8px; font-size: 12.5px; cursor: pointer;
    border-radius: 6px;
}
.table-check-item:hover { background: var(--green-light); }
.table-check-item input { accent-color: var(--green-dark); }
</style>

@endsection

@push('scripts')
<script>
function confirmRestore(filename) {
    document.getElementById('restore-filename').textContent = filename;
    document.getElementById('restore-confirm-file').value = filename;
    document.getElementById('restore-confirm-modal').classList.add('open');
}

document.querySelectorAll('.modal-overlay').forEach(o => {
    o.addEventListener('click', e => { if (e.target === o) o.classList.remove('open'); });
});
</script>
@endpush