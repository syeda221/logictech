<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Technical Document - #{{ $sale->id }} ({{ $sale->invoice_no }})</title>
    <style>
        @page {
            margin: 10mm 10mm 12mm 10mm;
            size: A4 portrait;
        }
        body {
            font-family: 'DejaVu Sans', Arial, sans-serif;
            font-size: 11px;
            color: #0f172a;
            margin: 0;
            padding: 0;
            background: #ffffff;
        }
        .header-table {
            width: 100%;
            border-collapse: collapse;
            border-bottom: 2px solid #1e3a8a;
            margin-bottom: 14px;
        }
        .header-table td {
            vertical-align: middle;
            padding-bottom: 10px;
        }
        .company-logo {
            max-width: 155px;
            max-height: 55px;
        }
        .company-title {
            font-size: 17px;
            font-weight: bold;
            color: #1e3a8a;
            margin-bottom: 2px;
        }
        .company-meta {
            font-size: 9.5px;
            color: #475569;
            line-height: 1.45;
        }
        .doc-badge-title {
            background-color: #1e3a8a;
            color: #ffffff;
            font-weight: bold;
            font-size: 13px;
            letter-spacing: 1.2px;
            text-transform: uppercase;
            padding: 7px 14px;
            border-radius: 4px;
            display: inline-block;
        }
        .doc-subtitle {
            margin-top: 5px;
            color: #64748b;
            font-size: 9px;
            font-weight: bold;
            letter-spacing: 0.4px;
        }
        .cards-table {
            width: 100%;
            border-collapse: separate;
            border-spacing: 8px 0;
            margin: 0 -8px 14px -8px;
        }
        .cards-table > tbody > tr > td {
            width: 50%;
            vertical-align: top;
            background-color: #f8fafc;
            border: 1px solid #cbd5e1;
            border-radius: 5px;
            padding: 9px 11px;
        }
        .info-card-header {
            font-size: 9.5px;
            font-weight: bold;
            text-transform: uppercase;
            color: #1e3a8a;
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
            margin-bottom: 6px;
            letter-spacing: 0.4px;
        }
        .kv-table {
            width: 100%;
            border-collapse: collapse;
        }
        .kv-table td {
            padding: 2.5px 0;
            font-size: 10.5px;
            vertical-align: middle;
        }
        .kv-lbl {
            width: 112px;
            font-weight: bold;
            color: #64748b;
        }
        .kv-val {
            font-weight: bold;
            color: #0f172a;
        }
        .kv-val-primary {
            color: #1d4ed8;
        }
        .kv-val-success {
            color: #059669;
        }
        .kv-divider td {
            border-top: 1px solid #e2e8f0;
            padding-top: 6px;
        }
        .status-badge {
            display: inline-block;
            padding: 2px 7px;
            border-radius: 3px;
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            letter-spacing: 0.4px;
        }
        .delivery-source-badge {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            font-weight: bold;
            padding: 2px 7px;
            border-radius: 3px;
            font-size: 10px;
            display: inline-block;
        }
        .write-line {
            border-bottom: 1px solid #94a3b8;
            height: 13px;
            width: 100%;
        }
        .section-heading {
            font-size: 10px;
            font-weight: bold;
            color: #0f172a;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            margin-bottom: 6px;
        }
        .doc-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 24px;
        }
        .doc-table th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-weight: bold;
            font-size: 9.5px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            padding: 7px 8px;
            border: 1px solid #1e3a8a;
            text-align: left;
        }
        .doc-table td {
            border: 1px solid #cbd5e1;
            padding: 8px 9px;
            vertical-align: top;
            font-size: 10.5px;
        }
        .equipment-title {
            font-size: 11.5px;
            font-weight: bold;
            color: #0f172a;
            margin-bottom: 4px;
        }
        .param-badge {
            display: inline-block;
            background-color: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
            border-radius: 3px;
            padding: 1.5px 5px;
            font-size: 9px;
            margin-right: 3px;
            margin-bottom: 3px;
        }
        .spec-label {
            font-size: 9px;
            font-weight: bold;
            text-transform: uppercase;
            margin-bottom: 3px;
            display: block;
        }
        .spec-box {
            font-size: 10px;
            color: #1e293b;
            background-color: #ffffff;
            padding: 5px 7px;
            border: 1px dashed #cbd5e1;
            border-radius: 3px;
            margin-bottom: 7px;
            line-height: 1.45;
        }
        .spec-write-line {
            border-bottom: 1px dashed #cbd5e1;
            height: 15px;
            width: 100%;
        }
        .signatures-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 32px;
            border-top: 1px solid #cbd5e1;
        }
        .signatures-table td {
            width: 33.33%;
            text-align: center;
            padding-top: 42px;
            vertical-align: bottom;
        }
        .sig-line {
            width: 78%;
            margin: 0 auto 5px auto;
            border-top: 1px solid #334155;
        }
        .sig-title {
            font-size: 9.5px;
            font-weight: bold;
            color: #475569;
            text-transform: uppercase;
        }
        .sig-sub {
            font-size: 8.5px;
            color: #94a3b8;
        }
        .footer-note {
            margin-top: 18px;
            padding-top: 8px;
            border-top: 1px solid #e2e8f0;
            text-align: center;
            color: #64748b;
            font-size: 9px;
        }
    </style>
