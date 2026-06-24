@extends('layouts.app')
@section('title', 'Consumption Reports')
@section('page-title', 'Consumption Reports')

@section('content')

<a href="{{ route('consumables') }}" style="display:inline-flex; align-items:center; gap:6px; font-size:13px; color:var(--text-secondary); text-decoration:none; margin-bottom:1rem;">
    <i class="ti ti-arrow-left"></i> Back to Consumables
</a>

<div style="display:flex; gap:10px; margin-bottom:1.25rem;">
    <a href="{{ route('consumable-requests') }}" class="tab-toggle-btn"><i class="ti ti-history"></i> Request History</a>
    <a href="{{ route('consumables.reports') }}" class="tab-toggle-btn active"><i class="ti ti-chart-line"></i> Consumption Reports</a>
</div>

{{-- Filters --}}
<div class="card" style="margin-bottom:1.25rem;">
    <div class="card-body" style="padding:1.1rem 1.25rem;">
        <form method="GET" action="{{ route('consumables.reports') }}" id="report-filter-form">
            <div class="modal-grid" style="grid-template-columns: repeat(4, 1fr);">
                <div class="modal-form-group">
                    <div class="modal-label">Select Year</div>
                    <select name="year" class="modal-input" onchange="this.form.submit()">
                        @foreach($years as $y)
                        <option value="{{ $y }}" {{ $year == $y ? 'selected' : '' }}>{{ $y }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Filter by Month</div>
                    <select name="month" class="modal-input" onchange="this.form.submit()">
                        <option value="">All Months</option>
                        @foreach(['1'=>'Jan','2'=>'Feb','3'=>'Mar','4'=>'Apr','5'=>'May','6'=>'Jun','7'=>'Jul','8'=>'Aug','9'=>'Sep','10'=>'Oct','11'=>'Nov','12'=>'Dec'] as $num => $name)
                        <option value="{{ $num }}" {{ $month == $num ? 'selected' : '' }}>{{ $name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Filter by Category</div>
                    <select name="category_id" class="modal-input" onchange="this.form.submit()">
                        <option value="">All Categories</option>
                        @foreach($categories as $cat)
                        <option value="{{ $cat->id }}" {{ $categoryId == $cat->id ? 'selected' : '' }}>{{ $cat->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="modal-form-group">
                    <div class="modal-label">Filter by Department</div>
                    <select name="department" class="modal-input" onchange="this.form.submit()">
                        <option value="">All Departments</option>
                        @foreach($departments as $dept)
                        <option value="{{ $dept }}" {{ $department == $dept ? 'selected' : '' }}>{{ $dept }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div style="display:flex; gap:8px; margin-top:0.75rem;">
                <a href="{{ route('consumables.reports.excel', request()->query()) }}" class="btn-table-action green"><i class="ti ti-file-spreadsheet"></i> Export to Excel</a>
                <a href="{{ route('consumables.reports.pdf', request()->query()) }}" target="_blank" class="btn-table-action" style="background:#fff5f5; color:var(--red);"><i class="ti ti-file-text"></i> Export to PDF</a>
            </div>
        </form>
    </div>
</div>

{{-- Stats --}}
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon blue"><i class="ti ti-package"></i></div>
        <div><div class="stat-value">{{ $stats['total_consumed'] }}</div><div class="stat-label">Total Consumption</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon green"><i class="ti ti-clipboard-check"></i></div>
        <div><div class="stat-value">{{ $stats['total_requests'] }}</div><div class="stat-label">Total Requests</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon orange"><i class="ti ti-category"></i></div>
        <div><div class="stat-value">{{ $stats['active_categories'] }}</div><div class="stat-label">Active Categories</div></div>
    </div>
    <div class="stat-card">
        <div class="stat-icon red"><i class="ti ti-building"></i></div>
        <div><div class="stat-value">{{ $stats['departments_served'] }}</div><div class="stat-label">Departments Served</div></div>
    </div>
</div>

{{-- Charts --}}
<div class="two-col">
    <div class="card">
        <div class="card-header"><div class="card-title"><i class="ti ti-chart-bar"></i> Monthly Consumption Trend</div></div>
        <div class="card-body"><canvas id="monthlyChart" height="180"></canvas></div>
    </div>
    <div class="card">
        <div class="card-header"><div class="card-title"><i class="ti ti-chart-pie"></i> Top Requested Items</div></div>
        <div class="card-body"><canvas id="topItemsChart" height="180"></canvas></div>
    </div>
</div>

{{-- Breakdown Table --}}
<div class="card" style="margin-bottom:1.25rem;">
    <div class="card-header"><div class="card-title"><i class="ti ti-table"></i> Detailed Breakdown</div></div>
    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr><th>Category</th><th>Department</th><th>Requested Items</th><th>Requests</th><th>Total Qty</th></tr>
            </thead>
            <tbody>
                @forelse($breakdown as $catName => $deptGroup)
                    @foreach($deptGroup as $deptName => $data)
                    <tr>
                        <td><span class="chip-badge chip-type">{{ $catName }}</span></td>
                        <td style="font-size:12.5px;">{{ $deptName }}</td>
                        <td style="font-size:12px;">
                            @foreach($data['items'] as $itemName => $qty)
                            <div style="margin-bottom:2px;">{{ $itemName }} <span class="chip-badge chip-equipment-has" style="margin-left:4px;">{{ $qty }}</span></div>
                            @endforeach
                        </td>
                        <td>{{ $data['requests'] }}</td>
                        <td><strong>{{ $data['total_qty'] }}</strong></td>
                    </tr>
                    @endforeach
                @empty
                <tr><td colspan="5"><div class="empty-state"><i class="ti ti-chart-bar-off"></i><p>No consumption data for the selected filters.</p></div></td></tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Stock Health --}}
<div class="card">
    <div class="card-header"><div class="card-title"><i class="ti ti-heart-rate-monitor"></i> Stock Health by Category</div></div>
    <div class="data-table-wrap">
        <table class="data-table">
            <thead>
                <tr><th>Category</th><th>Total Items</th><th>Total Stock</th><th>Avg Stock</th><th>In Stock</th><th>Low</th><th>Critical</th><th>Brands</th></tr>
            </thead>
            <tbody>
                @foreach($stockByCategory as $catName => $data)
                <tr>
                    <td class="cell-primary">{{ $catName }}</td>
                    <td>{{ $data['total_items'] }}</td>
                    <td>{{ $data['total_stock'] }}</td>
                    <td>{{ $data['avg_stock'] }}</td>
                    <td><span class="chip-badge chip-status-active">{{ $data['in_stock'] }}</span></td>
                    <td><span class="chip-badge" style="background:#fff8f0; color:#ef9f27;">{{ $data['low_stock'] }}</span></td>
                    <td><span class="chip-badge chip-status-inactive">{{ $data['critical_stock'] }}</span></td>
                    <td>{{ $data['unique_brands'] }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@4.4.0/dist/chart.umd.min.js"></script>
<script>
const monthlyData = {!! json_encode(array_values($monthlyTrend)) !!};
const topItemsLabels = {!! json_encode($topItems->keys()) !!};
const topItemsData = {!! json_encode($topItems->values()) !!};

new Chart(document.getElementById('monthlyChart'), {
    type: 'bar',
    data: {
        labels: ['Jan','Feb','Mar','Apr','May','Jun','Jul','Aug','Sep','Oct','Nov','Dec'],
        datasets: [{ label: 'Items Released', data: monthlyData, backgroundColor: '#1a6b3a', borderRadius: 6 }]
    },
    options: { responsive: true, plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true } } }
});

new Chart(document.getElementById('topItemsChart'), {
    type: 'doughnut',
    data: {
        labels: topItemsLabels,
        datasets: [{ data: topItemsData, backgroundColor: ['#1a6b3a','#3b82f6','#ef9f27','#e24b4a','#7c3aed','#20c997','#f4b942','#94a3b8'] }]
    },
    options: { responsive: true, plugins: { legend: { position: 'right', labels: { boxWidth: 12, font: { size: 11 } } } } }
});
</script>
@endpush