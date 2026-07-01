<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <style>
        @page { margin: 30px 35px; }
        body { font-family: Helvetica, Arial, sans-serif; font-size: 11px; color: #222; }

        .header-table { width: 100%; margin-bottom: 14px; border-collapse: collapse; }
        .header-table td { border: none; padding: 0; vertical-align: middle; }
        .header-logo-cell { width: 70px; }
        .header-logo { width: 60px; height: 60px; }
        .header-text-cell { text-align: left; padding-left: 10px; }
        .header-title { font-size: 15px; font-weight: bold; color: #111; margin: 0; }
        .header-sub { font-size: 11px; color: #444; margin: 2px 0 0; }
        .header-doc-title { font-size: 11.5px; font-weight: bold; text-decoration: underline; margin-top: 4px; }

        table.info-table { width: 100%; border-collapse: collapse; margin-bottom: 4px; }
        table.info-table td { border: 1px solid #999; padding: 6px 8px; font-size: 10.5px; }
        table.info-table td.label { font-weight: bold; width: 12%; background: #f7f7f7; }
        table.info-table td.value { color: #1a4fa3; width: 21%; }

        table.items-table { width: 100%; border-collapse: collapse; margin-top: 10px; }
        table.items-table th { border: 1px solid #999; background: #f0f0f0; padding: 6px; font-size: 10px; text-transform: uppercase; }
        table.items-table td { border: 1px solid #999; padding: 6px 8px; font-size: 10.5px; height: 18px; }
        table.items-table td.num { text-align: center; width: 4%; }
        td.purpose-cell { color: #1a4fa3; }

        .total-row td { font-weight: bold; background: #f7f7f7; }

        .sig-section { display: table; width: 100%; margin-top: 30px; }
        .sig-col { display: table-cell; width: 33.33%; text-align: center; vertical-align: top; padding: 0 8px; }
        .sig-label-top { font-size: 10px; text-align: left; margin-bottom: 30px; }
        .sig-name { font-weight: bold; text-decoration: underline; font-size: 11px; }
        .sig-role { font-size: 9.5px; color: #555; margin-top: 2px; }
        .sig-line { border-top: 1px solid #000; margin-top: 4px; }

        .received-section { margin-top: 24px; }
        .received-label { font-size: 10px; }
        .received-line { border-top: 1px solid #000; width: 320px; margin: 30px 0 4px; }

        .doc-note { font-size: 9px; font-style: italic; color: #555; text-align: right; margin-top: 16px; }

        .footer {
            position: fixed; bottom: -15px; left: 0; right: 0;
            border-top: 1px solid #ccc; padding-top: 6px;
        }
        .footer-table { display: table; width: 100%; }
        .footer-cell { display: table-cell; vertical-align: middle; font-size: 8.5px; color: #666; }
        .footer-left { width: 33%; text-align: left; }
        .footer-center { width: 34%; text-align: center; }
        .footer-right { width: 33%; text-align: right; }
        .footer-logo { width: 94px; height: 48px; }
    </style>
</head>
<body>

    <table class="header-table">
        <tr>
            <td class="header-logo-cell">
                @if($headerLogoBase64)<img src="{{ $headerLogoBase64 }}" class="header-logo">@endif
            </td>
            <td class="header-text-cell">
                <div class="header-title">UNIVERSITY OF CALOOCAN CITY</div>
                <div class="header-sub">CONSUMABLE MANAGEMENT SYSTEM</div>
                <div class="header-doc-title">{{ $blankReceipt ?? false ? 'CONSUMABLE RECEIPT REPORT' : 'OFFICIAL CONSUMABLE RELEASE REPORT' }}</div>
            </td>
        </tr>
    </table>

    <table class="info-table">
        <tr>
            <td class="label">Ref No.:</td>
            <td class="value">{{ $blankReceipt ?? false ? '' : $consumableRequest->reference_no }}</td>
            <td class="label">Date:</td>
            <td class="value">{{ $blankReceipt ?? false ? '' : $consumableRequest->request_date->format('M d, Y') }}</td>
            <td class="label">Total:</td>
            <td class="value">{{ $consumableRequest->items->count() }} items</td>
        </tr>
        <tr>
            <td class="label">Recipient:</td>
            <td class="value">{{ $blankReceipt ?? false ? '' : $consumableRequest->recipient_name }}</td>
            <td class="label">Office:</td>
            <td class="value" colspan="3">{{ $blankReceipt ?? false ? '' : $consumableRequest->department }}</td>
        </tr>
        <tr>
            <td class="label">Approved By:</td>
            <td class="value">{{ $blankReceipt ?? false ? '' : $consumableRequest->approved_by }}</td>
            <td class="label">Supply Officer:</td>
            <td class="value" colspan="3">{{ $blankReceipt ?? false ? '' : $consumableRequest->supply_officer }}</td>
        </tr>
    </table>

    <table class="items-table">
        <thead>
            <tr>
                <th style="width:4%;">#</th>
                <th style="width:36%;">Item Description</th>
                <th style="width:10%;">Qty</th>
                <th style="width:12%;">Unit</th>
                <th style="width:38%;">Purpose/Remarks</th>
            </tr>
        </thead>
        <tbody>
            @php $approvedItems = $consumableRequest->items->where('status', 'approved'); @endphp
            @for($i = 0; $i < ($blankReceipt ?? false ? 10 : max(10, $approvedItems->count())); $i++)
            @php $item = $approvedItems->values()->get($i); @endphp
            <tr>
                <td class="num">{{ $i + 1 }}</td>
                <td>{{ $item->consumable->item_name ?? '' }}</td>
                <td class="num">{{ $item->quantity ?? '' }}</td>
                <td>{{ $item ? strtoupper($item->consumable->unit ?? '') : '' }}</td>
                <td class="purpose-cell">{{ $item->purpose ?? '' }}</td>
            </tr>
            @endfor
            <tr class="total-row">
                <td colspan="2">TOTAL ITEMS:</td>
                <td class="num">{{ $blankReceipt ?? false ? '' : $approvedItems->sum('quantity') }}</td>
                <td colspan="2"></td>
            </tr>
        </tbody>
    </table>

    <div class="sig-section">
        <div class="sig-col">
            <div class="sig-label-top" style="text-align:left;">Requested by:</div>
            <div class="sig-line"></div>
            <div class="sig-name">{{ $blankReceipt ?? false ? '' : strtoupper($consumableRequest->requester->name ?? $consumableRequest->recipient_name) }}</div>
            <div class="sig-role">Signature</div>
        </div>
        <div class="sig-col">
            <div class="sig-label-top" style="text-align:left;">Approved by:</div>
            <div class="sig-line"></div>
            <div class="sig-name">{{ $blankReceipt ?? false ? '' : $consumableRequest->approved_by }}</div>
            <div class="sig-role">AVP for Administration</div>
        </div>
        <div class="sig-col">
            <div class="sig-label-top" style="text-align:left;">Released by:</div>
            <div class="sig-line"></div>
            <div class="sig-name">{{ $blankReceipt ?? false ? '' : $consumableRequest->supply_officer }}</div>
            <div class="sig-role">Supply Officer</div>
        </div>
    </div>

    <div class="received-section">
        <div class="received-label">Received by:</div>
        <div class="received-line"></div>
        <div class="sig-name">{{ $blankReceipt ?? false ? '' : strtoupper($consumableRequest->recipient_name) }}</div>
        <div class="sig-role">Signature</div>
    </div>

    <div class="doc-note">This document serves as official receipt of inventory items transferred.</div>

    <div class="footer">
        <div class="footer-table">
            <div class="footer-cell footer-left">Ref: {{ $blankReceipt ?? false ? '' : $consumableRequest->reference_no }}</div>
            <div class="footer-cell footer-center">{{ now()->format('Y-m-d h:i A') }}</div>
            <div class="footer-cell footer-right">
                @if($footerLogoBase64)<img src="{{ $footerLogoBase64 }}" class="footer-logo">@endif
            </div>
        </div>
    </div>

</body>
</html>