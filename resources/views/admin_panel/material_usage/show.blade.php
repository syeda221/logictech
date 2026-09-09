@php
    $coName    = \App\Models\Setting::get('company_name',    'LOGIC TECH ENGINEERING');
    $coAddr    = \App\Models\Setting::get('company_address', '01-KM Sharaqpur Road');
    $coEmail   = \App\Models\Setting::get('web_contact_email') ?: \App\Models\Setting::get('company_email','info@logictech.com.pk');
    $coPhone   = \App\Models\Setting::get('company_phone')   ?: \App\Models\Setting::get('web_contact_phone','92 300 5308035');
    $coLogo    = \App\Models\Setting::getLogoUrl();
    $coNtn     = \App\Models\Setting::get('company_ntn',  '5561761-4');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Material Issue Voucher - {{ $usage->usage_no }}</title>
    <link rel="stylesheet" href="{{ asset('assets/css/bootstrap.min.css') }}">
    <link rel="stylesheet" href="{{ asset('assets/vendors/font-awesome/css/all.min.css') }}">
    <style>
        body {
            background-color: #f1f5f9;
            font-family: 'Segoe UI', Arial, sans-serif;
            color: #1e293b;
            font-size: 13px;
        }
        .slip-container {
            max-width: 820px;
            margin: 25px auto;
            background: #ffffff;
            padding: 35px 40px;
            border-radius: 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.06);
        }
        .header-title {
            font-size: 20px;
            font-weight: 800;
            color: #0f172a;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .badge-doc {
            background: #0f172a;
            color: #fff;
            padding: 4px 12px;
            border-radius: 4px;
            font-size: 11px;
            font-weight: 700;
            letter-spacing: 1px;
            text-transform: uppercase;
            display: inline-block;
        }
        .meta-box {
            background: #f8fafc;
            border: 1px solid #e2e8f0;
            border-radius: 6px;
            padding: 12px 16px;
        }
        .meta-label {
            font-size: 11px;
            font-weight: 700;
            color: #64748b;
            text-transform: uppercase;
        }
        .meta-val {
            font-size: 13px;
            font-weight: 700;
            color: #0f172a;
        }
        .table-slip thead th {
            background-color: #f1f5f9;
            color: #334155;
            font-weight: 700;
            font-size: 11px;
            text-transform: uppercase;
            border-top: 1px solid #cbd5e1;
            border-bottom: 2px solid #94a3b8;
            padding: 8px 10px;
        }
        .table-slip tbody td {
            padding: 8px 10px;
            border-bottom: 1px solid #e2e8f0;
            vertical-align: middle;
        }
        .sig-line {
            border-top: 1.5px dashed #94a3b8;
            margin-top: 60px;
            padding-top: 6px;
            text-align: center;
            font-size: 11px;
            font-weight: 700;
            color: #475569;
            text-transform: uppercase;
        }
        @media print {
            body { background: #fff; }
            .slip-container {
                box-shadow: none;
                padding: 0;
                margin: 0;
                max-width: 100%;
            }
            .no-print { display: none !important; }
        }
    </style>
</head>
<body>

<div class="container no-print mt-3 mb-2 text-center">
    <button onclick="window.print()" class="btn btn-primary btn-sm px-4 mr-2">
        <i class="fas fa-print mr-1"></i> Print Voucher
    </button>
    <a href="{{ route('material_usage.index') }}" class="btn btn-outline-secondary btn-sm px-3">
        <i class="fas fa-arrow-left mr-1"></i> Back to List
    </a>
</div>

<div class="slip-container">
    <!-- Header -->
    <div class="d-flex justify-content-between align-items-start pb-3 border-bottom">
        <div>
            @if($coLogo)
                <img src="{{ $coLogo }}" alt="Logo" style="max-height: 55px; max-width: 200px; object-fit: contain;" class="mb-2">
            @else
                <h3 class="font-weight-bold mb-1" style="color: #2563eb;">{{ $coName }}</h3>
            @endif
            <div class="text-muted small" style="line-height: 1.4;">
                {{ $coAddr }}<br>
                @if($coPhone) Phone: {{ $coPhone }} | @endif
                @if($coEmail) Email: {{ $coEmail }} @endif
                @if($coNtn) <br>NTN: {{ $coNtn }} @endif
            </div>
        </div>
        <div class="text-right">
            <span class="badge-doc mb-2">Material Issue Voucher</span>
            <div class="header-title text-primary">{{ $usage->usage_no }}</div>
            <div class="text-muted small">Date: <strong>{{ \Carbon\Carbon::parse($usage->date)->format('d-M-Y') }}</strong></div>
            <div class="text-muted small">Time: <strong>{{ $usage->created_at->format('h:i A') }}</strong></div>
        </div>
    </div>

    <!-- Metadata Grid -->
    <div class="row g-3 my-3">
        <div class="col-6">
            <div class="meta-box">
                <div class="meta-label">Issued By (User)</div>
                <div class="meta-val">{{ $usage->user->name ?? 'System' }}</div>
            </div>
        </div>
        <div class="col-6">
            <div class="meta-box">
                <div class="meta-label">Purpose / Type</div>
                <div class="meta-val">{{ $usage->purpose ?: 'Production Consumption' }}</div>
            </div>
        </div>
        @if($usage->remarks)
            <div class="col-12 mt-2">
                <div class="meta-box py-2">
                    <span class="meta-label mr-2">Remarks:</span>
                    <span class="text-dark font-weight-600">{{ $usage->remarks }}</span>
                </div>
            </div>
        @endif
    </div>

    <!-- Items Table -->
    <table class="table table-slip mb-3">
        <thead>
            <tr>
                <th style="width: 35px;">#</th>
                <th>Raw Material Item</th>
                <th>Item Code</th>
                <th>Category</th>
                <th class="text-right" style="width: 110px;">Qty Issued</th>
                <th class="text-right" style="width: 95px;">Unit Cost</th>
                <th class="text-right" style="width: 115px;">Total Cost</th>
            </tr>
        </thead>
        <tbody>
            @foreach($usage->items as $index => $item)
                <tr>
                    <td class="text-muted">{{ $index + 1 }}</td>
                    <td>
                        <strong>{{ $item->product->item_name ?? 'N/A' }}</strong>
                        @if($item->notes)
                            <div class="text-muted small italic">{{ $item->notes }}</div>
                        @endif
                    </td>
                    <td class="text-muted small">{{ $item->product->item_code ?? '-' }}</td>
                    <td class="text-muted small">{{ $item->product->category_relation->category_name ?? '-' }}</td>
                    <td class="text-right font-weight-bold text-danger">
                        {{ number_format($item->qty_used, 2) }} {{ $item->unit_name }}
                    </td>
                    <td class="text-right text-muted">
                        Rs. {{ number_format($item->unit_cost, 2) }}
                    </td>
                    <td class="text-right font-weight-bold text-dark">
                        Rs. {{ number_format($item->total_cost, 2) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr style="border-top: 2px solid #cbd5e1; background: #f8fafc;">
                <td colspan="4" class="text-right font-weight-bold py-2">Grand Total:</td>
                <td class="text-right font-weight-bold text-danger py-2" style="font-size: 14px;">
                    {{ number_format($usage->total_qty, 2) }}
                </td>
                <td class="text-right py-2"></td>
                <td class="text-right font-weight-bold text-dark py-2" style="font-size: 14px;">
                    Rs. {{ number_format($usage->total_cost, 2) }}
                </td>
            </tr>
        </tfoot>
    </table>

    <!-- Signatures -->
    <div class="row mt-5 pt-3">
        <div class="col-4">
            <div class="sig-line">Prepared By</div>
        </div>
        <div class="col-4">
            <div class="sig-line">Production In-Charge</div>
        </div>
        <div class="col-4">
            <div class="sig-line">Authorized Sign</div>
        </div>
    </div>
</div>

</body>
</html>