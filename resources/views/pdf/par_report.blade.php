<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 28px 36px; }
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; font-size: 11px; color: #111; margin: 0; padding: 0; }

        /* ── HEADER ── */
        .header-wrap { width: 100%; display: table; margin-bottom: 6px; }
        .header-logo-cell {
            display: table-cell;
            width: 68px;
            vertical-align: middle;
            text-align: center;
        }
        .header-logo-cell img { width: 58px; height: 58px; object-fit: contain; }
        .header-text-cell {
            display: table-cell;
            vertical-align: middle;
            padding-left: 14px;
        }
        .header-title { font-size: 15px; font-weight: bold; color: #1a6b3a; letter-spacing: 0.5px; }
        .header-sub   { font-size: 10.5px; color: #555; margin-top: 2px; }

        .hr-line { border: none; border-top: 2.5px solid #1a6b3a; margin: 6px 0 10px; }

        /* ── DOCUMENT TITLE ── */
        .doc-title-wrap { text-align: center; margin-bottom: 8px; }
        .doc-title { font-size: 13.5px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #111; }
        .doc-subtitle { font-size: 10px; color: #555; margin-top: 2px; }
        .doc-title-underline { border-top: 1.5px solid #111; width: 60%; margin: 4px auto 0; }

        /* ── META ROW ── */
        .meta-row { display: table; width: 100%; margin-bottom: 8px; }
        .meta-cell { display: table-cell; width: 33.33%; font-size: 10px; color: #555; }
        .meta-cell.right { text-align: right; }
        .meta-cell .meta-value { font-weight: bold; color: #111; font-size: 10.5px; }

        /* ── ACCOUNTABLE PERSON ── */
        .accountable-wrap { margin-bottom: 8px; padding: 6px 10px; background: #f4faf6; border: 1px solid #c6e9d3; border-radius: 4px; }
        .accountable-label { font-size: 9px; font-weight: bold; text-transform: uppercase; letter-spacing: 1px; color: #888; margin-bottom: 2px; }
        .accountable-name  { font-size: 12.5px; font-weight: bold; color: #1a6b3a; text-transform: uppercase; }

        /* ── TABLE ── */
        table.par-table { width: 100%; border-collapse: collapse; margin-bottom: 6px; }
        table.par-table thead tr th {
            background: #1a6b3a; color: #fff;
            font-size: 9.5px; font-weight: bold;
            text-align: center; padding: 6px 4px;
            border: 1px solid #155a30;
            text-transform: uppercase; letter-spacing: 0.3px;
        }
        table.par-table tbody tr td {
            border: 1px solid #ccc;
            padding: 5px 4px;
            font-size: 10px; color: #111;
            vertical-align: middle;
        }
        table.par-table tbody tr td.center { text-align: center; }
        table.par-table tbody tr td.right  { text-align: right; }
        table.par-table tbody tr.empty-row td { height: 18px; color: #bbb; }
        table.par-table tbody tr.total-row td {
            background: #f4faf6;
            font-weight: bold; font-size: 10.5px;
            border-top: 2px solid #1a6b3a;
        }

        /* ── SIGNATORIES ── */
        .sig-wrap { display: table; width: 100%; margin-top: 18px; }
        .sig-cell { display: table-cell; width: 50%; padding: 0 8px; vertical-align: top; }
        .sig-label { font-size: 9px; color: #888; text-transform: uppercase; letter-spacing: 0.5px; margin-bottom: 20px; }
        .sig-name  { font-size: 11px; font-weight: bold; color: #1a6b3a; text-transform: uppercase; text-align: center; border-top: 1.5px solid #333; padding-top: 4px; }
        .sig-role  { font-size: 9.5px; color: #555; text-align: center; font-style: italic; margin-top: 2px; }
        .sig-date  { font-size: 9.5px; color: #1a6b3a; font-weight: bold; text-align: center; margin-top: 2px; }

        /* ── FOOTER ── */
        .footer-wrap { margin-top: 16px; display: table; width: 100%; }
        .footer-left  { display: table-cell; vertical-align: middle; }
        .footer-right { display: table-cell; vertical-align: middle; text-align: right; width: 70px; }
        .footer-right img { width: 94px; height: 48px; object-fit: contain; }
        .footer-text { font-size: 8.5px; color: #aaa; }
        .footer-ref  { font-size: 9px; font-weight: bold; color: #888; }
    </style>
</head>
<body>

    {{-- HEADER: UCC logo left, title immediately to the right --}}
    <div class="header-wrap">
        <div class="header-logo-cell">
            @if($headerLogoBase64)
            <img src="{{ $headerLogoBase64 }}" alt="UCC Logo">
            @endif
        </div>
        <div class="header-text-cell">
            <div class="header-title">University of Caloocan City</div>
            <div class="header-sub">Inventory Management System</div>
            <div class="header-sub">Biglang Awa Street, Cor 11th Ave Catleya, Caloocan City</div>
        </div>
    </div>

    <hr class="hr-line">

    {{-- DOCUMENT TITLE --}}
    <div class="doc-title-wrap">
        <div class="doc-title">Property Acknowledgement Receipt</div>
        <div class="doc-subtitle">Caloocan City Government &nbsp;•&nbsp; LGU</div>
        <div class="doc-title-underline"></div>
    </div>

    {{-- META --}}
    <div class="meta-row">
        <div class="meta-cell">
            <span>PAR No.: </span><span class="meta-value">{{ $parNo }}</span>
        </div>
        <div class="meta-cell" style="text-align:center;">
            <span>Date: </span><span class="meta-value">{{ now()->format('F d, Y') }}</span>
        </div>
        <div class="meta-cell right">
            <span>Total Items: </span><span class="meta-value">{{ $items->count() }}</span>
        </div>
    </div>

    {{-- ACCOUNTABLE PERSON --}}
    <div class="accountable-wrap">
        <div class="accountable-label">Accountable Person</div>
        <div class="accountable-name">{{ $accountablePerson }}</div>
    </div>

    {{-- ITEMS TABLE --}}
    <table class="par-table">
        <thead>
            <tr>
                <th style="width:6%;">QTY</th>
                <th style="width:7%;">UNIT</th>
                <th style="width:30%;">DESCRIPTION</th>
                <th style="width:12%;">ARTICLE / TYPE</th>
                <th style="width:15%;">ESTIMATED USEFUL LIFE</th>
                <th style="width:15%;">PROPERTY NO.</th>
                <th style="width:15%;">AMOUNT</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalAmount = 0;
                $minRows     = 15;
                $itemCount   = $items->count();
            @endphp

            @foreach($items as $item)
            @php
                $amount = $item->cost ?? 0;
                $totalAmount += $amount;
                $name   = $item->display_name ?? $item->equipment_name ?? $item->article ?? '—';
                $type   = $item->equipment_type ?? '—';
                $propNo = $item->property_no ?? 'N/A';
                $unit   = $item->unit ?? 'pcs';
            @endphp
            <tr>
                <td class="center">1</td>
                <td class="center">{{ $unit }}</td>
                <td>{{ strtoupper($name) }}</td>
                <td class="center">{{ strtoupper($type) }}</td>
                <td class="center">—</td>
                <td class="center">{{ $propNo }}</td>
                <td class="right">₱ {{ number_format($amount, 2) }}</td>
            </tr>
            @endforeach

            {{-- Fill empty rows up to minimum --}}
            @for($i = $itemCount; $i < $minRows; $i++)
            <tr class="empty-row">
                <td></td><td></td><td></td><td></td><td></td><td></td><td></td>
            </tr>
            @endfor

            {{-- Total row --}}
            <tr class="total-row">
                <td colspan="6" class="right" style="padding-right:8px;">TOTAL</td>
                <td class="right">₱ {{ number_format($totalAmount, 2) }}</td>
            </tr>
        </tbody>
    </table>

    {{-- SIGNATORIES --}}
    <div class="sig-wrap">
        <div class="sig-cell">
            <div class="sig-label">Received from:</div>
            <div class="sig-name">Reynaldo H. Carandang Jr.</div>
            <div class="sig-role">AVP for Administration</div>
            <div class="sig-date">{{ now()->format('F d, Y') }}</div>
            <div class="sig-role">Date</div>
        </div>
        <div class="sig-cell">
            <div class="sig-label">Received by:</div>
            <div class="sig-name">{{ $accountablePerson }}</div>
            <div class="sig-role">Accountable Person</div>
            <div class="sig-date">{{ now()->format('F d, Y') }}</div>
            <div class="sig-role">Date</div>
        </div>
    </div>

    {{-- FOOTER: ref left, Caloocan logo right --}}
    <div class="footer-wrap">
        <div class="footer-left">
            <div class="footer-ref">PAR No.: {{ $parNo }}</div>
            <div class="footer-text">Generated: {{ now()->format('F d, Y h:i A') }} &nbsp;•&nbsp; UCC Inventory Management System</div>
            <div class="footer-text">University of Caloocan City &nbsp;•&nbsp; Biglang Awa Street, Cor 11th Ave Catleya, Caloocan City</div>
        </div>
        <div class="footer-right">
            @if($footerLogoBase64)
            <img src="{{ $footerLogoBase64 }}" alt="Caloocan">
            @endif
        </div>
    </div>

</body>
</html>