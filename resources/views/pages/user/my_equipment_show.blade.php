@extends('layouts.app')
@section('title', $name)
@section('page-title', 'Equipment Details')

@section('content')

<a href="{{ route('my-equipment') }}" style="display:inline-flex; align-items:center; gap:6px; font-size:13px; color:var(--text-secondary); text-decoration:none; margin-bottom:1rem;">
    <i class="ti ti-arrow-left"></i> Back to My Equipment
</a>

<div class="hero-banner">
    <div class="hero-left">
        <div class="hero-greeting"><i class="ti ti-device-desktop"></i> {{ $name }}</div>
        <p class="hero-sub">{{ $item->description ?? 'No description provided.' }}</p>
        <div class="hero-chips">
            <div class="hero-chip"><span>Type:</span>{{ $label }}</div>
            <div class="hero-chip"><span>Status:</span>{{ ucfirst($item->status) }}</div>
            <div class="hero-chip"><span>Condition:</span>{{ $item->condition_status }}</div>
        </div>
    </div>
</div>

<div class="two-col">

    <div class="card">
        <div class="card-header"><div class="card-title"><i class="ti ti-info-circle"></i> Equipment Information</div></div>
        <div class="card-body">
            <div class="detail-grid">
                @if($type === 'computer')
                    <div class="detail-row"><span>Article: </span><strong>{{ $item->article ?? '—' }}</strong></div>
                    <div class="detail-row"><span>Processor: </span><strong>{{ $item->processor ?? '—' }}</strong></div>
                    <div class="detail-row"><span>RAM: </span><strong>{{ $item->ram ?? '—' }}</strong></div>
                    <div class="detail-row"><span>Storage: </span><strong>{{ $item->storage ?? '—' }}</strong></div>
                    <div class="detail-row"><span>Operating System: </span><strong>{{ $item->operating_system ?? '—' }}</strong></div>
                    @if(($item->article ?? '') === 'Computer Package')
                        <div class="detail-row"><span>Serial (Monitor): </span><strong>{{ $item->serial_number_monitor ?? '—' }}</strong></div>
                        <div class="detail-row"><span>Serial (System Unit): </span><strong>{{ $item->serial_number_system ?? '—' }}</strong></div>
                    @else
                        <div class="detail-row"><span>Serial Number: </span><strong>{{ $item->serial_number ?? '—' }}</strong></div>
                    @endif
                @else
                    <div class="detail-row"><span>Article: </span><strong>{{ $item->article ?? '—' }}</strong></div>
                    <div class="detail-row"><span>Brand: </span><strong>{{ $item->brand ?? '—' }}</strong></div>
                    <div class="detail-row"><span>Model: </span><strong>{{ $item->model ?? '—' }}</strong></div>
                    <div class="detail-row"><span>Serial Number: </span><strong>{{ $item->serial_number ?? '—' }}</strong></div>
                @endif

                <div class="detail-row"><span>Unit: </span><strong>{{ ucfirst($item->unit ?? '—') }}</strong></div>
                <div class="detail-row"><span>Property No.: </span><strong>{{ $item->property_no ?? '—' }}</strong></div>
                <div class="detail-row"><span>Cost: </span><strong>₱{{ number_format($item->cost ?? 0, 2) }}</strong></div>
                <div class="detail-row"><span>Purchase Date: </span><strong>{{ $item->purchase_date?->format('M d, Y') ?? '—' }}</strong></div>

                @if($type === 'lab')
                <div class="detail-row"><span>Calibration Date: </span><strong>{{ $item->calibration_date?->format('M d, Y') ?? '—' }}</strong></div>
                @endif
            </div>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><div class="card-title"><i class="ti ti-map-pin"></i> Location &amp; Assignment</div></div>
        <div class="card-body">
            <div class="detail-grid">
                <div class="detail-row"><span>Campus: </span><strong>{{ $item->campus->name ?? '—' }}</strong></div>
                <div class="detail-row"><span>Location: </span><strong>{{ $item->location->location_name ?? 'Unassigned / Storage' }}</strong></div>
                <div class="detail-row"><span>Status: </span><strong>{{ ucfirst($item->status) }}</strong></div>
                <div class="detail-row"><span>Accountable Person: </span><strong>{{ $item->remarks ?? auth()->user()->name }}</strong></div>
                <div class="detail-row"><span>Date Added: </span><strong>{{ $item->created_at->format('M d, Y h:i A') }}</strong></div>
                <div class="detail-row"><span>Last Updated: </span><strong>{{ $item->updated_at->format('M d, Y h:i A') }}</strong></div>
            </div>
        </div>
    </div>

</div>

<div class="user-notice" style="margin-top:1.25rem;">
    <i class="ti ti-info-circle"></i>
    <div class="user-notice-text">
        <h4>View Only</h4>
        <p>This page shows equipment assigned to your account. To request changes or transfers, contact your campus inventory administrator.</p>
    </div>
</div>

@endsection
