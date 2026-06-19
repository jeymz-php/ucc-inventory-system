@extends('layouts.app')
@section('title', $location->location_name)
@section('page-title', $location->location_name)

@section('content')

<a href="javascript:history.back()" style="display:inline-flex; align-items:center; gap:6px; font-size:13px; color:var(--text-secondary); text-decoration:none; margin-bottom:1rem;">
    <i class="ti ti-arrow-left"></i> Back
</a>

<div class="hero-banner">
    <div class="hero-left">
        <div class="hero-greeting"><i class="ti ti-door"></i> {{ $location->location_name }}</div>
        <p class="hero-sub">{{ $location->description ?: 'Equipment currently assigned to this room.' }}</p>
        <div class="hero-chips">
            <div class="hero-chip"><span>Campus</span>{{ $location->campus->name ?? '—' }}</div>
            <div class="hero-chip"><span>Capacity</span>{{ $location->capacity }}</div>
            <div class="hero-chip"><span>Total Equipment</span>{{ $equipment->count() }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-device-desktop"></i> Equipment in this Room</div>
    </div>

    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Equipment</th>
                    <th>Type</th>
                    <th>Serial / Property No.</th>
                    <th>Condition</th>
                    <th>Status</th>
                    <th>Accountable Person</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($equipment as $item)
                <tr>
                    <td>
                        <div class="cell-primary">{{ $item->display_name ?? '—' }}</div>
                        @if($item->brand)
                        <div class="cell-secondary">{{ $item->brand }} {{ $item->model ?? '' }}</div>
                        @endif
                    </td>
                    <td><span class="chip-badge chip-type">{{ $item->equipment_type }}</span></td>
                    <td style="font-size:12px;">{{ $item->serial_number ?? $item->property_no ?? '—' }}</td>
                    <td style="font-size:12px;">{{ $item->condition_status }}</td>
                    <td>
                        @php
                            $statusColors = [
                                'available'   => 'chip-status-active',
                                'assigned'    => 'chip-campus',
                                'maintenance' => 'chip-equipment-zero',
                                'damaged'     => 'chip-status-inactive',
                                'condemned'   => 'chip-status-inactive',
                                'retired'     => 'chip-status-inactive',
                            ];
                        @endphp
                        <span class="chip-badge {{ $statusColors[$item->status] ?? 'chip-equipment-zero' }}">{{ ucfirst($item->status) }}</span>
                    </td>
                    <td style="font-size:12px;">{{ $item->remarks ?? '—' }}</td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('equipment.show', [strtolower($item->equipment_type), $item->id]) }}" class="table-icon-btn view" title="View Details">
                                <i class="ti ti-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="7">
                        <div class="empty-state">
                            <i class="ti ti-device-desktop"></i>
                            <p>No equipment assigned to this room yet.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection