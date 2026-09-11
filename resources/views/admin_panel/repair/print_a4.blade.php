<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Job Sheet #{{ $repair->repair_no }} - {{ $repair->item_name }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm 12mm;
        }
        @media print {
            body {
                background: #fff !important;
                margin: 0 !important;
                padding: 0 !important;
            }
            .no-print {
                display: none !important;
            }
            .slip-block {
                box-shadow: none !important;
                border: 1px solid #94a3b8 !important;
            }
        }
        body {
            font-family: -apple-system, BlinkMacSystemFont, "Segoe UI", Roboto, "Helvetica Neue", Arial, sans-serif;
            background: #f8fafc;
            color: #0f172a;
            margin: 0;
            padding: 20px;
            font-size: 11.5px;
            line-height: 1.35;
        }
        .container {
            max-width: 210mm;
            margin: 0 auto;
        }
        .slip-block {
            background: #fff;
            border: 1.5px solid #cbd5e1;
            border-radius: 6px;
            padding: 14px 18px;
            box-shadow: 0 1px 3px rgba(0,0,0,0.05);
            box-sizing: border-box;
        }
        .cut-line {
            text-align: center;
            border-top: 1.5px dashed #94a3b8;
            margin: 16px 0;
            position: relative;
        }
        .cut-line span {
            background: #f8fafc;
            padding: 0 12px;
            position: relative;
            top: -9px;
            font-size: 10px;
            color: #64748b;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        @media print {
            .cut-line span {
                background: #fff;
            }
        }
        .header-table {
            width: 100%;
            border-bottom: 2px solid #2563eb;
            padding-bottom: 8px;
            margin-bottom: 10px;
        }
        .header-table td {
            vertical-align: middle;
        }
        .company-name {
            font-size: 16px;
            font-weight: 800;
            color: #1e3a8a;
            text-transform: uppercase;
            letter-spacing: -0.2px;
            margin: 0;
        }
        .company-meta {
            font-size: 10px;
            color: #475569;
        }
        .slip-title-badge {
            display: inline-block;
            background: #eff6ff;
            border: 1px solid #bfdbfe;
            color: #1d4ed8;
            font-weight: 700;
            font-size: 11px;
            padding: 3px 8px;
            border-radius: 4px;
            text-transform: uppercase;
        }
        .ticket-no-lg {
            font-size: 16px;
            font-weight: 900;
            font-family: monospace;
            color: #0f172a;
            letter-spacing: 1px;
        }
        .info-grid {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .info-grid th, .info-grid td {
            border: 1px solid #e2e8f0;
            padding: 5px 8px;
            vertical-align: top;
        }
        .info-grid th {
            background: #f8fafc;
            color: #334155;
            font-weight: 600;
            width: 22%;
            font-size: 10.5px;
        }
        .info-grid td {
            font-size: 11px;
        }
        .fault-box {
            border: 1px solid #fecaca;
            background: #fff5f5;
            border-radius: 4px;
            padding: 8px 10px;
            margin-bottom: 10px;
        }
        .fault-box-title {
            font-weight: 700;
            color: #b91c1c;
            font-size: 10px;
            text-transform: uppercase;
            letter-spacing: 0.4px;
            margin-bottom: 2px;
        }
        .fin-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 10px;
        }
        .fin-table th, .fin-table td {
            border: 1px solid #cbd5e1;
            padding: 4px 8px;
            text-align: right;
            font-size: 10.5px;
        }
        .fin-table th {
            background: #f1f5f9;
            color: #334155;
            font-weight: 700;
            text-align: center;
        }
        .terms-text {
            font-size: 9px;
            color: #64748b;
            line-height: 1.25;
            border-top: 1px solid #e2e8f0;
            padding-top: 5px;
        }
        .sig-grid {
            width: 100%;
            margin-top: 25px;
        }
        .sig-grid td {
            width: 50%;
            text-align: center;
            vertical-align: bottom;
        }
        .sig-line {
            width: 75%;
            margin: 0 auto;
            border-top: 1px solid #475569;
            padding-top: 3px;
            font-size: 10px;
            font-weight: 600;
            color: #334155;
        }
        .btn-toolbar {
            max-width: 210mm;
            margin: 0 auto 12px auto;
            display: flex;
            gap: 10px;
            align-items: center;
        }
        .btn-action {
            background: #2563eb;
            color: #fff;
            padding: 7px 16px;
            border-radius: 6px;
            text-decoration: none;
            font-weight: 600;
            font-size: 12px;
            border: none;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 6px;
        }
        .btn-action.btn-secondary {
            background: #64748b;
        }
    </style>
