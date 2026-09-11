<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Repair Receipt #{{ $repair->repair_no }} - {{ $repair->customer_display_name }}</title>
    <style>
        @page {
            margin: 0;
            size: 80mm auto;
        }
        @media print {
            body {
                width: 78mm;
                margin: 0;
                padding: 4px;
                background: #fff;
            }
            .no-print {
                display: none !important;
            }
        }
        body {
            font-family: 'Courier New', Courier, monospace, 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            font-size: 11px;
            line-height: 1.25;
            color: #000;
            background: #f1f5f9;
            margin: 0;
            padding: 20px 0;
        }
        .thermal-ticket {
            width: 78mm;
            max-width: 100%;
            margin: 0 auto;
            background: #fff;
            padding: 8px 6px;
            box-shadow: 0 2px 8px rgba(0,0,0,0.1);
            box-sizing: border-box;
        }
        .text-center { text-align: center; }
        .text-end { text-align: right; }
        .fw-bold { font-weight: bold; }
        .border-top { border-top: 1px dashed #000; }
        .border-bottom { border-bottom: 1px dashed #000; }
        .py-1 { padding-top: 3px; padding-bottom: 3px; }
        .my-1 { margin-top: 3px; margin-bottom: 3px; }
        .company-title {
            font-size: 14px;
            font-weight: 900;
            letter-spacing: 0.5px;
            text-transform: uppercase;
        }
        .ticket-badge {
            font-size: 16px;
            font-weight: 900;
            border: 2px solid #000;
            display: inline-block;
            padding: 3px 10px;
            margin: 4px 0;
            letter-spacing: 1px;
        }
        .row-kv {
            display: flex;
            justify-content: space-between;
            margin-bottom: 2px;
        }
        .row-kv .lbl {
            font-weight: bold;
            flex-shrink: 0;
            padding-right: 4px;
        }
        .row-kv .val {
            text-align: right;
            word-break: break-word;
        }
        .box-section {
            border: 1px solid #000;
            padding: 4px;
            margin: 4px 0;
            border-radius: 2px;
        }
        .terms {
            font-size: 8.5px;
            line-height: 1.15;
            margin-top: 6px;
        }
        .sig-block {
            display: flex;
            justify-content: space-between;
            margin-top: 25px;
            padding-top: 4px;
            font-size: 9px;
        }
        .sig-line {
            width: 45%;
            border-top: 1px solid #000;
            text-align: center;
            padding-top: 2px;
        }
        .print-toolbar {
            max-width: 78mm;
            margin: 0 auto 10px auto;
            display: flex;
            gap: 8px;
        }
        .btn-print {
            background: #2563eb;
            color: #fff;
            border: none;
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 12px;
            cursor: pointer;
            width: 100%;
        }
        .btn-back {
            background: #64748b;
            color: #fff;
            text-decoration: none;
            padding: 8px 12px;
            border-radius: 6px;
            font-weight: bold;
            font-size: 12px;
            display: inline-block;
            text-align: center;
        }
    </style>
</head>
<body>

    <div class="print-toolbar no-print">
        <button class="btn-print" onclick="window.print()">🖨️ Print Thermal Slip</button>
        <a href="{{ route('repair.show', $repair->id) }}" class="btn-back">Back</a>
    </div>

    <div class="thermal-ticket">
        {{-- Header --}}
        <div class="text-center">
            <div class="company-title">{{ \App\Models\Setting::get('company_name', 'LOGIC TECH ENGINEERING') }}</div>
            <div style="font-size: 9px;">Heating &amp; Cooling Solutions, Power Electronics</div>
            <div style="font-size: 9px;">{{ \App\Models\Setting::get('company_address', 'Hyderabad, Pakistan') }}</div>
            <div style="font-size: 9px;">Phone: {{ \App\Models\Setting::get('company_phone', '0300-5308035') }}</div>

            <div class="py-1">
                <span class="ticket-badge">{{ $repair->repair_no }}</span>
            </div>
            <div style="font-size: 10px; font-weight: bold;">REPAIR INTAKE RECEIPT / JOB CARD</div>
            <div style="font-size: 9px; color: #333;">Date: {{ $repair->received_date ? $repair->received_date->format('d-M-Y') : date('d-M-Y') }} | Status: {{ strtoupper($repair->status) }}</div>
        </div>

        <div class="border-top my-1"></div>

        {{-- Customer Info --}}
        <div class="py-1">
            <div class="row-kv">
                <span class="lbl">Customer:</span>
                <span class="val fw-bold">{{ $repair->customer_display_name }}</span>
            </div>
            <div class="row-kv">
                <span class="lbl">Phone:</span>
                <span class="val fw-bold">{{ $repair->customer_display_phone }}</span>
            </div>
            @if($repair->customer_display_address)
            <div class="row-kv">
                <span class="lbl">Address:</span>
                <span class="val" style="font-size: 9.5px;">{{ $repair->customer_display_address }}</span>
            </div>
            @endif
        </div>

        <div class="border-top my-1"></div>

        {{-- Device Details --}}
        <div class="py-1">
            <div class="row-kv">
                <span class="lbl">Device/Item:</span>
                <span class="val fw-bold">{{ $repair->item_name }}</span>
            </div>
            @if($repair->brand_model)
            <div class="row-kv">
                <span class="lbl">Model/Brand:</span>
                <span class="val">{{ $repair->brand_model }}</span>
            </div>
            @endif
            @if($repair->serial_no)
            <div class="row-kv">
                <span class="lbl">Serial/SN:</span>
                <span class="val fw-bold">{{ $repair->serial_no }}</span>
            </div>
            @endif
            @if($repair->physical_condition)
            <div class="row-kv">
                <span class="lbl">Condition:</span>
                <span class="val" style="font-size: 9px;">{{ $repair->physical_condition }}</span>
            </div>
            @endif
            @if($repair->accessories_received)
            <div class="row-kv">
                <span class="lbl">Accessories:</span>
                <span class="val" style="font-size: 9px;">{{ $repair->accessories_received }}</span>
            </div>
            @endif
            <div class="row-kv">
                <span class="lbl">Promised Date:</span>
                <span class="val fw-bold">{{ $repair->expected_delivery_date ? $repair->expected_delivery_date->format('d-M-Y') : 'To be confirmed' }}</span>
            </div>
        </div>

        {{-- Fault Description Box --}}
        <div class="box-section">
            <div style="font-size: 9px; font-weight: bold; text-decoration: underline;">REPORTED PROBLEM / DEFECT:</div>
            <div style="font-size: 10px; margin-top: 2px;">{{ $repair->problem_description }}</div>
        </div>

        {{-- Financial Settlement --}}
        <div class="border-top my-1"></div>
        <div class="py-1">
            <div class="row-kv">
                <span class="lbl">Estimated Cost:</span>
                <span class="val fw-bold">Rs. {{ number_format($repair->estimated_cost, 2) }}</span>
            </div>
            <div class="row-kv">
                <span class="lbl">Advance Paid:</span>
                <span class="val fw-bold">Rs. {{ number_format($repair->advance_paid, 2) }}</span>
            </div>
            @if($repair->advanceAccount)
            <div class="row-kv" style="font-size: 8.5px;">
                <span class="lbl">Advance Deposited:</span>
                <span class="val">{{ $repair->advanceAccount->title }}</span>
            </div>
            @endif
            @if($repair->status === 'delivered')
                <div class="row-kv">
                    <span class="lbl">Final Bill:</span>
                    <span class="val fw-bold">Rs. {{ number_format($repair->total_charges, 2) }}</span>
                </div>
                <div class="row-kv">
                    <span class="lbl">Final Paid:</span>
                    <span class="val fw-bold">Rs. {{ number_format($repair->final_paid, 2) }}</span>
                </div>
            @endif
            <div class="border-top my-1"></div>
            <div class="row-kv" style="font-size: 12px;">
                <span class="lbl">BALANCE DUE:</span>
                <span class="val fw-bold">Rs. {{ number_format($repair->due_amount, 2) }}</span>
            </div>
        </div>

        <div class="border-top my-1"></div>

        {{-- Terms & Notes --}}
        <div class="terms">
            <strong>NOTICE &amp; TERMS:</strong><br>
            1. Original receipt must be presented at the time of delivery.<br>
            2. 7 days testing warranty on repaired components only.<br>
            3. Workshop is not responsible for unclaimed devices after 30 days.<br>
            4. Diagnostic charges apply if repair estimate is declined.
        </div>

        {{-- Signature Lines --}}
        <div class="sig-block">
            <div class="sig-line">Customer Signature</div>
            <div class="sig-line">Receiver / Tech Sign</div>
        </div>

        <div class="text-center" style="font-size: 8.5px; margin-top: 10px; color: #555;">
            Thank you for choosing {{ \App\Models\Setting::get('company_name', 'LogicTech') }}!<br>
            Received by: {{ $repair->receiver->name ?? 'Admin' }} | {{ date('h:i A') }}
        </div>
    </div>

    <script>
        window.addEventListener('DOMContentLoaded', () => {
            // Uncomment if auto-print popup is desired on direct page open
            // window.print();
        });
    </script>
</body>
</html>
