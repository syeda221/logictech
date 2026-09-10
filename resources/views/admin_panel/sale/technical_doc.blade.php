<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Technical Document - #{{ $sale->id }} ({{ $sale->invoice_no }}) - Logic Tech Engineering</title>
    <link href="{{ asset('assets/vendors/bootstrap5/css/bootstrap.min.css') }}" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --lte-primary: #1e3a8a;
            --lte-secondary: #0284c7;
            --lte-dark: #0f172a;
            --lte-light: #f8fafc;
            --lte-border: #cbd5e1;
        }

        body {
            background-color: #f1f5f9;
            color: var(--lte-dark);
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 13px;
            margin: 0;
            padding: 20px 0;
        }

        .doc-page {
            max-width: 210mm;
            min-height: 297mm;
            margin: 0 auto;
            background: #ffffff;
            padding: 28px 32px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            border-radius: 6px;
            box-sizing: border-box;
            position: relative;
        }

        .doc-header {
            border-bottom: 2px solid var(--lte-primary);
            padding-bottom: 12px;
            margin-bottom: 18px;
        }

        .company-logo {
            max-width: 170px;
            max-height: 65px;
            object-fit: contain;
        }

        .company-title {
            font-size: 20px;
            font-weight: 800;
            color: var(--lte-primary);
            letter-spacing: 0.5px;
            margin-bottom: 2px;
        }

        .company-meta {
            font-size: 11px;
            color: #475569;
            line-height: 1.4;
        }

        .doc-badge-title {
            background: linear-gradient(135deg, #1e3a8a 0%, #0369a1 100%);
            color: #ffffff;
            font-weight: 800;
            font-size: 15px;
            letter-spacing: 1.5px;
            text-transform: uppercase;
            padding: 8px 16px;
            border-radius: 5px;
            display: inline-block;
            box-shadow: 0 2px 4px rgba(30, 58, 138, 0.2);
        }

        .info-card {
            background: #f8fafc;
            border: 1px solid var(--lte-border);
            border-radius: 6px;
            padding: 10px 14px;
            height: 100%;
        }

        .info-card-header {
            font-size: 11px;
            font-weight: 700;
            text-transform: uppercase;
            color: var(--lte-primary);
            border-bottom: 1px solid #e2e8f0;
            padding-bottom: 4px;
            margin-bottom: 6px;
            letter-spacing: 0.5px;
        }

        .info-row {
            display: flex;
            align-items: baseline;
            font-size: 12px;
            margin-bottom: 4px;
        }

        .info-lbl {
            width: 120px;
            flex-shrink: 0;
            font-weight: 600;
            color: #64748b;
        }

        .info-val {
            font-weight: 700;
            color: #0f172a;
            word-break: break-word;
        }

        .doc-table {
            width: 100%;
            border-collapse: collapse;
            margin-top: 15px;
            font-size: 12px;
        }

        .doc-table th {
            background-color: #1e3a8a;
            color: #ffffff;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            padding: 8px 10px;
            border: 1px solid #1e3a8a;
        }

        .doc-table td {
            border: 1px solid var(--lte-border);
            padding: 8px 10px;
            vertical-align: top;
            background-color: #ffffff;
        }

        .doc-table tbody tr:nth-of-type(even) td {
            background-color: #f8fafc;
        }

        .equipment-title {
            font-size: 13px;
            font-weight: 800;
            color: #0f172a;
            margin-bottom: 3px;
        }

        .param-badge {
            display: inline-block;
            background-color: #eff6ff;
            color: #1e40af;
            border: 1px solid #bfdbfe;
            border-radius: 4px;
            padding: 2px 6px;
            font-size: 11px;
            margin-right: 4px;
            margin-bottom: 3px;
        }

        .spec-content-box {
            font-size: 11.5px;
            color: #1e293b;
            background: #ffffff;
            padding: 6px 8px;
            border-radius: 4px;
            border: 1px dashed #cbd5e1;
            white-space: pre-line;
            line-height: 1.45;
        }

        .delivery-source-badge {
            background-color: #ecfdf5;
            color: #065f46;
            border: 1px solid #a7f3d0;
            font-weight: 700;
            padding: 2px 8px;
            border-radius: 4px;
            display: inline-block;
        }

        .signatures-section {
            margin-top: 36px;
            padding-top: 16px;
            border-top: 1px solid var(--lte-border);
        }

        .signature-box {
            text-align: center;
        }

        .signature-line {
            width: 80%;
            height: 1px;
            background-color: #334155;
            margin: 42px auto 6px auto;
        }

        .signature-title {
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
        }

        .signature-subtitle {
            font-size: 9.5px;
            color: #94a3b8;
        }

        .print-btn-bar {
            position: fixed;
            top: 16px;
            right: 24px;
            z-index: 9999;
            display: flex;
            gap: 10px;
        }

        @media print {
            body {
                background: #ffffff !important;
                padding: 0 !important;
                margin: 0 !important;
            }

            .doc-page {
                box-shadow: none !important;
                border: none !important;
                border-radius: 0 !important;
                max-width: 100% !important;
                width: 100% !important;
                padding: 10mm !important;
                min-height: auto !important;
            }

            .print-btn-bar {
                display: none !important;
            }

            @page {
                margin: 6mm;
                size: portrait;
            }
        }
    </style>
</head>
<body>

    {{-- Floating Action Toolbar --}}
    <div class="print-btn-bar">
        <button onclick="window.print()" class="btn btn-primary btn-sm shadow fw-bold px-3 py-1.5 d-flex align-items-center gap-1.5" style="border-radius: 6px;">
            <i class="fas fa-print"></i> Print Technical Document
        </button>
        <a href="{{ route('sale.index') }}" class="btn btn-secondary btn-sm shadow fw-bold px-3 py-1.5" style="border-radius: 6px;">
            <i class="fas fa-arrow-left me-1"></i> Back to Sales
        </a>
    </div>

    <div class="doc-page">
        {{-- Header Section --}}
        <div class="doc-header">
            <div class="row align-items-center">
                <div class="col-8">
                    <div class="d-flex align-items-center gap-3">
                        @if(!empty($logoUrl))
                            <img src="{{ $logoUrl }}" alt="Company Logo" class="company-logo">
                        @endif
                        <div>
                            @if(empty($logoUrl))
                                <div class="company-title">{{ \App\Models\Setting::get('company_name', 'LOGIC TECH ENGINEERING') }}</div>
                            @endif
                            <div class="company-meta">
                                <div><i class="fas fa-map-marker-alt me-1 text-primary"></i> {{ \App\Models\Setting::get('company_address', '01-KM Sharaqpur Road') }}</div>
                                <div><i class="fas fa-envelope me-1 text-primary"></i> {{ \App\Models\Setting::get('company_email', 'info@logictech.com.pk') }} &nbsp;|&nbsp; <i class="fas fa-phone me-1 text-primary"></i> {{ \App\Models\Setting::get('company_phone', '+92 300 5308035') }}</div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-4 text-end">
                    <div class="doc-badge-title">
                        <i class="fas fa-microchip me-1"></i> Technical Sheet
                    </div>
                    <div class="mt-1 text-muted small fw-bold font-monospace">NEW ORDER PROCESS DOCUMENT</div>
                </div>
            </div>
        </div>

        {{-- Order & Customer Meta Cards --}}
        <div class="row g-2.5 mb-3">
            {{-- Customer Box --}}
            <div class="col-6">
                <div class="info-card">
                    <div class="info-card-header">
                        <i class="fas fa-user-tie me-1"></i> Customer Information
                    </div>
                    <div class="info-row">
                        <span class="info-lbl">Customer Name:</span>
                        <span class="info-val text-primary">{{ $sale->walkin_name ?? ($sale->customer_relation->customer_name ?? 'Walk-in Customer') }}</span>
                    </div>
                    @if(!empty($sale->customer_relation->phone))
                    <div class="info-row">
                        <span class="info-lbl">Contact Phone:</span>
                        <span class="info-val">{{ $sale->customer_relation->phone }}</span>
                    </div>
                    @endif
                    @if(!empty($sale->customer_relation->address))
                    <div class="info-row">
                        <span class="info-lbl">Address / City:</span>
                        <span class="info-val">{{ $sale->customer_relation->address }}</span>
                    </div>
                    @endif
                    <div class="info-row">
                        <span class="info-lbl">Order Status:</span>
                        <span class="badge bg-success font-monospace text-uppercase" style="font-size: 10px;">{{ $sale->order_status ?: 'Delivered' }}</span>
                    </div>
                </div>
            </div>

            {{-- Document / Dispatch Logistics Box --}}
            <div class="col-6">
                <div class="info-card">
                    <div class="info-card-header">
                        <i class="fas fa-file-invoice me-1"></i> Order &amp; Dispatch Logistics
                    </div>
                    <div class="info-row">
                        <span class="info-lbl">Order / Doc No:</span>
                        <span class="info-val font-monospace text-dark">#{{ $sale->id }} ({{ $sale->invoice_no }})</span>
                    </div>
                    <div class="info-row">
                        <span class="info-lbl">Order Date:</span>
                        <span class="info-val">{{ $sale->created_at ? $sale->created_at->format('d-M-Y') : '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-lbl">Delivery Date:</span>
                        <span class="info-val text-success">{{ $sale->delivery_date ? \Carbon\Carbon::parse($sale->delivery_date)->format('d-M-Y') : '-' }}</span>
                    </div>
                    <div class="info-row">
                        <span class="info-lbl">Delivery Source:</span>
                        <span class="info-val">
                            <span class="delivery-source-badge">{{ $sale->delivery_source ?: 'Self Collection' }}</span>
                        </span>
                    </div>
                    @if(!empty($sale->delivery_remarks))
                    <div class="info-row">
                        <span class="info-lbl">Delivery Remarks:</span>
                        <span class="info-val text-muted fst-italic">{{ $sale->delivery_remarks }}</span>
                    </div>
                    @endif
                </div>
            </div>
        </div>

        {{-- Equipment & Technical Specifications Table --}}
        <div class="mb-2">
            <span class="fw-bold text-dark text-uppercase small" style="letter-spacing: 0.5px;">
                <i class="fas fa-cogs text-primary me-1"></i> Equipment &amp; Technical Specifications Checklist
            </span>
        </div>

        <table class="doc-table">
            <thead>
                <tr>
                    <th style="width: 5%; text-align: center;">S#</th>
                    <th style="width: 32%;">Equipment / Technical Title</th>
                    <th style="width: 18%;">Model &amp; Serial Number</th>
                    <th style="width: 8%; text-align: center;">QTY</th>
                    <th style="width: 37%;">Technical Specifications, QC &amp; Remarks</th>
                </tr>
            </thead>
            <tbody>
                @foreach($sale->items as $idx => $item)
                    @php
                        $techTitle = $item->technical_name ?: ($item->product_name ?: ($item->product->item_name ?? 'Equipment Item'));
                        $model = $item->model ?: '-';
                        $serial = $item->serial_no ?: '-';
                        $qty = (float)($item->total_pieces ?: ($item->qty ?: 1));
                    @endphp
                    <tr>
                        <td style="text-align: center; font-weight: 700; color: #64748b;">{{ $idx + 1 }}</td>
                        <td>
                            <div class="equipment-title">{{ $techTitle }}</div>
                            @if(!empty($item->product->brand->name))
                                <span class="param-badge"><i class="fas fa-tag me-1"></i>{{ $item->product->brand->name }}</span>
                            @endif
                            @if(!empty($item->product->item_code))
                                <span class="param-badge"><i class="fas fa-barcode me-1"></i>SKU: {{ $item->product->item_code }}</span>
                            @endif
                        </td>
                        <td>
                            <div class="mb-1">
                                <span class="text-muted small fw-semibold">Model:</span>
                                <strong class="text-dark d-block">{{ $model }}</strong>
                            </div>
                            <div>
                                <span class="text-muted small fw-semibold">Serial No:</span>
                                <strong class="text-primary font-monospace d-block">{{ $serial }}</strong>
                            </div>
                        </td>
                        <td style="text-align: center; font-weight: 800; font-size: 13px; color: #1e3a8a;">
                            {{ $qty == (int)$qty ? (int)$qty : $qty }}
                        </td>
                        <td>
                            @if(!empty($item->technical_specs))
                                <div class="mb-1.5">
                                    <span class="text-primary fw-bold" style="font-size: 10.5px; text-transform: uppercase;">
                                        <i class="fas fa-sliders-h me-1"></i> Technical Parameters:
                                    </span>
                                    <div class="spec-content-box mt-0.5">{{ $item->technical_specs }}</div>
                                </div>
                            @endif

                            @if(!empty($item->technical_remarks))
                                <div class="mb-1.5">
                                    <span class="text-secondary fw-bold" style="font-size: 10.5px; text-transform: uppercase;">
                                        <i class="fas fa-clipboard-check me-1"></i> Engineering &amp; QC Remarks:
                                    </span>
                                    <div class="spec-content-box mt-0.5" style="background-color: #f8fafc;">{{ $item->technical_remarks }}</div>
                                </div>
                            @endif

                            @if(empty($item->technical_specs) && empty($item->technical_remarks))
                                @if(!empty($item->color))
                                    <div>
                                        <span class="text-muted small fw-semibold">Specs:</span>
                                        <span class="text-dark">{{ $item->color }}</span>
                                    </div>
                                @else
                                    <span class="text-muted fst-italic small">Standard factory specification parameters apply.</span>
                                @endif
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        {{-- Process Checklist & Important Guidelines --}}
        <div class="mt-3 p-2.5 rounded-2 border bg-light" style="font-size: 11px; color: #475569;">
            <div class="fw-bold text-dark mb-1"><i class="fas fa-info-circle text-primary me-1"></i> Inspection &amp; Company Verification Protocol:</div>
            <div class="row g-2">
                <div class="col-6">
                    <div>✔ Power circuit &amp; IGBT module insulation resistance verified.</div>
                    <div>✔ Water cooling circulation / chiller flow rate checked.</div>
                </div>
                <div class="col-6">
                    <div>✔ Resonant frequency &amp; capacitor tuning tested on live load.</div>
                    <div>✔ Serial number matched with internal equipment production registry.</div>
                </div>
            </div>
        </div>

        {{-- Signatures Section --}}
        <div class="signatures-section">
            <div class="row">
                <div class="col-4">
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <div class="signature-title">Prepared By (Engineer)</div>
                        <div class="signature-subtitle">Technical / Assembly Department</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <div class="signature-title">Quality Inspector (QC)</div>
                        <div class="signature-subtitle">Testing &amp; Load Verification</div>
                    </div>
                </div>
                <div class="col-4">
                    <div class="signature-box">
                        <div class="signature-line"></div>
                        <div class="signature-title">Customer / Received By</div>
                        <div class="signature-subtitle">Signature &amp; Dispatch Stamp</div>
                    </div>
                </div>
            </div>
        </div>

        {{-- Footer Note --}}
        <div class="mt-4 pt-2 text-center text-muted border-top" style="font-size: 10px;">
            <span>This is an official Technical Document &amp; Equipment Process Sheet generated by {{ \App\Models\Setting::get('company_name', 'LOGIC TECH ENGINEERING') }} ERP System.</span>
        </div>
    </div>

</body>
</html>
