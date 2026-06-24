<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 25px 30px; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 10px; color: #222; }
        .header-row { display: table; width: 100%; margin-bottom: 10px; }
        .header-logo { display: table-cell; width: 55px; vertical-align: middle; }
        .header-logo img { width: 48px; height: 48px; }
        .header-text { display: table-cell; vertical-align: middle; padding-left: 10px; }
        .header-text h1 { font-size: 16px; color: #1a6b3a; margin: 0; }
        .header-text p { font-size: 9.5px; color: #666; margin: 2px 0 0; }
        .hr-line { border-top: 2px solid #1a6b3a; margin: 8px 0 12px; }
        .report-title-bar { background: #eaf6ee; color: #1a6b3a; text-align: center; font-weight: bold; font-size: 12px; padding: 7px 0; border-radius: 4px; }
        .filter-note { font-size: 9.5px; color: #555; margin: 8px 0; text-align: center; }
        table.report-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.report-table th { background: #1a6b3a; color: #fff; padding: 5px; font-size: 8.5px; text-align: left; }
        table.report-table td { padding: 4px 5px; border-bottom: 1px solid #eee; font-size: 9px; vertical-align: top; }
        .footer { position: fixed; bottom: -10px; left: 0; right: 0; text-align: center; font-size: 8px; color: #aaa; border-top: 1px solid #eee; padding-top: 6px; }
    </style>
</head>
<body>
    <div class="header-row">
        <div class="header-logo">
            @if($logoBase64)<img src="{{ $logoBase64 }}" alt="UCC Logo">@endif
        </div>
        <div class="header-text">
            <h1>University of Caloocan City</h1>
            <p>Inventory Management System &nbsp;|&nbsp; Biglang Awa Street, Cor 11th Ave Catleya, Caloocan City</p>
        </div>
    </div>
    <div class="hr-line"></div>

    <div class="report-title-bar">CONSUMABLES CONSUMPTION REPORT</div>
    <div class="filter-note">
        Year: <strong>{{ $year }}</strong> @if($month) &nbsp;•&nbsp; Month: <strong>{{ \Carbon\Carbon::create()->month($month)->format('F') }}</strong> @endif
        &nbsp;•&nbsp; Total Consumed: <strong>{{ $totalConsumed }}</strong>
        &nbsp;•&nbsp; Generated: {{ now()->format('F d, Y h:i A') }}
    </div>

    <table class="report-table">
        <thead><tr><th>Category</th><th>Department</th><th>Requested Items</th><th>Requests</th><th>Total Qty</th></tr></thead>
        <tbody>
            @foreach($breakdown as $catName => $deptGroup)
                @foreach($deptGroup as $deptName => $data)
                <tr>
                    <td>{{ $catName }}</td>
                    <td>{{ $deptName }}</td>
                    <td>
                        @foreach($data['items'] as $itemName => $qty)
                            {{ $itemName }} ({{ $qty }})@if(!$loop->last), @endif
                        @endforeach
                    </td>
                    <td>{{ $data['requests'] }}</td>
                    <td>{{ $data['total_qty'] }}</td>
                </tr>
                @endforeach
            @endforeach
        </tbody>
    </table>

    <div class="footer">
        This is a system-generated report &nbsp;•&nbsp; For concerns, contact the UCC-IMS Administrator &nbsp;•&nbsp; © {{ date('Y') }} University of Caloocan City. All rights reserved.
    </div>
</body>
</html>