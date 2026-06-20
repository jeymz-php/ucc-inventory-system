<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 30px 35px; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 10.5px; color: #222; }
        .header-row { display: table; width: 100%; margin-bottom: 10px; }
        .header-logo { display: table-cell; width: 60px; vertical-align: middle; }
        .header-logo img { width: 50px; height: 50px; }
        .header-text { display: table-cell; vertical-align: middle; padding-left: 10px; }
        .header-text h1 { font-size: 17px; color: #1a6b3a; margin: 0; }
        .header-text p { font-size: 10.5px; color: #666; margin: 2px 0 0; }
        .hr-line { border-top: 2px solid #1a6b3a; margin: 8px 0 12px; }
        .report-title-bar { background: #eaf6ee; color: #1a6b3a; text-align: center; font-weight: bold; font-size: 12px; padding: 7px 0; border-radius: 4px; }
        .filter-note { font-size: 9px; color: #555; margin: 8px 0; text-align: center; }
        .filter-note strong { color: #1a6b3a; }
        table.items-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.items-table th { background: #1a6b3a; color: #fff; padding: 5px 5px; font-size: 8px; text-transform: uppercase; text-align: left; }
        table.items-table td { padding: 4px 5px; border-bottom: 1px solid #eee; font-size: 8.5px; word-wrap: break-word; }
        table.items-table tr:nth-child(even) td { background: #fafafa; }
        .footer { position: fixed; bottom: -10px; left: 0; right: 0; text-align: center; font-size: 8px; color: #aaa; border-top: 1px solid #eee; padding-top: 6px; }
</head>
<body>

    <div class="header-row">
        <div class="header-logo">
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" alt="UCC Logo">
            @else
                <div>UCC</div>
            @endif
        </div>
        <div class="header-text">
            <h1>University of Caloocan City</h1>
            <p>Inventory Management System &nbsp;|&nbsp; Biglang Awa Street, Cor 11th Ave Catleya, Caloocan City</p>
        </div>
    </div>
    <div class="hr-line"></div>

    <div class="report-title-bar">INVENTORY LIST REPORT</div>
    <div class="filter-note">
        Filtered by <strong>{{ $type === 'campus' ? 'Campus' : 'Accountable Person' }}</strong>: <strong>{{ $filterLabel }}</strong>
        &nbsp;•&nbsp; Total Items: <strong>{{ $items->count() }}</strong>
        &nbsp;•&nbsp; Generated: {{ now()->format('F d, Y h:i A') }}
    </div>

    <table class="items-table">
        <thead>
            <tr>
                <th>Equipment Name</th>
                <th>Type</th>
                <th>Article</th>
                <th>Property No.</th>
                <th>Serial Number</th>
                <th>Serial No. (Monitor)</th>
                <th>Serial No. (System Unit)</th>
                <th>Campus</th>
                <th>Location</th>
                <th>Accountable Person</th>
                <th>Status</th>
            </tr>
        </thead>
        <tbody>
            @foreach($items as $item)
            @php $isPackage = $item->equipment_type === 'Computer' && $item->article === 'Computer Package'; @endphp
            <tr>
                <td>{{ $item->display_name ?? '—' }}</td>
                <td>{{ $item->equipment_type }}</td>
                <td>{{ $item->article ?? '—' }}</td>
                <td>{{ $item->property_no ?? '—' }}</td>
                <td>{{ $isPackage ? '—' : ($item->serial_number ?? '—') }}</td>
                <td>{{ $isPackage ? ($item->serial_number_monitor ?? '—') : '—' }}</td>
                <td>{{ $isPackage ? ($item->serial_number_system ?? '—') : '—' }}</td>
                <td>{{ $item->campus->name ?? '—' }}</td>
                <td>{{ $item->location->location_name ?? 'Unassigned' }}</td>
                <td>{{ $item->remarks ?? '—' }}</td>
                <td>{{ ucfirst($item->status) }}</td>
            </tr>
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        This is a system-generated report &nbsp;•&nbsp; For concerns, contact the UCC-IMS Administrator &nbsp;•&nbsp; © {{ date('Y') }} University of Caloocan City. All rights reserved.
    </div>

</body>
</html>