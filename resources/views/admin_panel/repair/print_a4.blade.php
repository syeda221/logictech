@php
    /* ── Company Settings ── */
    $coName    = \App\Models\Setting::get('company_name', 'LOGIC TECH ENGINEERING');
    $coAddr    = \App\Models\Setting::get('company_address', 'Hno 2/11B near bight future high school husri distric hyderabad');
    $coEmail   = \App\Models\Setting::get('company_email', 'info@logictech.com.pk');
    $coPhone   = \App\Models\Setting::get('company_phone', '0300-9464887, 0321-9596972');
    $coLogo    = \App\Models\Setting::getLogoUrl();

    /* ── Financial Calculations ── */
    $serviceCharges = (float)($repair->service_charges > 0 ? $repair->service_charges : $repair->estimated_cost);
    $partsCharges   = (float)($repair->parts_charges ?? 0);
    $totalBill      = (float)($repair->total_charges > 0 ? $repair->total_charges : ($serviceCharges + $partsCharges));
    $advancePaid    = (float)($repair->advance_paid ?? 0);
    $finalPaid      = (float)($repair->final_paid ?? 0);
    $dueAmount      = max(0, $totalBill - $advancePaid);

    if (!function_exists('numberToWordsPhp')) {
        function numberToWordsPhp($num) {
            $num = (float)$num;
            if ($num <= 0) return '** Zero Rupees Only **';
            $ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
            $tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

            $inWords = function($n) use (&$inWords, $ones, $tens) {
                if ($n < 20) return $ones[$n];
                if ($n < 100) return $tens[(int)($n / 10)] . ($n % 10 ? ' ' . $ones[$n % 10] : '');
                if ($n < 1000) return $ones[(int)($n / 100)] . ' Hundred' . ($n % 100 ? ' and ' . $inWords($n % 100) : '');
                if ($n < 100000) return $inWords((int)($n / 1000)) . ' Thousand' . ($n % 1000 ? ' ' . $inWords($n % 1000) : '');
                if ($n < 10000000) return $inWords((int)($n / 100000)) . ' Lakh' . ($n % 100000 ? ' ' . $inWords($n % 100000) : '');
                return $inWords((int)($n / 10000000)) . ' Crore' . ($n % 10000000 ? ' ' . $inWords($n % 10000000) : '');
            };

            $whole = (int)floor($num);
            $words = trim($inWords($whole));
            return '** ' . ($words ? $words . ' Rupees Only' : 'Zero Rupees Only') . ' **';
        }
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Invoice #{{ $repair->repair_no }} - LOGICTECH</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@600;700;800;900&family=Plus+Jakarta+Sans:wght@500;600;700;800&display=swap" rel="stylesheet">
<script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
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
  .no-print-bar .hint-pill {
    background: rgba(56, 189, 248, 0.12);
    border: 1px solid rgba(56, 189, 248, 0.3);
    color: #38bdf8;
    padding: 7px 16px;
    border-radius: 20px;
    font-size: 12px;
    font-weight: 600;
    display: flex; align-items: center; gap: 8px;
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
  .no-print-bar .btn-print    { background: linear-gradient(135deg, #ef4444, #dc2626); color: #fff; }
  .no-print-bar .btn-save     { background: linear-gradient(135deg, #16a34a, #15803d); color: #fff; }
  .no-print-bar .btn-jobsheet { background: linear-gradient(135deg, #0284c7, #0369a1); color: #fff; }
  .no-print-bar .btn-add      { background: linear-gradient(135deg, #3b82f6, #2563eb); color: #fff; }
  .no-print-bar .btn-del      { background: linear-gradient(135deg, #f59e0b, #d97706); color: #fff; }
  .no-print-bar .btn-gst      { background: linear-gradient(135deg, #10b981, #059669); color: #fff; }
  .no-print-bar .btn-back     { background: linear-gradient(135deg, #475569, #334155); color: #fff; }

  .page-pad {
    width: 210mm;
    min-height: auto;
    margin: 0 auto;
    background: #ffffff;
    padding: 10mm 12mm 10mm 12mm;
    box-shadow: 0 8px 30px rgba(0,0,0,0.15);
    position: relative;
  }

  /* Editable helper styles */
  [contenteditable="true"] {
    outline: none;
    transition: background 0.15s ease, box-shadow 0.15s ease;
    border-radius: 2px;
  }
  [contenteditable="true"]:hover {
    background: #f0f9ff !important;
    outline: 1px dashed #0284c7 !important;
    cursor: text;
  }
  [contenteditable="true"]:focus {
    background: #e0f2fe !important;
    outline: 2px solid #0284c7 !important;
    box-shadow: 0 0 0 3px rgba(2, 132, 199, 0.2) !important;
  }

  /* Header Layout */
  .pad-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 6px;
  }
  .logo-block {
    display: flex;
    flex-direction: column;
  }
  .logo-main {
    font-family: 'Outfit', sans-serif;
    font-size: 32px;
    font-weight: 900;
    letter-spacing: -0.5px;
    color: #000;
    line-height: 0.95;
  }
  .logo-main span.red-o { color: #dc2626; }
  .logo-sub {
    font-family: 'Outfit', sans-serif;
    font-size: 13px;
    font-weight: 700;
    color: #000;
    letter-spacing: 0.5px;
    margin-top: 3px;
  }

  .deals-block {
    text-align: right;
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-size: 10.5px;
    font-weight: 700;
    color: #000;
    line-height: 1.35;
    max-width: 330px;
  }

  /* Quotation / Invoice Checkboxes */
  .doc-type-bar {
    text-align: center;
    margin-bottom: 10px;
    font-family: 'Outfit', sans-serif;
    font-size: 13px;
    font-weight: 800;
    letter-spacing: 1px;
  }
  .checkbox-box {
    display: inline-block;
    width: 14px;
    height: 14px;
    border: 1.5px solid #000;
    margin-left: 4px;
    vertical-align: middle;
    text-align: center;
    line-height: 12px;
    font-size: 11px;
    font-weight: 900;
    cursor: pointer;
  }

  /* Meta Info Lines: Sr., Date, M/s. */
  .meta-lines {
    margin-bottom: 10px;
    font-size: 12px;
    font-weight: 700;
    color: #000;
  }
  .meta-line-row {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    margin-bottom: 8px;
  }
  .line-field {
    border-bottom: 1.5px solid #000;
    padding-bottom: 1px;
    display: inline-block;
  }

  /* Table Grid */
  .table-wrap {
    flex-grow: 1;
    display: flex;
    flex-direction: column;
  }
  .pad-table {
    width: 100%;
    border-collapse: collapse;
    border: 2px solid #000;
  }
  .pad-table th {
    padding: 6px 4px;
    font-family: 'Outfit', sans-serif;
    font-size: 10.5px;
    font-weight: 800;
    text-transform: uppercase;
    letter-spacing: 0.3px;
    color: #ffffff;
    border: 1.5px solid #000;
  }
  .pad-table th.th-red {
    background-color: #d92525 !important;
    text-align: center;
  }
  .pad-table th.th-dark {
    background-color: #262626 !important;
  }
  .pad-table td {
    border-right: 1.5px solid #000;
    border-left: 1.5px solid #000;
    border-bottom: 1px solid #52525b;
    padding: 5px 6px;
    font-size: 11px;
    font-weight: 600;
    color: #000;
    height: 30px;
    vertical-align: middle;
  }
  .pad-table td.td-center { text-align: center; }
  .pad-table td.td-right  { text-align: right; }

  /* Summary Box inside Table Footer */
  .sum-cell-lbl {
    font-family: 'Outfit', sans-serif;
    font-weight: 800;
    font-size: 11px;
    text-align: right;
    padding-right: 10px;
    border-top: 1.5px solid #000 !important;
    border-right: 1.5px solid #000 !important;
    background: #f8fafc;
  }
  .sum-cell-val {
    font-family: 'Plus Jakarta Sans', sans-serif;
    font-weight: 800;
    font-size: 11.5px;
    text-align: right;
    padding-right: 6px;
    border-top: 1.5px solid #000 !important;
    background: #ffffff;
  }

  /* Footer Section */
  .pad-footer {
    margin-top: 18px;
    border-top: 2px solid #000;
    padding-top: 8px;
  }
  .footer-row-1 {
    display: flex;
    justify-content: space-between;
    font-family: 'Outfit', sans-serif;
    font-size: 11px;
    font-weight: 800;
    color: #000;
    margin-bottom: 4px;
  }
  .footer-row-2 {
    display: flex;
    justify-content: space-between;
    align-items: flex-end;
    font-size: 10.5px;
    font-weight: 700;
    color: #000;
  }

  @media print {
    @page { size: A4 portrait; margin: 4mm; }
    body { background: #fff; padding: 0; }
    .no-print { display: none !important; }
    .page-pad { width: 100%; min-height: auto; box-shadow: none; padding: 4mm; margin: 0; }
    [contenteditable="true"] { background: transparent !important; outline: none !important; box-shadow: none !important; }
    .pad-table th.th-red { background-color: #d92525 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
    .pad-table th.th-dark { background-color: #262626 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; }
  }
</style>
</head>
<body>

<div class="no-print-bar no-print">
    <div class="action-group">
        <button onclick="saveInvoiceData()" class="btn-save" id="btnSaveInvoice">💾 Save Invoice</button>
        <button onclick="window.print()" class="btn-print">🖨️ Print Invoice</button>
        <a href="{{ route('repair.print.jobsheet', $repair->id) }}" class="btn-jobsheet">📋 A4 Job Sheet</a>
        <button onclick="addNewRow()" class="btn-add">➕ Add Item Row</button>
        <button onclick="removeLastRow()" class="btn-del">🗑️ Remove Last Row</button>
        <button onclick="toggleGstAll()" class="btn-gst" id="btnGstToggle">⚡ Apply 18% GST</button>
        <a href="{{ route('repair.show', $repair->id) }}" class="btn-back">← Back to Repair Details</a>
    </div>
</div>

<div class="page-pad">
    <div class="table-wrap">
        {{-- Header --}}
        <div class="pad-header">
            <div class="logo-block">
                @if(!empty($coLogo))
                    <img src="{{ $coLogo }}" alt="LOGICTECH" style="max-height: 55px; width: auto; object-fit: contain;">
                @else
                    <div class="logo-main">L<span class="red-o">Ó</span>GICTECH</div>
                    <div class="logo-sub">induction Heating Solutions</div>
                @endif
            </div>
            <div class="deals-block" contenteditable="true">
                <strong>Deals In:</strong> All kinds of Induction Heater,<br>
                Induction Melting Furnace,<br>
                Industrial Automation,<br>
                Programing &amp; Repairing
            </div>
        </div>

        {{-- Type Checkboxes --}}
        <div class="doc-type-bar">
            QUOTATION <span class="checkbox-box" id="chkQuotation" onclick="toggleDocType('quotation')">{!! ($repair->doc_type ?? 'invoice') === 'quotation' ? '&#10004;' : '' !!}</span> &nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
            INVOICE <span class="checkbox-box" id="chkInvoice" onclick="toggleDocType('invoice')">{!! ($repair->doc_type ?? 'invoice') === 'invoice' ? '&#10004;' : '' !!}</span>
        </div>

        {{-- Meta Information Lines --}}
        <div class="meta-lines">
            <div class="meta-line-row">
                <div>
                    Sr. <span class="line-field" contenteditable="true" style="min-width: 160px; font-weight: 800; font-family: monospace;">{{ $repair->repair_no }}</span>
                </div>
                <div>
                    Date. <span class="line-field" contenteditable="true" style="min-width: 140px;">{{ date('d-M-Y', strtotime($repair->received_date ?? now())) }}</span>
                </div>
            </div>
            <div>
                M/s. <span class="line-field" contenteditable="true" style="width: calc(100% - 45px); font-weight: 800;">
                    {{ $repair->customer_display_name }} @if($repair->customer_display_phone) ({{ $repair->customer_display_phone }}) @endif @if($repair->customer_display_address) - {{ $repair->customer_display_address }} @endif
                </span>
            </div>
        </div>

        {{-- Main Invoice Grid Table Matching Uploaded Column Specs --}}
        <table class="pad-table" id="invoiceGridTable">
            <thead>
                <tr>
                    <th class="th-red" style="width: 4%;">No.</th>
                    <th class="th-dark" style="width: 33%; text-align: left;">DESCRIPTION</th>
                    <th class="th-red" style="width: 6%;">QTY</th>
                    <th class="th-dark" style="width: 11%; text-align: right;">RATE</th>
                    <th class="th-red" style="width: 14%; text-align: right;">GROSS AMOUNT</th>
                    <th class="th-dark" style="width: 6%;">GST %</th>
                    <th class="th-red" style="width: 11%; text-align: right;">GST AMOUNT</th>
                    <th class="th-dark" style="width: 15%; text-align: right;">TOTAL AMOUNT</th>
                </tr>
            </thead>
            <tbody id="invoiceTableBody">
                @php
                    $itemsData = json_decode($repair->problem_description, true);
                    $rows = [];

                    if (is_array($itemsData) && count($itemsData) > 0) {
                        foreach($itemsData as $idx => $it) {
                            if (isset($it['desc'])) {
                                // Saved directly from A4 screen
                                $qty = isset($it['qty']) && $it['qty'] !== '' ? (float)$it['qty'] : 1;
                                $rate = (float)($it['rate'] ?? 0);
                                $gross = (float)($it['gross'] ?? ($qty * $rate));
                                $gst_pct = (float)($it['gst_pct'] ?? 0);
                                $gst_amt = (float)($it['gst_amt'] ?? 0);
                                $total = (float)($it['total'] ?? ($gross + $gst_amt));

                                $rows[] = [
                                    'no' => $it['sn'] ?? ($idx + 1),
                                    'desc' => $it['desc'],
                                    'qty' => $qty,
                                    'rate' => $rate,
                                    'gross' => $gross,
                                    'gst_pct' => $gst_pct,
                                    'gst_amt' => $gst_amt,
                                    'total' => $total,
                                ];
                            } else {
                                // Initial intake format
                                $rate = (float)($it['estimated_cost'] ?? 0);
                                if ($rate == 0 && count($itemsData) == 1 && $totalBill > 0) {
                                    $rate = $totalBill;
                                }
                                $descTitle = $it['item_name'] ?? $repair->item_name;
                                $descExtra = [];
                                if (!empty($it['brand_model'])) $descExtra[] = 'Model: ' . $it['brand_model'];
                                if (!empty($it['serial_no'])) $descExtra[] = 'SN: ' . $it['serial_no'];
                                if (!empty($it['problem_description']) && $it['problem_description'] !== $descTitle) $descExtra[] = $it['problem_description'];
                                $fullDesc = $descTitle . (!empty($descExtra) ? ' (' . implode(' | ', $descExtra) . ')' : '');

                                $rows[] = [
                                    'no' => $it['sn'] ?? ($idx + 1),
                                    'desc' => $fullDesc,
                                    'qty' => 1,
                                    'rate' => $rate,
                                    'gross' => $rate,
                                    'gst_pct' => 0,
                                    'gst_amt' => 0,
                                    'total' => $rate,
                                ];
                            }
                        }
                    } else {
                        // Single Repair Item Format
                        $serviceCharges = (float)($repair->service_charges > 0 ? $repair->service_charges : $repair->estimated_cost);
                        $partsCharges   = (float)($repair->parts_charges ?? 0);

                        $rows[] = [
                            'no' => 1,
                            'desc' => "Repair & Servicing: " . $repair->item_name . ($repair->brand_model ? ' ('.$repair->brand_model.')' : '') . ($repair->serial_no ? ' [S/N: '.$repair->serial_no.']' : ''),
                            'qty' => 1,
                            'rate' => $serviceCharges,
                            'gross' => $serviceCharges,
                            'gst_pct' => 0,
                            'gst_amt' => 0,
                            'total' => $serviceCharges,
                        ];

                        if ($partsCharges > 0) {
                            $rows[] = [
                                'no' => 2,
                                'desc' => "Replacement Spare Parts / Components Charges",
                                'qty' => 1,
                                'rate' => $partsCharges,
                                'gross' => $partsCharges,
                                'gst_pct' => 0,
                                'gst_amt' => 0,
                                'total' => $partsCharges,
                            ];
                        }
                    }

                    // Total initial rows in pad grid: 10 rows
                    $totalRowsToFill = 10;
                    $actualCount = count($rows);
                @endphp

                @foreach($rows as $r)
                    <tr class="item-grid-row">
                        <td class="td-center row-sn-cell" contenteditable="true" oninput="recalculateTotals()">{{ $r['no'] }}</td>
                        <td class="row-desc-cell" contenteditable="true" oninput="recalculateTotals()">{{ $r['desc'] }}</td>
                        <td class="td-center row-qty-cell" contenteditable="true" oninput="recalculateTotals()">{{ $r['qty'] }}</td>
                        <td class="td-right row-rate-cell" contenteditable="true" oninput="recalculateTotals()">{{ number_format($r['rate'], 2, '.', '') }}</td>
                        <td class="td-right row-gross-cell" contenteditable="true" style="font-weight: 700;" oninput="recalculateTotals()">{{ number_format($r['gross'], 2, '.', '') }}</td>
                        <td class="td-center row-gstpct-cell" contenteditable="true" oninput="recalculateTotals()">{{ $r['gst_pct'] }}</td>
                        <td class="td-right row-gstamt-cell" contenteditable="true" style="color: #047857;" oninput="recalculateTotals()">{{ number_format($r['gst_amt'], 2, '.', '') }}</td>
                        <td class="td-right row-total-cell" contenteditable="true" style="font-weight: 800;" oninput="recalculateTotals()">{{ number_format($r['total'], 2, '.', '') }}</td>
                    </tr>
                @endforeach

                @for($i = $actualCount + 1; $i <= $totalRowsToFill; $i++)
                    <tr class="item-grid-row">
                        <td class="td-center row-sn-cell" contenteditable="true" oninput="recalculateTotals()"></td>
                        <td class="row-desc-cell" contenteditable="true" oninput="recalculateTotals()"></td>
                        <td class="td-center row-qty-cell" contenteditable="true" oninput="recalculateTotals()"></td>
                        <td class="td-right row-rate-cell" contenteditable="true" oninput="recalculateTotals()"></td>
                        <td class="td-right row-gross-cell" contenteditable="true" style="font-weight: 700;" oninput="recalculateTotals()"></td>
                        <td class="td-center row-gstpct-cell" contenteditable="true" oninput="recalculateTotals()"></td>
                        <td class="td-right row-gstamt-cell" contenteditable="true" style="color: #047857;" oninput="recalculateTotals()"></td>
                        <td class="td-right row-total-cell" contenteditable="true" style="font-weight: 800;" oninput="recalculateTotals()"></td>
                    </tr>
                @endfor
            </tbody>
            <tfoot>
                <tr id="grandTotalRow">
                    <td colspan="4" class="sum-cell-lbl" style="font-size: 12px; font-weight: 900; text-align: right; padding-right: 12px;">Grand Total (PKR)</td>
                    <td class="sum-cell-val" id="totalGrossVal" contenteditable="true" style="font-weight: 800; text-align: right;" oninput="manualTotalChange()">Rs. {{ number_format($totalBill, 2) }}</td>
                    <td class="sum-cell-val td-center" style="font-weight: 700; text-align: center;">-</td>
                    <td class="sum-cell-val" id="totalGstVal" contenteditable="true" style="font-weight: 800; color: #047857; text-align: right;" oninput="manualTotalChange()">Rs. 0.00</td>
                    <td class="sum-cell-val" id="grandTotalVal" contenteditable="true" style="font-size: 13px; font-weight: 900; color: #000; text-align: right;" oninput="manualTotalChange()">Rs. {{ number_format($totalBill, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="2" class="sum-cell-lbl" style="font-weight: 800; text-align: left; padding-left: 8px;">Amount in words</td>
                    <td colspan="6" class="sum-cell-val" id="amountInWordsVal" contenteditable="true" style="font-weight: 700; text-align: center; font-style: italic; background: #f8fafc;">{{ numberToWordsPhp($totalBill) }}</td>
                </tr>
                <tr>
                    <td colspan="7" class="sum-cell-lbl">Advance</td>
                    <td class="sum-cell-val" id="advanceVal" contenteditable="true" oninput="manualTotalChange()">Rs. {{ number_format($advancePaid, 2) }}</td>
                </tr>
                <tr>
                    <td colspan="7" class="sum-cell-lbl" style="font-weight: 900; color: #dc2626;">Balance Due</td>
                    <td class="sum-cell-val" id="balanceVal" contenteditable="true" style="color: #dc2626; font-size: 13px; font-weight: 900;">Rs. {{ number_format($dueAmount, 2) }}</td>
                </tr>
            </tfoot>
        </table>
    </div>

    {{-- Footer --}}
    <div class="pad-footer">
        <div class="footer-row-1">
            <div contenteditable="true">SHEIKHUPURA PUNJAB PAKISTAN</div>
            <div contenteditable="true">www.inductionheats.com</div>
        </div>
        <div class="footer-row-2">
            <div contenteditable="true">
                Cell: 0300-9464887, 0321-9596972 &nbsp;&nbsp;&nbsp;&nbsp; info@logictech.com.pk
            </div>
            <div style="font-weight: 800;" contenteditable="true">
                Signature. ___________________________
            </div>
        </div>
    </div>
</div>

<script data-version="{{ time() }}">
    let globalGstState = false;

    function round2(num) {
        let n = parseFloat(num);
        if (isNaN(n)) return 0;
        return Math.round((n + Number.EPSILON) * 100) / 100;
    }

    function parseNum(val) {
        if (val === null || val === undefined) return 0;
        if (typeof val === 'number') return isNaN(val) ? 0 : val;
        let s = val.toString()
            .replace(/Rs\.?/gi, ' ')
            .replace(/PKR\.?/gi, ' ')
            .replace(/[a-zA-Z]/g, ' ')
            .replace(/,/g, '')
            .trim();
        s = s.replace(/^\.+/, '');
        if (s === '') return 0;
        let match = s.match(/-?\d+(\.\d+)?/);
        if (!match) return 0;
        let num = parseFloat(match[0]);
        return isNaN(num) ? 0 : num;
    }

    function formatRs(num) {
        const val = round2(num);
        return 'Rs. ' + val.toLocaleString('en-US', {minimumFractionDigits: 2, maximumFractionDigits: 2});
    }

    function numberToWords(num) {
        num = round2(num);
        if (num <= 0) return '** Zero Rupees Only **';
        const ones = ['', 'One', 'Two', 'Three', 'Four', 'Five', 'Six', 'Seven', 'Eight', 'Nine', 'Ten', 'Eleven', 'Twelve', 'Thirteen', 'Fourteen', 'Fifteen', 'Sixteen', 'Seventeen', 'Eighteen', 'Nineteen'];
        const tens = ['', '', 'Twenty', 'Thirty', 'Forty', 'Fifty', 'Sixty', 'Seventy', 'Eighty', 'Ninety'];

        function inWords (n) {
            if (n < 20) return ones[n];
            if (n < 100) return tens[Math.floor(n / 10)] + (n % 10 ? ' ' + ones[n % 10] : '');
            if (n < 1000) return ones[Math.floor(n / 100)] + ' Hundred' + (n % 100 ? ' and ' + inWords(n % 100) : '');
            if (n < 100000) return inWords(Math.floor(n / 1000)) + ' Thousand' + (n % 1000 ? ' and ' + inWords(n % 1000) : '');
            if (n < 10000000) return inWords(Math.floor(n / 100000)) + ' Lakh' + (n % 100000 ? ' ' + inWords(n % 100000) : '');
            return inWords(Math.floor(n / 10000000)) + ' Crore' + (n % 10000000 ? ' ' + inWords(n % 10000000) : '');
        }
        let whole = Math.floor(num);
        let words = inWords(whole).trim();
        return '** ' + (words ? words + ' Rupees Only' : 'Zero Rupees Only') + ' **';
    }

    function toggleGstAll() {
        globalGstState = !globalGstState;
        const btn = document.getElementById('btnGstToggle');
        const rows = document.querySelectorAll('#invoiceTableBody tr.item-grid-row');

        rows.forEach(row => {
            const qtyCell = row.querySelector('.row-qty-cell');
            const gstPctCell = row.querySelector('.row-gstpct-cell');
            if (qtyCell && parseNum(qtyCell.innerText || qtyCell.textContent) > 0 && gstPctCell) {
                gstPctCell.innerText = globalGstState ? '18' : '0';
            }
        });

        if (btn) {
            btn.innerText = globalGstState ? '✔ 18% GST Applied' : '⚡ Apply 18% GST';
            btn.style.background = globalGstState ? '#047857' : '#059669';
        }
        recalculateTotals();
    }

    function recalculateTotals() {
        let sumGross = 0;
        let sumGst = 0;
        let sumGrandTotal = 0;
        let autoSnCounter = 0;

        const rows = document.querySelectorAll('#invoiceTableBody tr.item-grid-row');
        rows.forEach(row => {
            const snCell     = row.querySelector('.row-sn-cell');
            const descCell   = row.querySelector('.row-desc-cell');
            const qtyCell    = row.querySelector('.row-qty-cell');
            const rateCell   = row.querySelector('.row-rate-cell');
            const grossCell  = row.querySelector('.row-gross-cell');
            const gstPctCell = row.querySelector('.row-gstpct-cell');
            const gstAmtCell = row.querySelector('.row-gstamt-cell');
            const totalCell  = row.querySelector('.row-total-cell');

            const descStr  = descCell ? (descCell.innerText || descCell.textContent || '').trim() : '';
            const qtyStr   = qtyCell ? (qtyCell.innerText || qtyCell.textContent || '').trim() : '';
            const rateStr  = rateCell ? (rateCell.innerText || rateCell.textContent || '').trim() : '';
            const grossStr = grossCell ? (grossCell.innerText || grossCell.textContent || '').trim() : '';
            const totalStr = totalCell ? (totalCell.innerText || totalCell.textContent || '').trim() : '';

            const hasQtyInput  = qtyStr !== '';
            const hasRateInput = rateStr !== '';
            const hasDescInput = descStr !== '';

            const qtyVal  = parseNum(qtyStr);
            const rateVal = parseNum(rateStr);
            const gstPct  = parseNum(gstPctCell ? (gstPctCell.innerText || gstPctCell.textContent) : 0);

            let effectiveQty = 0;
            let isRowActive = false;

            if (hasDescInput || hasQtyInput || (hasRateInput && rateVal > 0) || (totalStr !== '' && parseNum(totalStr) > 0)) {
                isRowActive = true;
            }

            if (isRowActive) {
                autoSnCounter++;
                if (snCell) {
                    snCell.innerText = autoSnCounter;
                }

                if (hasQtyInput) {
                    effectiveQty = qtyVal;
                    if (qtyStr === '0' || qtyVal === 0) {
                        effectiveQty = 0;
                    }
                } else if (hasRateInput && rateVal > 0) {
                    effectiveQty = 1;
                }

                const grossAmt = round2(effectiveQty * rateVal);
                const gstAmt   = round2(grossAmt * (gstPct / 100));
                const rowTotal = round2(grossAmt + gstAmt);

                if (grossCell && (hasQtyInput || hasRateInput)) grossCell.innerText = grossAmt.toFixed(2);
                if (gstAmtCell && (hasQtyInput || hasRateInput)) gstAmtCell.innerText = gstAmt.toFixed(2);
                if (totalCell && (hasQtyInput || hasRateInput)) totalCell.innerText = rowTotal.toFixed(2);

                const finalGross = round2(parseNum(grossCell ? (grossCell.innerText || grossCell.textContent) : grossAmt));
                const finalGst   = round2(parseNum(gstAmtCell ? (gstAmtCell.innerText || gstAmtCell.textContent) : gstAmt));
                const finalTotal = round2(parseNum(totalCell ? (totalCell.innerText || totalCell.textContent) : rowTotal));

                sumGross      = round2(sumGross + finalGross);
                sumGst        = round2(sumGst + finalGst);
                sumGrandTotal = round2(sumGrandTotal + finalTotal);
            } else {
                if (snCell) snCell.innerText = '';
                if (grossCell) grossCell.innerText = '';
                if (gstAmtCell) gstAmtCell.innerText = '';
                if (totalCell) totalCell.innerText = '';
            }
        });

        sumGross      = round2(sumGross);
        sumGst        = round2(sumGst);
        sumGrandTotal = round2(sumGrandTotal);

        if (document.getElementById('totalGrossVal')) {
            document.getElementById('totalGrossVal').innerText = formatRs(sumGross);
        }
        if (document.getElementById('totalGstVal')) {
            document.getElementById('totalGstVal').innerText = formatRs(sumGst);
        }
        if (document.getElementById('grandTotalVal')) {
            document.getElementById('grandTotalVal').innerText = formatRs(sumGrandTotal);
        }

        if (document.getElementById('amountInWordsVal')) {
            document.getElementById('amountInWordsVal').innerText = numberToWords(sumGrandTotal);
        }

        const advanceCell = document.getElementById('advanceVal');
        const advanceText = advanceCell ? (advanceCell.innerText || advanceCell.textContent || '0') : '0';
        const advance = round2(parseNum(advanceText));
        const balance = round2(Math.max(0, sumGrandTotal - advance));
        if (document.getElementById('balanceVal')) {
            document.getElementById('balanceVal').innerText = formatRs(balance);
        }
    }

    function manualTotalChange() {
        const grandTotalCell = document.getElementById('grandTotalVal');
        const advanceCell = document.getElementById('advanceVal');

        const grandTotalText = grandTotalCell ? (grandTotalCell.innerText || grandTotalCell.textContent || '0') : '0';
        const advanceText = advanceCell ? (advanceCell.innerText || advanceCell.textContent || '0') : '0';

        const grandTotal = round2(parseNum(grandTotalText));
        const advance = round2(parseNum(advanceText));

        const balance = round2(Math.max(0, grandTotal - advance));
        if (document.getElementById('balanceVal')) {
            document.getElementById('balanceVal').innerText = formatRs(balance);
        }
        if (document.getElementById('amountInWordsVal')) {
            document.getElementById('amountInWordsVal').innerText = numberToWords(grandTotal);
        }
    }

    function addNewRow() {
        const tbody = document.getElementById('invoiceTableBody');
        const rows = tbody.querySelectorAll('tr.item-grid-row');
        const nextSn = rows.length + 1;

        const tr = document.createElement('tr');
        tr.className = 'item-grid-row';
        tr.innerHTML = `
            <td class="td-center row-sn-cell" contenteditable="true">${nextSn}</td>
            <td class="row-desc-cell" contenteditable="true">Enter description...</td>
            <td class="td-center row-qty-cell" contenteditable="true" oninput="recalculateTotals()">1</td>
            <td class="td-right row-rate-cell" contenteditable="true" oninput="recalculateTotals()">0.00</td>
            <td class="td-right row-gross-cell" contenteditable="true" style="font-weight: 700;" oninput="recalculateTotals()">0.00</td>
            <td class="td-center row-gstpct-cell" contenteditable="true" oninput="recalculateTotals()">${globalGstState ? '18' : '0'}</td>
            <td class="td-right row-gstamt-cell" contenteditable="true" style="color: #047857;" oninput="recalculateTotals()">0.00</td>
            <td class="td-right row-total-cell" contenteditable="true" style="font-weight: 800;" oninput="recalculateTotals()">0.00</td>
        `;
        tbody.appendChild(tr);
        recalculateTotals();
    }

    function removeLastRow() {
        const tbody = document.getElementById('invoiceTableBody');
        const rows = tbody.querySelectorAll('tr.item-grid-row');
        if (rows.length > 1) {
            rows[rows.length - 1].remove();
            recalculateTotals();
        }
    }

    let currentDocType = '{{ $repair->doc_type ?? "invoice" }}';

    function toggleDocType(type) {
        currentDocType = type;
        const qBox = document.getElementById('chkQuotation');
        const iBox = document.getElementById('chkInvoice');
        if (type === 'quotation') {
            qBox.innerHTML = '&#10004;';
            iBox.innerHTML = '';
        } else {
            qBox.innerHTML = '';
            iBox.innerHTML = '&#10004;';
        }
    }

    async function saveInvoiceData() {
        const btn = document.getElementById('btnSaveInvoice');
        const origText = btn ? btn.innerHTML : '';
        if (btn) {
            btn.disabled = true;
            btn.innerHTML = '⏳ Saving...';
        }

        try {
            const rows = [];
            const trs = document.querySelectorAll('#invoiceTableBody tr.item-grid-row');
            trs.forEach(tr => {
                const snCell = tr.querySelector('.row-sn-cell');
                const descCell = tr.querySelector('.row-desc-cell');
                const qtyCell = tr.querySelector('.row-qty-cell');
                const rateCell = tr.querySelector('.row-rate-cell');
                const grossCell = tr.querySelector('.row-gross-cell');
                const gstPctCell = tr.querySelector('.row-gstpct-cell');
                const gstAmtCell = tr.querySelector('.row-gstamt-cell');
                const totalCell = tr.querySelector('.row-total-cell');

                const desc = descCell ? (descCell.innerText || descCell.textContent || '').trim() : '';
                const qty = parseNum(qtyCell ? (qtyCell.innerText || qtyCell.textContent) : 0);
                const rate = parseNum(rateCell ? (rateCell.innerText || rateCell.textContent) : 0);
                const gross = parseNum(grossCell ? (grossCell.innerText || grossCell.textContent) : 0);
                const gstPct = parseNum(gstPctCell ? (gstPctCell.innerText || gstPctCell.textContent) : 0);
                const gstAmt = parseNum(gstAmtCell ? (gstAmtCell.innerText || gstAmtCell.textContent) : 0);
                const total = parseNum(totalCell ? (totalCell.innerText || totalCell.textContent) : 0);

                if (desc !== '' || total > 0 || qty > 0) {
                    rows.push({
                        sn: snCell ? (snCell.innerText || snCell.textContent || '').trim() : '',
                        desc: desc,
                        qty: qty,
                        rate: rate,
                        gross: gross,
                        gst_pct: gstPct,
                        gst_amt: gstAmt,
                        total: total
                    });
                }
            });

            const grandTotalVal = parseNum(document.getElementById('grandTotalVal')?.innerText || 0);
            const advanceVal = parseNum(document.getElementById('advanceVal')?.innerText || 0);
            const balanceVal = parseNum(document.getElementById('balanceVal')?.innerText || 0);

            const customerNameCell = document.querySelector('.meta-lines .line-field[style*="width: calc"]');
            const customerName = customerNameCell ? customerNameCell.innerText.trim() : '';

            const dateCell = document.querySelector('.meta-lines .meta-line-row div:nth-child(2) .line-field');
            const receivedDate = dateCell ? dateCell.innerText.trim() : '';

            const payload = {
                _token: '{{ csrf_token() }}',
                doc_type: currentDocType,
                items: rows,
                total_charges: grandTotalVal,
                advance_paid: advanceVal,
                due_amount: balanceVal,
                customer_name: customerName,
                received_date: receivedDate
            };

            const response = await fetch('{{ route("repair.save.a4", $repair->id) }}', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                    'Accept': 'application/json',
                    'X-CSRF-TOKEN': '{{ csrf_token() }}'
                },
                body: JSON.stringify(payload)
            });

            const resData = await response.json();

            if (response.ok && resData.status === 'success') {
                if (typeof Swal !== 'undefined') {
                    Swal.fire({
                        icon: 'success',
                        title: 'Saved Successfully',
                        text: resData.message,
                        timer: 2500,
                        showConfirmButton: false,
                        toast: true,
                        position: 'top-end'
                    });
                } else {
                    alert(resData.message);
                }
            } else {
                throw new Error(resData.message || 'Error occurred while saving invoice');
            }
        } catch (err) {
            if (typeof Swal !== 'undefined') {
                Swal.fire({
                    icon: 'error',
                    title: 'Save Failed',
                    text: err.message,
                });
            } else {
                alert('Save Failed: ' + err.message);
            }
        } finally {
            if (btn) {
                btn.disabled = false;
                btn.innerHTML = origText;
            }
        }
    }

    document.addEventListener('DOMContentLoaded', () => {
        recalculateTotals();

        const grid = document.getElementById('invoiceGridTable');
        if (grid) {
            ['input', 'keyup', 'blur', 'change', 'paste', 'focusout'].forEach(evt => {
                grid.addEventListener(evt, () => {
                    recalculateTotals();
                });
            });
        }
    });
</script>
</body>
</html>
