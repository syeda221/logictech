@php
    if (!function_exists('numberToWordsInvoice')) {
        function numberToWordsInvoice($number) {
            $d = [0=>'Zero',1=>'One',2=>'Two',3=>'Three',4=>'Four',5=>'Five',6=>'Six',7=>'Seven',8=>'Eight',9=>'Nine',10=>'Ten',11=>'Eleven',12=>'Twelve',13=>'Thirteen',14=>'Fourteen',15=>'Fifteen',16=>'Sixteen',17=>'Seventeen',18=>'Eighteen',19=>'Nineteen',20=>'Twenty',30=>'Thirty',40=>'Forty',50=>'Fifty',60=>'Sixty',70=>'Seventy',80=>'Eighty',90=>'Ninety',100=>'Hundred',1000=>'Thousand',1000000=>'Million',1000000000=>'Billion'];
            if (!is_numeric($number)) return '';
            $v = (float)$number;
            if ($v < 0) return 'Negative '.numberToWordsInvoice(abs($v));
            [$ip,$fp] = explode('.', number_format($v,2,'.',''));
            $n = (int)$ip;
            $cg = function($x) use (&$cg,$d) {
                if ($x<21)  return $d[$x];
                if ($x<100) { $t=((int)($x/10))*10; $u=$x%10; return $d[$t].($u?'-'.$d[$u]:''); }
                if ($x<1000){ $h=(int)($x/100);$r=$x%100; return $d[$h].' '.$d[100].($r?' '.$cg($r):''); }
                if ($x<1e6) { $t=(int)($x/1000);$r=$x%1000; return $cg($t).' '.$d[1000].($r?' '.$cg($r):''); }
                if ($x<1e9) { $m=(int)($x/1e6);$r=$x%1e6; return $cg($m).' '.$d[1000000].($r?' '.$cg($r):''); }
                $b=(int)($x/1e9);$r=$x%1e9; return $cg($b).' '.$d[1000000000].($r?' '.$cg($r):'');
            };
            $w = $n===0?'Zero':$cg($n);
            $f = (int)$fp;
            return $f>0 ? trim($w).' Rupees and '.trim($cg($f)).' Paisas Only' : trim($w).' Only';
        }
    }

    /* ── Settings ── */
    $coName    = \App\Models\Setting::get('company_name',    'LOGIC TECH ENGINEERING');
    $coAddr    = \App\Models\Setting::get('company_address', '01-KM Sharaqpur Road');
    $coEmail   = \App\Models\Setting::get('web_contact_email') ?: \App\Models\Setting::get('company_email','info@logictech.com.pk');
    $coPhone   = \App\Models\Setting::get('company_phone')   ?: \App\Models\Setting::get('web_contact_phone','92 300 5308035');
    $coLogo    = \App\Models\Setting::getLogoUrl();
    $coNtn     = \App\Models\Setting::get('company_ntn',  '5561761-4');
    $coStrn    = \App\Models\Setting::get('company_strn',  '');
    $currency  = \App\Models\Setting::get('currency_symbol','PKR');

    /* ── Vendor Details ── */
    $vendorName = $purchase->vendor->name ?? 'N/A';
    $vendorAddr = $purchase->vendor->address ?? '';
    $vendorPhone = $purchase->vendor->phone ?? ($purchase->vendor->mobile ?? '');
    $vendorEmail = $purchase->vendor->email ?? '';
    $vendorCnic  = $purchase->vendor->cnic ?? ($purchase->vendor->ntn ?? '');

    /* ── Totals ── */
    $subTotal     = (float)($purchase->subtotal ?? 0);
    $itemDisc     = (float)($purchase->items->sum('item_discount') ?? 0);
    $addDisc      = (float)($purchase->additional_discount ?? 0);
    $extraCost    = (float)($purchase->extra_cost ?? 0);
    $netPayable   = (float)($purchase->net_amount ?? 0);
    $paidAmount   = (float)($purchase->paid_amount ?? 0);
    $billDue      = max(0, $netPayable - $paidAmount);
    $prevBal      = (float)($previousBalance ?? 0);
    $closeBal     = (float)($currentBalance ?? ($prevBal + $netPayable - $paidAmount));
    $amtWords     = numberToWordsInvoice($netPayable);

    $totalQty     = $purchase->items->sum('qty');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Purchase Invoice - {{ $purchase->invoice_no }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
/* ── Reset ── */
*{margin:0;padding:0;box-sizing:border-box;}

body{
    font-family:'Plus Jakarta Sans', -apple-system, BlinkMacSystemFont, 'Segoe UI', Roboto, Helvetica, Arial, sans-serif;
    font-size:10.5px;
    color:#1e293b;
    background:#e2e8f0;
    padding:12px 0;
    -webkit-font-smoothing:antialiased;
    -moz-osx-font-smoothing:grayscale;
    -webkit-print-color-adjust:exact;
    print-color-adjust:exact;
}

/* ── Green Theme Variables ── */
:root {
    --inv-primary-green: #15803d;      /* Main Emerald Green */
    --inv-dark-green: #14532d;         /* Forest Dark Green */
    --inv-border-green: #16a34a;       /* Border Green */
    --inv-border-subtle: #86efac;      /* Light Green Border */
    --inv-bg-light: #f0fdf4;           /* Soft Green Light BG */
    --inv-bg-highlight: #dcfce7;       /* Highlight Row BG */
    --inv-text-dark: #0f172a;
    --inv-text-muted: #475569;
}

/* ── Buttons ── */
.no-print{
    position:fixed;top:12px;right:20px;z-index:9999;
    display:flex;gap:8px;
}
.no-print button,.no-print a{
    padding:7px 18px;font-size:12px;font-weight:700;
    border-radius:6px;cursor:pointer;border:none;
    text-decoration:none;display:inline-flex;align-items:center;gap:6px;
    font-family:'Plus Jakarta Sans', sans-serif;
    transition:all 0.15s ease;
}
.no-print button{
    background:#15803d;color:#fff;
    box-shadow:0 3px 8px rgba(21,128,61,.35);
}
.no-print button:hover{
    background:#166534;
    transform:translateY(-1px);
}
.no-print a{
    background:#ffffff;color:#334155;
    border:1.5px solid #cbd5e1;
    box-shadow:0 1px 3px rgba(0,0,0,0.05);
}
.no-print a:hover{
    background:#f8fafc;
    color:#0f172a;
}

/* ── A4 Sheet ── */
.pg{
    width:210mm;
    min-height:297mm;
    margin:0 auto;
    background:#fff;
    padding:6mm 10mm 10mm;
    box-shadow:0 10px 30px rgba(0,0,0,.12);
    position:relative;
    border-radius:4px;
}

/* ══════════════════
   1. HEADER
══════════════════ */
.hdr{
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding-bottom:6px;
    border-bottom:2.5px solid var(--inv-border-green);
    margin-bottom:6px;
}
.hdr-logo{flex:0 0 auto;}
.hdr-logo img{max-width:210px;max-height:65px;object-fit:contain;display:block;}
.logo-txt{
    font-family:'Outfit', sans-serif;
    font-size:24px;font-weight:800;color:var(--inv-dark-green);
    text-transform:uppercase;letter-spacing:0.8px;line-height:1;
    border-left:4px solid var(--inv-border-green);
    padding-left:10px;
}
.hdr-info{
    text-align:right;
    display:flex;
    flex-direction:column;
    align-items:flex-end;
    gap:3px;
}
.hdr-info .co-name{
    font-family:'Outfit', sans-serif;
    font-size:18px;
    font-weight:800;
    color:var(--inv-dark-green);
    text-transform:uppercase;
    letter-spacing:0.4px;
    margin-bottom:1px;
    line-height:1.2;
}
.hdr-info .co-line{
    font-size:10.5px;
    color:#334155;
    font-weight:500;
    line-height:1.4;
    display:inline-flex;
    align-items:center;
    gap:6px;
}
.hdr-badge{
    display:inline-flex;
    align-items:center;
    gap:6px;
    background:var(--inv-bg-light);
    border:1px solid var(--inv-border-subtle);
    padding:2px 8px;
    border-radius:4px;
    font-size:10.5px;
    margin-top:2px;
}
.badge-ntn-tag{
    background:var(--inv-primary-green);
    color:#fff;
    font-size:9px;
    font-weight:800;
    padding:1.5px 6px;
    border-radius:3px;
    letter-spacing:0.5px;
    text-transform:uppercase;
    font-family:'Outfit', sans-serif;
}
.badge-strn-tag{
    background:#475569;
    color:#fff;
    font-size:9px;
    font-weight:800;
    padding:1.5px 6px;
    border-radius:3px;
    letter-spacing:0.5px;
    text-transform:uppercase;
    font-family:'Outfit', sans-serif;
}
.badge-val{
    font-weight:700;
    color:#0f172a;
    font-size:10.5px;
}

/* ══════════════════
   2. INVOICE TITLE
══════════════════ */
.inv-title{
    text-align:center;
    padding:4px 0 8px;
    margin-bottom:6px;
}
.inv-title span{
    font-family:'Outfit', sans-serif;
    font-size:22px;
    font-weight:800;
    color:var(--inv-dark-green);
    letter-spacing:6px;
    text-transform:uppercase;
    padding-bottom:3px;
    border-bottom:2.5px solid var(--inv-border-green);
    display:inline-block;
}

/* ══════════════════
   3. META TABLE (Balanced 4-Column Full Width)
══════════════════ */
.meta-wrap{
    width:100%;
    margin-bottom:8px;
    border:2px solid var(--inv-border-green);
    border-radius:4px;
    overflow:hidden;
    background:#fff;
    box-sizing:border-box;
}
.meta-tbl{
    width:100%;
    border-collapse:collapse;
    margin:0;
}
.meta-tbl td{
    border:1px solid var(--inv-border-subtle);
    padding:4px 8px;
    font-size:10px;
}
.meta-tbl .ml{
    font-family:'Outfit', sans-serif;
    font-weight:700;
    color:var(--inv-dark-green);
    background:var(--inv-bg-light);
    white-space:nowrap;
    width:16%;
    border:1px solid var(--inv-border-subtle);
    letter-spacing:0.2px;
}
.meta-tbl .mv{
    font-weight:600;
    color:#0f172a;
    width:34%;
    border:1px solid var(--inv-border-subtle);
    font-size:10.5px;
}
.meta-tbl tr td:first-child{border-left:none;}
.meta-tbl tr td:last-child{border-right:none;}
.meta-tbl tr:first-child td{border-top:none;}
.meta-tbl tr:last-child td{border-bottom:none;}

/* ══════════════════
   4. VENDOR DETAILS BOX
══════════════════ */
.buyer{
    border:2px solid var(--inv-border-green);
    border-radius:4px;
    margin-bottom:8px;
    overflow:hidden;
    background:#fff;
}
.buyer-hdr{
    background:var(--inv-bg-light);
    font-family:'Outfit', sans-serif;
    font-size:10.5px;font-weight:800;color:var(--inv-dark-green);
    text-transform:uppercase;letter-spacing:.5px;
    padding:4px 9px;
    border-bottom:1.5px solid var(--inv-border-green);
}
.buyer-body{padding:5px 9px 6px;}
.buyer-ms{
    font-size:11.5px;font-weight:700;color:#0f172a;
    margin-bottom:4px;padding-bottom:3px;
    border-bottom:1px dashed var(--inv-border-subtle);
}
.buyer-ms .ms-pre{color:#64748b;font-weight:600;font-size:10.5px;}
.buyer-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:2px 20px;
    font-size:10.5px;
}
.bg-r{display:flex;align-items:baseline;line-height:1.5;}
.bg-l{font-weight:600;color:#475569;min-width:65px;}
.bg-v{font-weight:600;color:#0f172a;}

/* ══════════════════
   5. SUBJECT / NOTE
══════════════════ */
.subj{
    font-size:10.5px;color:#0f172a;
    margin-bottom:6px;
    padding:4px 8px;
    border-left:3.5px solid var(--inv-border-green);
    background:var(--inv-bg-light);
    border-radius:0 4px 4px 0;
}
.subj strong{color:var(--inv-dark-green);font-family:'Outfit', sans-serif;font-weight:700;}

/* ══════════════════
   6. ITEMS TABLE
══════════════════ */
.itbl{
    width:100%;
    border-collapse:collapse;
    border:2px solid var(--inv-border-green);
}
.itbl th{
    background:var(--inv-primary-green) !important;
    color:#fff !important;
    font-family:'Outfit', sans-serif;
    font-size:10px;font-weight:700;
    text-align:center;
    padding:5px 4px;
    border:1px solid var(--inv-dark-green);
    line-height:1.2;
    letter-spacing:.3px;
    text-transform:uppercase;
    -webkit-print-color-adjust:exact;
    print-color-adjust:exact;
}
.itbl th.tl{text-align:left;padding-left:7px;}
.itbl td{
    border:1px solid var(--inv-border-subtle);
    padding:4.5px 5px;
    font-size:10px;
    vertical-align:middle;
}
.itbl tbody tr:nth-child(odd) td {background:var(--inv-bg-light);}
.itbl tbody tr:nth-child(even) td{background:#ffffff;}
.iname{font-family:'Plus Jakarta Sans', sans-serif;font-weight:700;font-size:10.5px;color:#0f172a;margin-bottom:1px;}
.icode{font-family:monospace;font-size:9.5px;color:#475569;font-weight:700;}
.tc{text-align:center;}
.tr{text-align:right;padding-right:6px !important;}
.sn {text-align:center;font-weight:700;font-size:10px;color:#475569;}

/* Grand Total Row */
.gt-row td{
    background:var(--inv-bg-highlight) !important;
    font-weight:700;font-size:11px;
    padding:4px 6px;
    border-top:2px solid var(--inv-border-green);
    border-bottom:2px solid var(--inv-border-green);
    border-left:1px solid var(--inv-border-subtle);
    border-right:1px solid var(--inv-border-subtle);
    -webkit-print-color-adjust:exact;
    print-color-adjust:exact;
}
.gt-lbl{font-family:'Outfit', sans-serif;text-align:center;color:var(--inv-dark-green);font-weight:800;letter-spacing:.5px;font-size:11px;text-transform:uppercase;}
.gt-val{font-family:'Plus Jakarta Sans', sans-serif;text-align:right;font-weight:800;font-size:12px;color:#0f172a;padding-right:5px;}

/* Amount in Words Table */
.awtbl{
    width:100%;
    border-collapse:collapse;
    border:2px solid var(--inv-border-green);
    border-top:none;
}
.awtbl td{
    border:1px solid var(--inv-border-subtle);
    border-top:0;
    padding:4px 8px;
    font-size:10px;
}
.aw-lbl{
    font-family:'Outfit', sans-serif;
    font-weight:800;
    background:var(--inv-primary-green);
    color:#fff;
    width:110px;white-space:nowrap;
    text-align:center;
    font-size:9.5px;text-transform:uppercase;
    letter-spacing:.4px;
    border:1px solid var(--inv-dark-green);
    -webkit-print-color-adjust:exact;
    print-color-adjust:exact;
}
.aw-val{
    font-family:'Plus Jakarta Sans', sans-serif;
    text-align:center;
    font-weight:700;
    color:var(--inv-dark-green);
    font-size:10.5px;
    letter-spacing:.3px;
}

/* ══════════════════
   7. BOTTOM SECTION
══════════════════ */
.bottom{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:14px;
    margin-top:8px;
}

/* Terms / Sign Area */
.terms-col{
    flex:1.2;
    display:flex;
    flex-direction:column;
    justify-content:space-between;
    min-height:140px;
}
.terms-box{
    font-size:9.5px;color:#334155;
}
.terms-box .t-ttl{
    font-family:'Outfit', sans-serif;
    font-weight:800;font-size:10.5px;color:var(--inv-dark-green);
    border-bottom:1.5px solid var(--inv-border-green);
    padding-bottom:2px;margin-bottom:4px;
    text-transform:uppercase;letter-spacing:.4px;
}
.terms-box ul{padding-left:14px;margin:0;}
.terms-box ul li{margin-bottom:2px;line-height:1.35;}

/* Signature Box */
.sig-area{
    margin-top:28px;
}
.sig-blk{text-align:left;width:200px;}
.sig-co{
    font-family:'Outfit', sans-serif;
    font-size:9px;color:var(--inv-dark-green);font-weight:700;
    margin-bottom:26px;text-transform:uppercase;letter-spacing:.3px;
}
.sig-line{
    border-top:1.5px solid #0f172a;
    padding-top:3px;
    font-family:'Outfit', sans-serif;
    font-size:9.5px;font-weight:800;
    text-transform:uppercase;letter-spacing:.3px;color:#0f172a;
}

/* Financial Summary Box */
.fin-box{
    flex:0 0 215px;
    border:2px solid var(--inv-border-green);
    border-radius:4px;
    overflow:hidden;
    background:#fff;
}
.fin-hdr{
    background:var(--inv-primary-green) !important;
    color:#fff !important;
    font-family:'Outfit', sans-serif;
    font-weight:800;font-size:10px;
    text-align:center;
    padding:4px;
    text-transform:uppercase;letter-spacing:.4px;
    -webkit-print-color-adjust:exact;
    print-color-adjust:exact;
}
.fin-tbl{width:100%;border-collapse:collapse;}
.fin-tbl tr td{
    padding:3px 7px;
    border-bottom:1px solid var(--inv-border-subtle);
    border-right:1px solid #e2e8f0;
    font-size:9.5px;
}
.fin-tbl tr:last-child td{border-bottom:none;}
.fin-tbl tr td:last-child{border-right:none;}
.fin-tbl .fl{color:#475569;font-weight:600;}
.fin-tbl .fv{font-weight:700;color:#0f172a;text-align:right;}
.fin-tbl .fv.red  {color:#c62828;}
.fin-tbl .fv.green{color:#15803d;}
.fin-tbl .fv.sub  {font-size:8.5px;color:#64748b;}
.fin-hl td{
    background:var(--inv-bg-highlight) !important;
    font-weight:800 !important;
    font-size:10.5px !important;
    border-top:1.5px solid var(--inv-border-green) !important;
    border-bottom:1.5px solid var(--inv-border-green) !important;
    -webkit-print-color-adjust:exact;
    print-color-adjust:exact;
}
.fin-hl .fv{font-size:11.5px !important;}
.fin-closing td{
    background:#fff !important;
    font-weight:800 !important;
    border-top:2px solid var(--inv-border-green) !important;
}
.fin-closing .fv{
    font-size:12px !important;
    color:#c62828;
}

/* ── PRINT MEDIA QUERY ── */
@media print{
    @page{size:A4 portrait;margin:4mm 8mm;}
    body{background:#fff !important;padding:0 !important;margin:0 !important;}
    .no-print{display:none !important;}
    .pg{width:100% !important;min-height:auto !important;margin:0 !important;padding:0 !important;box-shadow:none !important;border-radius:0 !important;}
    .itbl th,.fin-hdr,.aw-lbl{background-color:var(--inv-primary-green) !important;color:#fff !important;-webkit-print-color-adjust:exact !important;print-color-adjust:exact !important;}
    .gt-row td,.fin-hl td{background:var(--inv-bg-highlight) !important;-webkit-print-color-adjust:exact !important;print-color-adjust:exact !important;}
    .itbl tbody tr:nth-child(odd) td{background:var(--inv-bg-light) !important;-webkit-print-color-adjust:exact !important;print-color-adjust:exact !important;}
    .itbl td,.meta-tbl td,.buyer,.meta-wrap,.fin-box,.awtbl{border-color:var(--inv-border-green) !important;}
}
</style>
</head>
<body>

<!-- Floating Actions -->
<div class="no-print">
    <button onclick="window.print()">
        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
            <path d="M2.5 8a.5.5 0 1 0 0-1 .5.5 0 0 0 0 1z"/>
            <path d="M5 1a2 2 0 0 0-2 2v2H2a2 2 0 0 0-2 2v3a2 2 0 0 0 2 2h1v1a2 2 0 0 0 2 2h6a2 2 0 0 0 2-2v-1h1a2 2 0 0 0 2-2V7a2 2 0 0 0-2-2h-1V3a2 2 0 0 0-2-2H5zM4 3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1v2H4V3zm1 5a2 2 0 0 0-2 2v1H2a1 1 0 0 1-1-1V7a1 1 0 0 1 1-1h12a1 1 0 0 1 1 1v3a1 1 0 0 1-1 1h-1v-1a2 2 0 0 0-2-2H5zm7 2v3a1 1 0 0 1-1 1H5a1 1 0 0 1-1-1v-3a1 1 0 0 1 1-1h6a1 1 0 0 1 1 1z"/>
        </svg>
        Print Invoice
    </button>
    <a href="{{ route('Purchase.home') }}">
        <svg xmlns="http://www.w3.org/2000/svg" width="13" height="13" fill="currentColor" viewBox="0 0 16 16">
            <path fill-rule="evenodd" d="M15 8a.5.5 0 0 0-.5-.5H2.707l3.147-3.146a.5.5 0 1 0-.708-.708l-4 4a.5.5 0 0 0 0 .708l4 4a.5.5 0 0 0 .708-.708L2.707 8.5H14.5A.5.5 0 0 0 15 8z"/>
        </svg>
        Back to Purchases
    </a>
</div>

<!-- A4 Page -->
<div class="pg">

    <!-- 1. HEADER -->
    <div class="hdr">
        <div class="hdr-logo">
            @if(!empty($coLogo))
                <img src="{{ $coLogo }}" alt="{{ $coName }}">
            @else
                <div class="logo-txt">{{ $coName }}</div>
            @endif
        </div>
        <div class="hdr-info">
            <div class="co-name">{{ $coName }} - {{ date('Y') }}</div>
            <div class="co-line">
                <svg width="10" height="10" fill="#15803d" viewBox="0 0 16 16"><path d="M8 16s6-5.686 6-10A6 6 0 0 0 2 6c0 4.314 6 10 6 10zm0-7a3 3 0 1 1 0-6 3 3 0 0 1 0 6z"/></svg>
                <span>{{ $coAddr }}</span>
            </div>
            <div class="co-line">
                <svg width="10" height="10" fill="#15803d" viewBox="0 0 16 16"><path d="M3.654 1.328a.678.678 0 0 0-1.015-.063L1.605 2.3c-.483.484-.661 1.169-.45 1.77a17.568 17.568 0 0 0 4.168 6.608 17.569 17.569 0 0 0 6.608 4.168c.601.211 1.286.033 1.77-.45l1.034-1.034a.678.678 0 0 0-.063-1.015l-2.307-1.794a.678.678 0 0 0-.58-.122l-2.19.547a1.745 1.745 0 0 1-1.657-.459L5.482 8.062a1.745 1.745 0 0 1-.46-1.657l.548-2.19a.678.678 0 0 0-.122-.58L3.654 1.328zM1.884.511a1.745 1.745 0 0 1 2.612.163L6.29 2.98c.329.423.445.974.315 1.494l-.547 2.19a.678.678 0 0 0 .178.643l2.457 2.457a.678.678 0 0 0 .644.178l2.189-.547a1.745 1.745 0 0 1 1.494.315l2.306 1.794c.829.645.905 1.87.163 2.611l-1.034 1.034c-.74.74-1.846 1.065-2.877.702a18.634 18.634 0 0 1-7.01-4.42 18.634 18.634 0 0 1-4.42-7.009c-.362-1.03-.037-2.137.703-2.877L1.885.511z"/></svg>
                <strong>Mob:</strong> {{ $coPhone }}
                @if($coEmail)
                    <span style="color:#cbd5e1;">|</span>
                    <strong>Email:</strong> {{ $coEmail }}
                @endif
            </div>
            @if($coNtn || $coStrn)
                <div class="hdr-badge">
                    @if($coNtn)
                        <span class="badge-ntn-tag">NTN</span>
                        <span class="badge-val">{{ $coNtn }}</span>
                    @endif
                    @if($coNtn && $coStrn)
                        <span style="color:#cbd5e1;">|</span>
                    @endif
                    @if($coStrn)
                        <span class="badge-strn-tag">STRN</span>
                        <span class="badge-val">{{ $coStrn }}</span>
                    @endif
                </div>
            @endif
        </div>
    </div>

    <!-- 2. TITLE -->
    <div class="inv-title">
        <span>PURCHASE INVOICE</span>
    </div>

    <!-- 3. META TABLE -->
    <div class="meta-wrap">
        <table class="meta-tbl">
            <tr>
                <td class="ml">Invoice No</td>
                <td class="mv">
                    <strong style="color:var(--inv-dark-green); font-family:monospace; font-size:11px;">
                        INV-{{ $purchase->id }}
                    </strong>
                    @if($purchase->invoice_no && $purchase->invoice_no != $purchase->id)
                        <span style="color:#64748b; font-size:9.5px;">({{ $purchase->invoice_no }})</span>
                    @endif
                </td>
                <td class="ml">Invoice Date</td>
                <td class="mv">{{ \Carbon\Carbon::parse($purchase->purchase_date)->format('d/m/Y') }}</td>
            </tr>
            <tr>
                <td class="ml">Status / Type</td>
                <td class="mv">
                    <span style="display:inline-block; padding:1px 6px; background:var(--inv-bg-light); border:1px solid var(--inv-border-subtle); border-radius:3px; font-weight:700; color:var(--inv-primary-green); font-size:9.5px; text-transform:uppercase;">
                        {{ $purchase->status_purchase ?: 'Approved' }}
                    </span>
                </td>
                <td class="ml">Warehouse</td>
                <td class="mv">{{ $purchase->warehouse->warehouse_name ?? ($purchase->warehouse->name ?? 'Main Store') }}</td>
            </tr>
        </table>
    </div>

    <!-- 4. VENDOR DETAILS BOX -->
    <div class="buyer">
        <div class="buyer-hdr">Vendor Details</div>
        <div class="buyer-body">
            <div class="buyer-ms">
                <span class="ms-pre">M/s:</span> {{ $vendorName }}
            </div>
            <div class="buyer-grid">
                <div class="bg-r">
                    <span class="bg-l">Address:</span>
                    <span class="bg-v">{{ $vendorAddr ?: 'N/A' }}</span>
                </div>
                <div class="bg-r">
                    <span class="bg-l">Phone / Mob:</span>
                    <span class="bg-v">{{ $vendorPhone ?: 'N/A' }}</span>
                </div>
                @if($vendorEmail)
                    <div class="bg-r">
                        <span class="bg-l">Email:</span>
                        <span class="bg-v">{{ $vendorEmail }}</span>
                    </div>
                @endif
                @if($vendorCnic)
                    <div class="bg-r">
                        <span class="bg-l">NTN / CNIC:</span>
                        <span class="bg-v">{{ $vendorCnic }}</span>
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- 5. NOTE / REMARKS -->
    @if(!empty($purchase->note))
        <div class="subj">
            <strong>Note / Remarks:</strong> {{ $purchase->note }}
        </div>
    @endif

    <!-- 6. ITEMS TABLE -->
    <table class="itbl">
        <thead>
            <tr>
                <th style="width:4%;">#</th>
                <th style="width:13%;">Code</th>
                <th class="tl" style="width:33%;">Description / Item Name</th>
                <th style="width:13%;">Qty</th>
                <th style="width:9%;">UOM</th>
                <th style="width:12%;">Unit Price</th>
                <th style="width:7%;">Disc</th>
                <th style="width:13%;">Total (PKR)</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($purchase->items as $idx => $item)
                @php
                    $pCode = $item->product->item_code ?? 'ITEM-' . str_pad($item->product_id, 4, '0', STR_PAD_LEFT);
                    $pName = $item->product->item_name ?? 'Item';
                    $totalPieces = (int) $item->qty;

                    // Variant breakdown
                    $variantInfo = '';
                    if (!empty($item->color)) {
                        $decodedColor = base64_decode($item->color, true);
                        $vData = ($decodedColor !== false) ? json_decode($decodedColor, true) : null;
                        if (empty($vData)) {
                            $vData = json_decode($item->color, true);
                        }
                        if (!empty($vData)) {
                            $vColorName = $vData['color'] ?? '';
                            $vSizeName = $vData['size'] ?? '';
                            $vParts = [];
                            if ($vSizeName && $vSizeName !== '-') $vParts[] = $vSizeName;
                            if ($vColorName && $vColorName !== '-') $vParts[] = $vColorName;
                            if (!empty($vParts)) $variantInfo = ' ' . implode(' | ', $vParts);
                        } else {
                            $variantInfo = ' (' . $item->color . ')';
                        }
                    }

                    $unitStr = $item->unit ?? 'Pcs';
                @endphp
                <tr>
                    <td class="sn">{{ $idx + 1 }}</td>
                    <td class="tc icode">{{ $pCode }}</td>
                    <td style="padding-left:7px;">
                        <div class="iname">{{ $pName }}{{ $variantInfo }}</div>
                    </td>
                    <td class="tc" style="font-weight:700;">
                        {{ number_format($totalPieces) }} {{ $unitStr }}
                        @if($unitStr !== 'Pcs')
                            <div style="font-size:8.5px; color:#64748b; font-weight:normal;">({{ $totalPieces }} pcs)</div>
                        @endif
                    </td>
                    <td class="tc" style="font-weight:600; color:#334155;">{{ $unitStr }}</td>
                    <td class="tr" style="font-family:monospace; font-size:10.5px;">{{ number_format($item->price, 2) }}</td>
                    <td class="tc">
                        @if ($item->item_discount > 0)
                            @php
                                $grossLine = $item->line_total + $item->item_discount;
                                $discPercent = $grossLine > 0 ? ($item->item_discount / $grossLine) * 100 : 0;
                            @endphp
                            <span style="color:#c62828; font-weight:700; font-size:9px;">
                                {{ number_format($discPercent, 1) }}%<br>{{ number_format($item->item_discount, 2) }}
                            </span>
                        @else
                            <span style="color:#94a3b8;">-</span>
                        @endif
                    </td>
                    <td class="tr" style="font-family:monospace; font-weight:800; font-size:11px; color:#0f172a;">
                        {{ number_format($item->line_total, 2) }}
                    </td>
                </tr>
            @endforeach

            <!-- Grand Total Summary Row -->
            <tr class="gt-row">
                <td colspan="3" class="gt-lbl">Total Summary</td>
                <td class="tc" style="font-weight:800; font-size:11px; color:var(--inv-dark-green);">
                    {{ number_format($totalQty) }} Pcs
                </td>
                <td colspan="3" class="tr" style="font-weight:700; color:#475569; font-size:10px;">
                    Gross Net Total:
                </td>
                <td class="gt-val">
                    {{ number_format($subTotal, 2) }}
                </td>
            </tr>
        </tbody>
    </table>

    <!-- 7. AMOUNT IN WORDS -->
    <table class="awtbl">
        <tr>
            <td class="aw-lbl">Amount in Words</td>
            <td class="aw-val">{{ $amtWords }}</td>
        </tr>
    </table>

    <!-- 8. BOTTOM AREA: Terms & Signature (Left) + Financial Summary (Right) -->
    <div class="bottom">
        <div class="terms-col">
            <div class="terms-box">
                <div class="t-ttl">Terms & Conditions</div>
                <ul>
                    <li>Goods received in sound condition and verified against approved purchase order.</li>
                    <li>Payments are subject to standard verification and ledger reconciliation.</li>
                    <li>Any discrepancy must be reported within 3 days of goods receipt.</li>
                </ul>
            </div>

            <!-- Signature -->
            <div class="sig-area">
                <div class="sig-blk">
                    <div class="sig-co">For {{ $coName }}</div>
                    <div class="sig-line">Authorized Signature</div>
                </div>
            </div>
        </div>

        <!-- Financial Summary Box -->
        <div class="fin-box">
            <div class="fin-hdr">Financial Summary</div>
            <table class="fin-tbl">
                <tr>
                    <td class="fl">Subtotal</td>
                    <td class="fv">Rs. {{ number_format($subTotal, 2) }}</td>
                </tr>
                @if ($addDisc > 0)
                    <tr>
                        <td class="fl">Additional Discount</td>
                        <td class="fv red">
                            @php
                                $bDiscPct = $subTotal > 0 ? ($addDisc / $subTotal) * 100 : 0;
                            @endphp
                            <span class="sub">({{ number_format($bDiscPct, 1) }}%)</span>
                            -Rs. {{ number_format($addDisc, 2) }}
                        </td>
                    </tr>
                @endif
                @if ($extraCost > 0)
                    <tr>
                        <td class="fl">Extra Cost / Freight</td>
                        <td class="fv">Rs. {{ number_format($extraCost, 2) }}</td>
                    </tr>
                @endif
                <tr class="fin-hl">
                    <td class="fl" style="color:var(--inv-dark-green);">Total Net</td>
                    <td class="fv" style="color:var(--inv-dark-green);">Rs. {{ number_format($netPayable, 2) }}</td>
                </tr>
                <tr>
                    <td class="fl">Paid Amount</td>
                    <td class="fv green">Rs. {{ number_format($paidAmount, 2) }}</td>
                </tr>
                <tr>
                    <td class="fl">Bill Due</td>
                    <td class="fv {{ $billDue > 0 ? 'red' : 'green' }}">
                        Rs. {{ number_format($billDue, 2) }}
                    </td>
                </tr>
                <tr>
                    <td class="fl">Previous Balance</td>
                    <td class="fv">Rs. {{ number_format($prevBal, 2) }}</td>
                </tr>
                <tr class="fin-closing">
                    <td class="fl" style="color:#c62828;">Total Closing Balance</td>
                    <td class="fv">
                        Rs. {{ number_format($closeBal, 2) }}
                    </td>
                </tr>
            </table>
        </div>
    </div>

</div>

</body>
</html>