</head>
<body>
    @php
        $ordStatus = strtolower($sale->order_status ?: 'pending');
        $isDelivered = ($ordStatus === 'delivered');

        $statusBadgeStyle = match ($ordStatus) {
            'ready' => 'background-color: #2563eb; color: #ffffff;',
            'delivered' => 'background-color: #10b981; color: #ffffff;',
            'cancelled' => 'background-color: #f59e0b; color: #0f172a;',
            default => 'background-color: #ef4444; color: #ffffff;',
        };

        $custName = optional($sale->customer_relation)->customer_name ?: ($sale->walkin_name ?: 'Walk-in Customer');
        $custPhone = optional($sale->customer_relation)->mobile ?: (optional($sale->customer_relation)->phone ?? '');
        $custAddr = optional($sale->customer_relation)->address ?? '';

        $firstItem = $sale->items->first();
        $allModels = $sale->items->pluck('model')->map(fn($v) => trim((string)$v))->filter()->unique()->implode(', ');
        $allSerials = $sale->items->pluck('serial_no')->map(fn($v) => trim((string)$v))->filter()->unique()->implode(', ');
        $totalQty = $sale->items->sum(function($i){ return (float)($i->total_pieces ?: ($i->qty ?: 1)); });

        $companyName = \App\Models\Setting::get('company_name', 'LOGIC TECH ENGINEERING');
        $companyAddress = \App\Models\Setting::get('company_address', '01-KM Sharaqpur Road');
        $companyEmail = \App\Models\Setting::get('company_email', 'info@logictech.com.pk');
        $companyPhone = trim(str_replace(['92 300 5308035,', '92 300 5308035', '0300-5308035', '+92 300 5308035'], '', \App\Models\Setting::get('company_phone', '+92 336 1500082')), " \t\n\r\0\x0B,") ?: '+92 336 1500082';
    @endphp

    <table class="header-table">
        <tr>
            <td style="width: 65%;">
                @if(!empty($logoUrl))
                    <div style="margin-bottom: 4px;">
                        <img src="{{ $logoUrl }}" alt="Company Logo" class="company-logo">
                    </div>
                @else
                    <div class="company-title">{{ $companyName }}</div>
                @endif
                <div class="company-meta">
                    <div>{{ $companyAddress }}</div>
                    <div>{{ $companyEmail }} &nbsp;|&nbsp; {{ $companyPhone }}</div>
                </div>
            </td>
            <td style="width: 35%; text-align: right;">
                <div class="doc-badge-title">TECHNICAL SHEET</div>
                <div class="doc-subtitle">NEW ORDER PROCESS DOCUMENT</div>
            </td>
        </tr>
    </table>

    <table class="cards-table">
        <tr>
            <td>
                <div class="info-card-header">Customer Information</div>
                <table class="kv-table">
                    <tr>
                        <td class="kv-lbl">Customer Name:</td>
                        <td class="kv-val kv-val-primary">{{ $custName }}</td>
                    </tr>
                    @if(!empty($custPhone))
                    <tr>
                        <td class="kv-lbl">Contact Phone:</td>
                        <td class="kv-val">{{ $custPhone }}</td>
                    </tr>
                    @endif
                    @if(!empty($custAddr))
                    <tr>
                        <td class="kv-lbl">Address / City:</td>
                        <td class="kv-val">{{ $custAddr }}</td>
                    </tr>
                    @endif
                    <tr>
                        <td class="kv-lbl">Order Status:</td>
                        <td><span class="status-badge" style="{{ $statusBadgeStyle }}">{{ strtoupper($ordStatus) }}</span></td>
                    </tr>
                    <tr class="kv-divider">
                        <td class="kv-lbl">Model:</td>
                        <td class="kv-val">
                            @if(!empty($allModels) || !empty($firstItem->model))
                                {{ $allModels ?: $firstItem->model }}
                            @elseif(!$isDelivered)
                                <div class="write-line"></div>
                            @else
                                -
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="kv-lbl">Serial No:</td>
                        <td class="kv-val kv-val-primary">
                            @if($isDelivered)
                                {{ $allSerials ?: ($firstItem->serial_no ?? '-') }}
                            @else
                                <div class="write-line"></div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="kv-lbl">QTY:</td>
                        <td class="kv-val kv-val-primary">{{ $totalQty == (int)$totalQty ? (int)$totalQty : $totalQty }}</td>
                    </tr>
                </table>
            </td>
            <td>
                <div class="info-card-header">Order &amp; Dispatch Logistics</div>
                <table class="kv-table">
                    <tr>
                        <td class="kv-lbl">Order / Doc No:</td>
                        <td class="kv-val">#{{ $sale->id }} ({{ $sale->invoice_no }})</td>
                    </tr>
                    <tr>
                        <td class="kv-lbl">Order Date:</td>
                        <td class="kv-val">{{ $sale->created_at ? $sale->created_at->format('d-M-Y') : '-' }}</td>
                    </tr>
                    <tr>
                        <td class="kv-lbl">Delivery Date:</td>
                        <td class="kv-val kv-val-success">
                            @if($isDelivered)
                                {{ $sale->delivery_date ? \Carbon\Carbon::parse($sale->delivery_date)->format('d-M-Y') : '-' }}
                            @else
                                <div class="write-line"></div>
                            @endif
                        </td>
                    </tr>
                    <tr>
                        <td class="kv-lbl">Delivery Source:</td>
                        <td>
                            @if($isDelivered)
                                <span class="delivery-source-badge">{{ $sale->delivery_source ?: 'Self Collection' }}</span>
                            @else
                                <div class="write-line"></div>
                            @endif
                        </td>
                    </tr>
                    @if($isDelivered)
                        @if(!empty($sale->delivery_remarks))
                        <tr>
                            <td class="kv-lbl">Delivery Remarks:</td>
                            <td class="kv-val" style="color: #475569; font-style: italic;">{{ $sale->delivery_remarks }}</td>
                        </tr>
                        @endif
                    @else
                        <tr>
                            <td class="kv-lbl">Delivery Remarks:</td>
                            <td><div class="write-line"></div></td>
                        </tr>
                    @endif
                </table>
            </td>
        </tr>
    </table>

    <div class="section-heading">Equipment &amp; Technical Specifications Checklist</div>

    <table class="doc-table">
        <thead>
            <tr>
                <th style="width: 6%; text-align: center;">S#</th>
                <th style="width: 44%;">Equipment / Technical Title</th>
                <th style="width: 50%;">Technical Specifications, QC &amp; Remarks</th>
            </tr>
        </thead>
        <tbody>
            @foreach($sale->items as $idx => $item)
                @php
                    $techTitle = $item->technical_name ?: ($item->product_name ?: ($item->product->item_name ?? 'Equipment Item'));
                    $itemQty = (float)($item->total_pieces ?: ($item->qty ?: 1));
                    $decodedColorSpecs = '';
                    if (!empty($item->color)) {
                        $rawColor = trim((string) $item->color);
                        $b64 = base64_decode($rawColor, true);
                        $json = ($b64 !== false) ? json_decode($b64, true) : null;
                        if (!is_array($json)) {
                            $json = json_decode($rawColor, true);
                        }
                        if (is_array($json)) {
                            $decodedColorSpecs = trim((string) ($json['color'] ?? ($json['spec'] ?? ($json['specs'] ?? ''))));
                            if ($decodedColorSpecs === '-') {
                                $decodedColorSpecs = '';
                            }
                        } else {
                            $decodedColorSpecs = $rawColor;
                        }
                    }
                @endphp
                <tr>
                    <td style="text-align: center; font-weight: bold; color: #64748b;">{{ $idx + 1 }}</td>
                    <td>
                        <div class="equipment-title">{{ $techTitle }}</div>
                        @if(!empty($item->product->brand->name))
                            <span class="param-badge">{{ $item->product->brand->name }}</span>
                        @endif
                        @if(!empty($item->product->item_code))
                            <span class="param-badge">SKU: {{ $item->product->item_code }}</span>
                        @endif
                        @if($sale->items->count() > 1)
                            <span class="param-badge">Qty: {{ $itemQty == (int)$itemQty ? (int)$itemQty : $itemQty }}</span>
                        @endif
                    </td>
                    <td>
                        @if(!empty($item->technical_specs))
                            <span class="spec-label" style="color: #1d4ed8;">Technical Parameters:</span>
                            <div class="spec-box">{!! nl2br(e($item->technical_specs)) !!}</div>
                        @elseif(!$isDelivered)
                            <span class="spec-label" style="color: #1d4ed8;">Technical Parameters:</span>
                            <div class="spec-box">
                                <div class="spec-write-line"></div>
                                <div class="spec-write-line"></div>
                                <div class="spec-write-line"></div>
                            </div>
                        @endif

                        @if(!empty($item->technical_remarks))
                            <span class="spec-label" style="color: #475569;">Engineering &amp; QC Remarks:</span>
                            <div class="spec-box" style="background-color: #f8fafc;">{!! nl2br(e($item->technical_remarks)) !!}</div>
                        @elseif(!$isDelivered)
                            <span class="spec-label" style="color: #475569;">Engineering &amp; QC Remarks:</span>
                            <div class="spec-box" style="background-color: #f8fafc;">
                                <div class="spec-write-line"></div>
                                <div class="spec-write-line"></div>
                            </div>
                        @endif

                        @if($isDelivered && empty($item->technical_specs) && empty($item->technical_remarks))
                            @if(!empty($decodedColorSpecs))
                                <div><strong>Specs:</strong> {{ $decodedColorSpecs }}</div>
                            @else
                                <span style="color: #64748b; font-style: italic; font-size: 10px;">Standard factory specification parameters apply.</span>
                            @endif
                        @endif
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    <table class="signatures-table">
        <tr>
            <td>
                <div class="sig-line"></div>
                <div class="sig-title">Prepared By (Engineer)</div>
                <div class="sig-sub">Technical / Assembly Department</div>
            </td>
            <td>
                <div class="sig-line"></div>
                <div class="sig-title">Quality Inspector (QC)</div>
                <div class="sig-sub">Testing &amp; Load Verification</div>
            </td>
            <td>
                <div class="sig-line"></div>
                <div class="sig-title">Customer / Received By</div>
                <div class="sig-sub">Signature &amp; Dispatch Stamp</div>
            </td>
        </tr>
    </table>

    <div class="footer-note">
        This is an official Technical Document &amp; Equipment Process Sheet generated by {{ $companyName }} ERP System.
    </div>
</body>
</html>
