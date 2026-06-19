<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 30px 40px; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 12px; color: #222; }

        .header-row { display: table; width: 100%; margin-bottom: 14px; }
        .header-logo { display: table-cell; width: 60px; vertical-align: middle; }
        .header-logo img { width: 50px; height: 50px; }
        .header-text { display: table-cell; vertical-align: middle; padding-left: 12px; }
        .header-text h1 { font-size: 17px; color: #1a6b3a; margin: 0; }
        .header-text p { font-size: 10.5px; color: #666; margin: 2px 0 0; }

        .hr-line { border-top: 2px solid #1a6b3a; margin: 8px 0 14px; }

        .report-title-bar {
            background: #eaf6ee; color: #1a6b3a;
            text-align: center; font-weight: bold;
            font-size: 13px; letter-spacing: 0.5px;
            padding: 8px 0; border-radius: 4px; margin-bottom: 4px;
        }

        .generated-note { text-align: right; font-size: 9.5px; color: #999; margin-bottom: 14px; }

        .section-bar {
            background: #1a6b3a; color: #fff;
            font-size: 11.5px; font-weight: bold;
            padding: 6px 10px; margin-top: 16px; border-radius: 3px 3px 0 0;
        }

        table.info-table { width: 100%; border-collapse: collapse; border: 1px solid #e0e0e0; border-top: none; }
        table.info-table td {
            padding: 7px 10px; border-bottom: 1px solid #eee; font-size: 11px;
        }
        table.info-table td.label { color: #888; width: 38%; }
        table.info-table td.value { font-weight: bold; color: #1a1a1a; }
        table.info-table tr:last-child td { border-bottom: none; }

        .status-pill {
            display: inline-block; padding: 2px 10px; border-radius: 10px;
            font-size: 9.5px; font-weight: bold;
            background: #eaf6ee; color: #1a6b3a;
        }
        .status-pill.condemned { background: #fdecec; color: #c0392b; }

        .two-col { display: table; width: 100%; }
        .two-col-cell { display: table-cell; width: 50%; vertical-align: top; padding-right: 10px; }

        .footer {
            position: fixed; bottom: -10px; left: 0; right: 0;
            text-align: center; font-size: 9px; color: #aaa;
            border-top: 1px solid #eee; padding-top: 6px;
        }
    </style>
</head>
<body>

    <div class="header-row">
        <div class="header-logo">
            @if($logoBase64)
                <img src="{{ $logoBase64 }}" alt="UCC Logo">
            @else
                <div style="width:50px; height:50px; border-radius:50%; background:#1a6b3a; color:#fff; text-align:center; line-height:50px; font-size:10px; font-weight:bold;">UCC</div>
            @endif
        </div>
        <div class="header-text">
            <h1>University of Caloocan City</h1>
            <p>Inventory Management System &nbsp;|&nbsp; Biglang Awa Street, Cor 11th Ave Catleya, Caloocan City</p>
        </div>
    </div>

    <div class="hr-line"></div>

    <div class="report-title-bar">EQUIPMENT INVENTORY REPORT</div>
    <div class="generated-note">Generated: {{ now()->format('F d, Y h:i A') }}</div>

    <div class="section-bar">Equipment Overview</div>
    <table class="info-table">
        <tr><td class="label">Equipment Name</td><td class="value">{{ $name }}</td></tr>
        <tr><td class="label">Category</td><td class="value">{{ ucfirst($type) }} Equipment</td></tr>
        <tr><td class="label">Status</td><td class="value">
            <span class="status-pill {{ $item->is_condemned ? 'condemned' : '' }}">{{ ucfirst($item->status) }}</span>
        </td></tr>
        <tr><td class="label">Condition</td><td class="value">{{ $item->condition_status }}</td></tr>
    </table>

    <div class="section-bar">Specifications</div>
    <table class="info-table">
        @if($type === 'computer')
            <tr><td class="label">Article</td><td class="value">{{ $item->article }}</td></tr>
            <tr><td class="label">Processor</td><td class="value">{{ $item->processor }}</td></tr>
            <tr><td class="label">RAM</td><td class="value">{{ $item->ram }}</td></tr>
            <tr><td class="label">Storage</td><td class="value">{{ $item->storage }}</td></tr>
            <tr><td class="label">Operating System</td><td class="value">{{ $item->operating_system ?? '—' }}</td></tr>
            @if($item->article === 'Computer Package')
                <tr><td class="label">Serial No. (Monitor)</td><td class="value">{{ $item->serial_number_monitor ?? '—' }}</td></tr>
                <tr><td class="label">Serial No. (System Unit)</td><td class="value">{{ $item->serial_number_system ?? '—' }}</td></tr>
            @else
                <tr><td class="label">Serial Number</td><td class="value">{{ $item->serial_number ?? '—' }}</td></tr>
            @endif
        @else
            <tr><td class="label">Article</td><td class="value">{{ $item->article ?? '—' }}</td></tr>
            <tr><td class="label">Brand</td><td class="value">{{ $item->brand ?? '—' }}</td></tr>
            <tr><td class="label">Model</td><td class="value">{{ $item->model ?? '—' }}</td></tr>
            <tr><td class="label">Serial Number</td><td class="value">{{ $item->serial_number ?? '—' }}</td></tr>
        @endif
        <tr><td class="label">Unit</td><td class="value">{{ ucfirst($item->unit) }}</td></tr>
        <tr><td class="label">Property No.</td><td class="value">{{ $item->property_no ?? '—' }}</td></tr>
        <tr><td class="label">Cost</td><td class="value">₱{{ number_format($item->cost, 2) }}</td></tr>
    </table>

    <div class="section-bar">Location &amp; Assignment</div>
    <div class="two-col">
        <div class="two-col-cell">
            <table class="info-table">
                <tr><td class="label">Campus</td><td class="value">{{ $item->campus->name ?? '—' }}</td></tr>
                <tr><td class="label">Location</td><td class="value">{{ $item->location->location_name ?? 'Unassigned' }}</td></tr>
            </table>
        </div>
        <div class="two-col-cell">
            <table class="info-table">
                <tr><td class="label">Accountable Person</td><td class="value">{{ $item->remarks ?? '—' }}</td></tr>
                <tr><td class="label">Date Added</td><td class="value">{{ $item->created_at->format('M d, Y') }}</td></tr>
            </table>
        </div>
    </div>

    @if($item->is_condemned)
    <div class="section-bar" style="background:#c0392b;">Condemnation Details</div>
    <table class="info-table">
        <tr><td class="label">Condemned Date</td><td class="value">{{ $item->condemned_date?->format('M d, Y') }}</td></tr>
        <tr><td class="label">Condemned By</td><td class="value">{{ $item->condemnedByUser->name ?? '—' }}</td></tr>
        <tr><td class="label">Reason</td><td class="value">{{ $item->condemned_reason ?? '—' }}</td></tr>
    </table>
    @endif

    <div class="footer">
        This is a system-generated report &nbsp;•&nbsp; For concerns, contact the UCC-IMS Administrator &nbsp;•&nbsp; © {{ date('Y') }} University of Caloocan City. All rights reserved.
    </div>

</body>
</html>