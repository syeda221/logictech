@php
    /* ── Company Settings ── */
    $coName    = \App\Models\Setting::get('company_name', 'LOGIC TECH ENGINEERING');
    $coAddr    = \App\Models\Setting::get('company_address', 'Hno 2/11B near bight future high school husri distric hyderabad');
    $coEmail   = \App\Models\Setting::get('company_email', 'info@logictech.com.pk');
    $coPhone   = \App\Models\Setting::get('company_phone', '0300-9464887, 0321-9596972');
    $coLogo    = \App\Models\Setting::getLogoUrl();

    $itemsData = json_decode($repair->problem_description, true);
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Job Sheet #{{ $repair->repair_no }} - {{ $coName }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
<style>
  * { margin:0; padding:0; box-sizing:border-box; }
  body {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 11px;
    color: #000;
    background: #cbd5e1;
    padding: 68px 0 25px 0;
    -webkit-print-color-adjust: exact;
    print-color-adjust: exact;
  }
  .no-print-bar {
    position: fixed; top: 0; left: 0; right: 0; height: 56px; z-index: 9999;
    background: rgba(15, 23, 42, 0.92);
    backdrop-filter: blur(12px);
    -webkit-backdrop-filter: blur(12px);
    border-bottom: 1px solid rgba(255, 255, 255, 0.1);
    box-shadow: 0 4px 20px rgba(0,0,0,0.25);
    display: flex; align-items: center; justify-content: space-between;
    padding: 0 24px;
  }
  .no-print-bar .action-group {
    display: flex; align-items: center; gap: 10px;
  }
  .no-print-bar button, .no-print-bar a {
    padding: 7px 16px; font-size: 12px; font-weight: 700; border-radius: 8px; cursor: pointer; border: none; text-decoration: none; display: inline-flex; align-items: center; gap: 6px; font-family: sans-serif; transition: all 0.2s ease; box-shadow: 0 2px 6px rgba(0,0,0,0.2);
  }
  .no-print-bar button:hover, .no-print-bar a:hover {
    transform: translateY(-1.5px);
    box-shadow: 0 4px 12px rgba(0,0,0,0.3);
  }
  .no-print-bar .btn-print   { background: linear-gradient(135deg, #0284c7, #0369a1); color: #fff; }
  .no-print-bar .btn-invoice { background: linear-gradient(135deg, #16a34a, #15803d); color: #fff; }
  .no-print-bar .btn-back    { background: linear-gradient(135deg, #475569, #334155); color: #fff; }

  .page-pad {
    width: 210mm;
    min-height: auto;
    margin: 0 auto;
    background: #ffffff;
    padding: 10mm 12mm 10mm 12mm;
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    position: relative;
  }

  /* Header Layout */
  .pad-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 8px;
    border-bottom: 2px solid #000;
    padding-bottom: 8px;
  }
  .logo-block {
    display: flex;
    flex-direction: column;
  }
  .logo-main {
    font-family: 'Outfit', sans-serif;
    font-size: 30px;
    font-weight: 900;
    letter-spacing: -0.5px;
    color: #000;
    line-height: 0.95;
  }
  .logo-main span.red-o { color: #dc2626; }
  .logo-sub {
    font-family: 'Outfit', sans-serif;
    font-size: 12px;
    font-weight: 700;
    color: #000;
    letter-spacing: 0.5px;
    margin-top: 3px;
  }

  .header-right {
    text-align: right;
  }
  .doc-title-badge {
    font-family: 'Outfit', sans-serif;
    font-size: 15px;
    font-weight: 900;
    background: #0f172a;
    color: #fff;
    padding: 4px 12px;
    border-radius: 4px;
    display: inline-block;
    letter-spacing: 1px;
    margin-bottom: 4px;
  }
  .ticket-no-text {
    font-size: 13px;
    font-weight: 800;
    color: #dc2626;
    font-family: monospace;
  }

  /* Section Title styling */
  .sec-heading {
    font-family: 'Outfit', sans-serif;
    font-size: 11px;
    font-weight: 900;
    background: #1e293b;
    color: #fff;
    padding: 4px 8px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    margin-top: 10px;
    margin-bottom: 6px;
    border-radius: 2px;
  }

  /* Info Grid Layout */
  .info-grid {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 12px;
    margin-bottom: 10px;
  }
  .info-box {
    border: 1.5px solid #000;
    padding: 8px 10px;
    border-radius: 4px;
  }
  .info-box-title {
    font-family: 'Outfit', sans-serif;
    font-size: 11px;
    font-weight: 800;
    border-bottom: 1px solid #000;
    padding-bottom: 3px;
    margin-bottom: 6px;
    text-transform: uppercase;
  }
  .info-row {
    display: flex;
    justify-content: space-between;
    margin-bottom: 4px;
    font-size: 11px;
  }
  .info-label {
    font-weight: 700;
    color: #475569;
  }
  .info-value {
    font-weight: 800;
    color: #000;
    text-align: right;
  }

  /* Items Table */
  .job-table {
    width: 100%;
    border-collapse: collapse;
    border: 2px solid #000;
    margin-bottom: 10px;
  }
  .job-table th {
    background: #0f172a;
    color: #fff;
    font-family: 'Outfit', sans-serif;
    font-size: 10.5px;
    font-weight: 800;
    padding: 6px 6px;
    border: 1.5px solid #000;
    text-transform: uppercase;
  }
  .job-table td {
    border: 1.5px solid #000;
    padding: 6px 8px;
    font-size: 11px;
    font-weight: 600;
    vertical-align: top;
  }

  /* Tech Notes Box */
  .notes-box {
    border: 1.5px solid #000;
    padding: 8px 10px;
    border-radius: 4px;
    min-height: 50px;
    margin-bottom: 8px;
  }

  /* QA Checklist Box */
  .qa-grid {
    display: grid;
    grid-template-columns: repeat(4, 1fr);
    gap: 8px;
    border: 1.5px solid #000;
    padding: 8px 10px;
    border-radius: 4px;
    margin-bottom: 8px;
    font-weight: 700;
    font-size: 10.5px;
  }
  .qa-item {
    display: flex;
    align-items: center;
    gap: 6px;
  }
  .qa-chk {
    width: 13px;
    height: 13px;
    border: 1.5px solid #000;
    display: inline-block;
  }

  /* Terms and Signatures */
  .terms-box {
    font-size: 9px;
    line-height: 1.3;
    border-top: 1px solid #94a3b8;
    padding-top: 6px;
    margin-top: 10px;
    color: #334155;
  }
  .sig-container {
    display: flex;
    justify-content: space-between;
    margin-top: 20px;
    font-weight: 800;
    font-size: 11px;
  }
  .sig-line-block {
    text-align: center;
    width: 220px;
    border-top: 1.5px solid #000;
    padding-top: 4px;
  }

  @media print {
    @page { size: A4 portrait; margin: 4mm; }
    body { background: #fff; padding: 0; }
    .no-print { display: none !important; }
    .page-pad { width: 100%; min-height: auto; box-shadow: none; padding: 4mm; margin: 0; }
  }
</style>
</head>
<body>

<div class="no-print-bar no-print">
    <div class="action-group" style="width: 100%; justify-content: space-between;">
        <div style="color: #fff; font-weight: 800; font-size: 14px; font-family: sans-serif;">
            📋 A4 Repair Technical Job Sheet (#{{ $repair->repair_no }})
        </div>
        <div style="display: flex; gap: 10px;">
            <button onclick="window.print()" class="btn-print">🖨️ Print Job Sheet</button>
            <a href="{{ route('repair.print.a4', $repair->id) }}" class="btn-invoice">🧾 Switch to A4 Invoice</a>
            <a href="{{ route('repair.show', $repair->id) }}" class="btn-back">← Back to Repair Details</a>
        </div>
    </div>
</div>

<div class="page-pad">
    {{-- Header --}}
    <div class="pad-header">
        <div class="logo-block">
            @if(!empty($coLogo))
                <img src="{{ $coLogo }}" alt="LOGICTECH" style="max-height: 55px; width: auto; object-fit: contain;">
            @else
                <div class="logo-main">L<span class="red-o">Ó</span>GICTECH</div>
                <div class="logo-sub">induction Heating Solutions</div>
            @endif
            <div style="font-size: 9.5px; font-weight: 600; margin-top: 4px; color: #334155;">
                {{ $coAddr }} &bull; Cell: {{ $coPhone }}
            </div>
        </div>
        <div class="header-right">
            <div class="doc-title-badge">REPAIR JOB SHEET &amp; INTAKE RECEIPT</div>
            <div class="ticket-no-text">Ticket #: {{ $repair->repair_no }}</div>
            <div style="font-size: 10.5px; font-weight: 700; margin-top: 2px;">
                Date: {{ $repair->received_date ? $repair->received_date->format('d-M-Y') : date('d-M-Y') }}
            </div>
        </div>
    </div>

    {{-- Info Grid --}}
    <div class="info-grid">
        <div class="info-box">
            <div class="info-box-title">👤 Customer Information</div>
            <div class="info-row">
                <span class="info-label">Customer Name:</span>
                <span class="info-value">{{ $repair->customer_display_name }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Mobile / WhatsApp:</span>
                <span class="info-value">{{ $repair->customer_display_phone }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Address / Location:</span>
                <span class="info-value">{{ $repair->customer_display_address ?? 'N/A' }}</span>
            </div>
        </div>

        <div class="info-box">
            <div class="info-box-title">⚙️ Job & Custody Status</div>
            <div class="info-row">
                <span class="info-label">Intake Date:</span>
                <span class="info-value">{{ date('d-M-Y', strtotime($repair->received_date ?? now())) }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Promised Delivery:</span>
                <span class="info-value">{{ $repair->expected_delivery_date ? $repair->expected_delivery_date->format('d-M-Y') : 'To be assigned' }}</span>
            </div>
            <div class="info-row">
                <span class="info-label">Priority Level:</span>
                <span class="info-value" style="text-transform: uppercase; color: {{ $repair->priority === 'urgent' || $repair->priority === 'high' ? '#dc2626' : '#000' }};">
                    {{ $repair->priority }}
                </span>
            </div>
            <div class="info-row">
                <span class="info-label">Current Status:</span>
                <span class="info-value" style="text-transform: uppercase;">{{ $repair->status_label }}</span>
            </div>
        </div>
    </div>

    {{-- Equipment Details & Customer Problem Table --}}
    <div class="sec-heading">📦 Equipment Specifications & Reported Problem</div>
    <table class="job-table">
        <thead>
            <tr>
                <th style="width: 5%; text-align: center;">No.</th>
                <th style="width: 25%;">Item / Equipment Name</th>
                <th style="width: 20%;">Brand / Model</th>
                <th style="width: 15%;">Serial No. (S/N)</th>
                <th style="width: 35%;">Customer Reported Fault / Problem</th>
            </tr>
        </thead>
        <tbody>
            @if(is_array($itemsData) && count($itemsData) > 0)
                @foreach($itemsData as $idx => $it)
                    <tr>
                        <td style="text-align: center; font-weight: 800;">{{ $idx + 1 }}</td>
                        <td style="font-weight: 800;">{{ $it['desc'] ?? $it['item_name'] ?? $repair->item_name }}</td>
                        <td>{{ $it['brand_model'] ?? $repair->brand_model ?? '-' }}</td>
                        <td style="font-family: monospace;">{{ $it['serial_no'] ?? $repair->serial_no ?? '-' }}</td>
                        <td>{{ $it['problem_description'] ?? '-' }}</td>
                    </tr>
                @endforeach
            @else
                <tr>
                    <td style="text-align: center; font-weight: 800;">1</td>
                    <td style="font-weight: 800;">{{ $repair->item_name }}</td>
                    <td>{{ $repair->brand_model ?? '-' }}</td>
                    <td style="font-family: monospace;">{{ $repair->serial_no ?? '-' }}</td>
                    <td>{{ $repair->problem_description ?? 'General Inspection & Service' }}</td>
                </tr>
            @endif
        </tbody>
    </table>

    {{-- Accessories & Condition Grid --}}
    <div class="info-grid">
        <div class="info-box">
            <div class="info-box-title">🔌 Accessories Received</div>
            <div style="font-size: 11px; font-weight: 700; color: #0f172a;">
                {{ $repair->accessories_received ?: 'No extra accessories received with unit.' }}
            </div>
        </div>
        <div class="info-box">
            <div class="info-box-title">🔍 Physical Condition / Pre-inspection</div>
            <div style="font-size: 11px; font-weight: 700; color: #0f172a;">
                {{ $repair->physical_condition ?: 'Standard pre-repair condition.' }}
            </div>
        </div>
    </div>

    {{-- Technician Diagnosis & Notes Box --}}
    <div class="sec-heading">🛠️ Technician Diagnostic & Work Progress Notes</div>
    <div class="notes-box">
        @if(!empty($repair->technician_notes))
            <div style="font-size: 11.5px; font-weight: 700; color: #0f172a; white-space: pre-line;">
                {{ $repair->technician_notes }}
            </div>
        @else
            <div style="color: #64748b; font-style: italic;">
                Technician diagnostic notes, component testing results, and repair work log will be recorded here...
            </div>
        @endif
    </div>

    {{-- Quality Assurance Checklist --}}
    <div class="sec-heading">✅ Quality Assurance & Final Testing Checklist</div>
    <div class="qa-grid">
        <div class="qa-item"><span class="qa-chk"></span> Visual & Physical Inspection</div>
        <div class="qa-item"><span class="qa-chk"></span> Power & Load Testing</div>
        <div class="qa-item"><span class="qa-chk"></span> Thermal Performance OK</div>
        <div class="qa-item"><span class="qa-chk"></span> Final Safety & QA Pass</div>
    </div>

    {{-- Footer Terms & Signatures --}}
    <div class="terms-box">
        <strong>Terms &amp; Conditions:</strong><br>
        1. Equipment must be claimed within 30 days of completion notification. LOGICTECH is not responsible for unclaimed items after 30 days.<br>
        2. Repair warranty applies strictly to replaced parts and reported fault specified in the repair order.<br>
        3. Customer must present this original intake receipt / job sheet at the time of equipment delivery.
    </div>

    <div class="sig-container">
        <div class="sig-line-block">
            Customer Signature / Stamp
        </div>
        <div class="sig-line-block">
            Authorized Technician / Staff
        </div>
    </div>
</div>

</body>
</html>