</head>
<body>

    <div class="btn-toolbar no-print">
        <button class="btn-action" onclick="window.print()">
            🖨️ Print Full A4 Job Sheet
        </button>
        <a href="{{ route('repair.print.thermal', $repair->id) }}" target="_blank" class="btn-action" style="background: #059669;">
            🧾 Open 80mm Thermal Receipt
        </a>
        <a href="{{ route('repair.show', $repair->id) }}" class="btn-action btn-secondary">
            ⬅ Back to Job Console
        </a>
    </div>

    <div class="container">

        {{-- =========================================================================
             SECTION 1: CUSTOMER RECEIVING COPY (TOP HALF)
             ========================================================================= --}}
        <div class="slip-block">
            <table class="header-table">
                <tr>
                    <td style="width: 65%;">
                        <h1 class="company-name">{{ \App\Models\Setting::get('company_name', 'LOGIC TECH ENGINEERING') }}</h1>
                        <div class="company-meta">
                            Industrial Heating &amp; Cooling Solutions, Power Electronics, Spare Parts<br>
                            Address: {{ \App\Models\Setting::get('company_address', 'Hyderabad, Pakistan') }} | Phone: {{ \App\Models\Setting::get('company_phone', '0300-5308035') }}
                        </div>
                    </td>
                    <td style="width: 35%; text-align: right;">
                        <span class="slip-title-badge">Customer Intake Receipt</span>
                        <div class="ticket-no-lg mt-1">{{ $repair->repair_no }}</div>
                        <div style="font-size: 10px; color: #64748b;">Date: <strong>{{ $repair->received_date ? $repair->received_date->format('d-M-Y') : date('d-M-Y') }}</strong></div>
                    </td>
                </tr>
            </table>

            <table class="info-grid">
                <tr>
                    <th>Customer Name:</th>
                    <td><strong>{{ $repair->customer_display_name }}</strong></td>
                    <th>Contact / Phone:</th>
                    <td><strong>{{ $repair->customer_display_phone }}</strong></td>
                </tr>
                <tr>
                    <th>Device / Item:</th>
                    <td><strong>{{ $repair->item_name }}</strong></td>
                    <th>Brand &amp; Model:</th>
                    <td>{{ $repair->brand_model ?: 'N/A' }}</td>
                </tr>
                <tr>
                    <th>Serial / Batch #:</th>
                    <td><span style="font-family: monospace; font-weight: bold;">{{ $repair->serial_no ?: 'N/A' }}</span></td>
                    <th>Promised Delivery:</th>
                    <td><strong>{{ $repair->expected_delivery_date ? $repair->expected_delivery_date->format('d-M-Y') : 'Subject to diagnosis' }}</strong></td>
                </tr>
                <tr>
                    <th>Accessories Received:</th>
                    <td>{{ $repair->accessories_received ?: 'None (Body only)' }}</td>
                    <th>Physical Condition:</th>
                    <td>{{ $repair->physical_condition ?: 'Standard used' }}</td>
                </tr>
            </table>

            <div class="fault-box">
                <div class="fault-box-title">Reported Defect / Customer Problem:</div>
                <div style="font-size: 11px;">{{ $repair->problem_description }}</div>
            </div>

            <table class="fin-table">
                <thead>
                    <tr>
                        <th style="text-align: left;">Estimated Charges</th>
                        <th>Advance Received</th>
                        <th>Account Deposited</th>
                        <th style="color: #dc2626;">Estimated Balance Due at Pickup</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td style="text-align: left; font-weight: bold;">Rs. {{ number_format($repair->estimated_cost, 2) }}</td>
                        <td style="font-weight: bold; color: #059669;">Rs. {{ number_format($repair->advance_paid, 2) }}</td>
                        <td>{{ $repair->advanceAccount->title ?? 'None / Cash' }}</td>
                        <td style="font-weight: bold; font-size: 12px; color: #dc2626;">Rs. {{ number_format($repair->due_amount, 2) }}</td>
                    </tr>
                </tbody>
            </table>

            <div class="terms-text">
                <strong>TERMS &amp; CONDITIONS:</strong>
                1. Original receipt must be produced at the time of delivery. 
                2. Goods not collected within 30 days from promised date will incur storage charges of Rs. 50/day and may be disposed of to recover costs.
                3. Repair carries 7 days testing warranty on replaced components only; warranty does not cover burnt coils or physical/water damage.
                4. Inspection/diagnostic fee will be charged if the quotation is declined after diagnosis.
            </div>

            <table class="sig-grid">
                <tr>
                    <td>
                        <div class="sig-line">Customer Signature / Acknowledgment</div>
                    </td>
                    <td>
                        <div class="sig-line">Authorized Technician / Receiver ({{ $repair->receiver->name ?? 'Admin' }})</div>
                    </td>
                </tr>
            </table>
        </div>

        {{-- Perforated Cut Line --}}
        <div class="cut-line">
            <span>✂ CUT HERE — DETACH CUSTOMER COPY ABOVE / ATTACH SHOP COPY BELOW TO UNIT ✂</span>
        </div>

        {{-- =========================================================================
             SECTION 2: WORKSHOP / INTERNAL JOB CARD (BOTTOM HALF)
             ========================================================================= --}}
        <div class="slip-block">
            <table class="header-table" style="border-bottom-color: #d97706;">
                <tr>
                    <td style="width: 65%;">
                        <div style="display: flex; align-items: center; gap: 8px;">
                            <span style="background: #fffbeb; border: 1.5px solid #f59e0b; color: #b45309; font-weight: 800; font-size: 11px; padding: 2px 7px; border-radius: 4px;">WORKSHOP / SHOP COPY</span>
                            <span style="font-weight: bold; font-size: 13px; color: #334155;">{{ \App\Models\Setting::get('company_name', 'LOGIC TECH') }}</span>
                        </div>
                        <div style="font-size: 10px; color: #64748b; margin-top: 2px;">
                            Priority: <strong style="text-transform: uppercase; color: {{ $repair->priority === 'urgent' ? '#dc2626' : ($repair->priority === 'high' ? '#d97706' : '#2563eb') }}">{{ $repair->priority }}</strong> | Received By: <strong>{{ $repair->receiver->name ?? 'Staff' }}</strong>
                        </div>
                    </td>
                    <td style="width: 35%; text-align: right;">
                        <div class="ticket-no-lg" style="color: #b45309;">{{ $repair->repair_no }}</div>
                        <div style="font-size: 10px; color: #64748b;">Target Delivery: <strong>{{ $repair->expected_delivery_date ? $repair->expected_delivery_date->format('d-M-Y') : 'ASAP' }}</strong></div>
                    </td>
                </tr>
            </table>

            <table class="info-grid">
                <tr>
                    <th>Customer:</th>
                    <td>{{ $repair->customer_display_name }} ({{ $repair->customer_display_phone }})</td>
                    <th>Device/Machine:</th>
                    <td><strong>{{ $repair->item_name }}</strong></td>
                </tr>
                <tr>
                    <th>Model &amp; Serial:</th>
                    <td>{{ $repair->brand_model ?: 'Model N/A' }} | SN: <strong>{{ $repair->serial_no ?: 'N/A' }}</strong></td>
                    <th>Condition / Mark:</th>
                    <td>{{ $repair->physical_condition ?: 'Normal' }}</td>
                </tr>
                <tr>
                    <th>Accessories Held:</th>
                    <td>{{ $repair->accessories_received ?: 'Unit only' }}</td>
                    <th>Advance Paid:</th>
                    <td><strong style="color: #059669;">Rs. {{ number_format($repair->advance_paid, 2) }}</strong> ({{ $repair->advanceAccount->title ?? 'None' }})</td>
                </tr>
            </table>

            <div style="display: grid; grid-template-columns: 1fr 1fr; gap: 10px; margin-bottom: 8px;">
                <div style="border: 1px solid #cbd5e1; border-radius: 4px; padding: 6px 8px; background: #f8fafc;">
                    <div style="font-weight: 700; font-size: 10px; color: #1e40af; text-transform: uppercase;">Customer Fault Statement:</div>
                    <div style="font-size: 10.5px; margin-top: 2px;">{{ $repair->problem_description }}</div>
                </div>

                <div style="border: 1px solid #cbd5e1; border-radius: 4px; padding: 6px 8px; background: #fff;">
                    <div style="font-weight: 700; font-size: 10px; color: #475569; text-transform: uppercase;">Technician Diagnostic &amp; Parts Notes:</div>
                    <div style="font-size: 10.5px; margin-top: 2px; min-height: 28px; color: #334155;">
                        {{ $repair->technician_notes ?: '________________________________________________' }}
                    </div>
                </div>
            </div>

            <table class="sig-grid" style="margin-top: 15px;">
                <tr>
                    <td>
                        <div class="sig-line">Bench Technician Assigned</div>
                    </td>
                    <td>
                        <div class="sig-line">Quality Check &amp; Ready for Delivery</div>
                    </td>
                </tr>
            </table>
        </div>

    </div>

</body>
</html>
