@extends('layouts.app')
@section('title', $locationType->type_name)
@section('page-title', $locationType->type_name)

@section('content')

<a href="{{ route('inventory') }}" style="display:inline-flex; align-items:center; gap:6px; font-size:13px; color:var(--text-secondary); text-decoration:none; margin-bottom:1rem;">
    <i class="ti ti-arrow-left"></i> Back to Inventory
</a>

<div class="hero-banner" style="background: linear-gradient(135deg, {{ $locationType->color_primary }}, {{ $locationType->color_secondary }});">
    <div class="hero-left">
        <div class="hero-greeting"><i class="fa {{ $locationType->icon_class }}"></i> {{ $locationType->type_name }}</div>
        <p class="hero-sub">{{ $locationType->description }}</p>
        <div class="hero-chips">
            <div class="hero-chip"><span>Campus</span>{{ $locationType->campus->name }}</div>
            <div class="hero-chip"><span>Floor</span>{{ $locationType->type_code }}</div>
            <div class="hero-chip"><span>Total Rooms</span>{{ $locations->count() }}</div>
        </div>
    </div>
</div>

<div class="card">
    <div class="card-header">
        <div class="card-title"><i class="ti ti-door"></i> Rooms in this Floor/Type</div>
    </div>
    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr>
                    <th>Room Name</th>
                    <th>Description</th>
                    <th>Capacity</th>
                    <th>Equipment</th>
                    <th>Facilitator</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @forelse($locations as $loc)
                <tr>
                    <td>
                        <div class="cell-primary">{{ $loc->location_name }}</div>
                    </td>
                    <td style="max-width:260px;">
                        @if($loc->description)
                            <span style="font-size:12.5px; color:var(--text-secondary);">{{ $loc->description }}</span>
                        @else
                            <span class="chip-dash">—</span>
                        @endif
                    </td>
                    <td>
                        <span class="capacity-pill"><i class="ti ti-users"></i> {{ $loc->capacity }}</span>
                    </td>
                    <td>
                        @if($loc->equipment_count > 0)
                            <span class="chip-badge chip-equipment-has"><i class="ti ti-device-desktop" style="font-size:11px"></i> {{ $loc->equipment_count }} items</span>
                        @else
                            <span class="chip-badge chip-equipment-zero"><i class="ti ti-device-desktop" style="font-size:11px"></i> 0 items</span>
                        @endif
                    </td>
                    <td>
                        @if($loc->facilitator)
                            {{ $loc->facilitator->name }}
                        @else
                            <span class="chip-dash">—</span>
                        @endif
                    </td>
                    <td>
                        <div class="table-actions">
                            <a href="{{ route('locations.equipment', $loc) }}" class="table-icon-btn view" title="View Equipment">
                                <i class="ti ti-eye"></i>
                            </a>
                        </div>
                    </td>
                </tr>
                @empty
                <tr>
                    <td colspan="6">
                        <div class="empty-state">
                            <i class="ti ti-door"></i>
                            <p>No rooms found in this location type.</p>
                        </div>
                    </td>
                </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

@endsection