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

    /* ── Customer ── */
    $customer  = $return->customer;
    $sale      = $return->sale;
    $custName  = $customer->customer_name ?? ($sale->walkin_name ?? 'Walk-in Customer');
    $custNtn   = $customer->cnic         ?? ($customer->ntn ?? '');
    $custStrn  = $customer->strn         ?? '';
    $custAddr  = $customer->address      ?? '';
    $custMob   = $customer->mobile       ?? ($customer->mobile_2 ?? '');
    $custEmail = $customer->email_address ?? '';
    $custAttn  = $customer->contact_person ?? '';
    $custType  = $customer->customer_type  ?? '';
    $isWalkin  = empty($return->customer_id) || empty($customer);

    /* ── Financial Breakdown ── */
    $grossBill     = (float) $return->bill_amount;
    $itemDisc      = (float) $return->item_discount;
    $extraDisc     = (float) $return->extra_discount;
    $netReturnVal  = (float) $return->net_amount;
    $dueAdjusted   = (float) ($return->due_adjusted ?? 0);
    $refundPaid    = (float) ($return->paid ?? 0);
    $storeCredit   = (float) ($return->balance ?? 0);
    $amtWords      = numberToWordsInvoice($netReturnVal);

    /* ── Original Sale Metrics ── */
    $saleNet       = $sale ? (float) $sale->total_net : 0;
    $salePaid      = $sale ? (float) ($sale->cash + ($sale->card ?? 0)) : 0;
    $saleOrigDue   = max(0, $saleNet - $salePaid);
    $isFullReturn  = $sale && ($netReturnVal >= ($saleNet - 0.001));

    /* ── Customer Ledger Balance ── */
    $prevLedgerBal = $customer ? (float)($customer->previous_balance ?? 0) : 0;
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Return Invoice {{ $return->return_invoice }}</title>
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
    background:#cdd5df;
    padding:10px 0;
    -webkit-font-smoothing:antialiased;
    -moz-osx-font-smoothing:grayscale;
    -webkit-print-color-adjust:exact;
    print-color-adjust:exact;
}

/* ── Top Action Buttons (No-Print) ── */
.no-print{
    position:fixed;top:12px;right:20px;z-index:9999;
    display:flex;gap:8px;
}
.no-print button,.no-print a{
    padding:6px 16px;font-size:12px;font-weight:700;
    border-radius:5px;cursor:pointer;border:none;
    text-decoration:none;display:inline-flex;align-items:center;gap:5px;
    font-family:'Plus Jakarta Sans', sans-serif;
}
.no-print button{background:#dc2626;color:#fff;box-shadow:0 3px 8px rgba(220,38,38,.35);}
.no-print button:hover{background:#b91c1c;}
.no-print a.btn-back{background:#64748b;color:#fff;}
.no-print a.btn-back:hover{background:#475569;}

/* ── A4 Sheet ── */
.pg{
    width:210mm;
    min-height:297mm;
    margin:0 auto;
    background:#fff;
    padding:5mm 10mm 10mm;
    box-shadow:0 8px 32px rgba(0,0,0,.2);
    position:relative;
}

/* ══════════════════
   1. HEADER
══════════════════ */
.hdr{
    display:flex;
    align-items:center;
    justify-content:space-between;
    padding-bottom:6px;
    border-bottom:2px solid #dc2626;
    margin-bottom:6px;
}
.hdr-logo{flex:0 0 auto;}
.hdr-logo img{max-width:210px;max-height:65px;object-fit:contain;display:block;}
.logo-txt{
    font-family:'Outfit', sans-serif;
    font-size:24px;font-weight:800;color:#1e3a8a;
    text-transform:uppercase;letter-spacing:0.8px;line-height:1;
    border-left:4px solid #dc2626;
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
    color:#1e3a8a;
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
.hdr-info .co-line strong{
    color:#0f172a;
    font-weight:700;
}
.hdr-badge{
    display:inline-flex;
    align-items:center;
    gap:6px;
    background:#f1f5f9;
    border:1px solid #cbd5e1;
    padding:2px 8px;
    border-radius:4px;
    font-size:10.5px;
    margin-top:2px;
}
.badge-ntn-tag{
    background:#1e3a8a;
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
.badge-divider{
    color:#94a3b8;
    font-weight:300;
    margin:0 2px;
}

/* ══════════════════
   2. RED RETURN TITLE
══════════════════ */
.inv-title{
    text-align:center;
    padding:6px 0 10px;
    margin-bottom:8px;
}
.inv-title span{
    font-family:'Outfit', sans-serif;
    font-size:22px;
    font-weight:800;
    color:#dc2626 !important;
    letter-spacing:7px;
    text-transform:uppercase;
    padding-bottom:3px;
    border-bottom:2.5px solid #dc2626 !important;
    display:inline-block;
}

/* ══════════════════
   3. META TABLE (4-Column Full Width)
══════════════════ */
.meta-wrap{
    width:100%;
    margin-bottom:8px;
    border:2px solid #dc2626;
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
    border:1px solid #cbd5e1;
    padding:4px 8px;
    font-size:10px;
}
.meta-tbl .ml{
    font-family:'Outfit', sans-serif;
    font-weight:700;
    color:#991b1b;
    background:#fef2f2;
    white-space:nowrap;
    width:16%;
    border:1px solid #cbd5e1;
    letter-spacing:0.2px;
}
.meta-tbl .mv{
    font-weight:600;
    color:#0f172a;
    width:34%;
    border:1px solid #cbd5e1;
    font-size:10.5px;
}
.meta-tbl tr td:first-child{border-left:none;}
.meta-tbl tr td:last-child{border-right:none;}
.meta-tbl tr:first-child td{border-top:none;}
.meta-tbl tr:last-child td{border-bottom:none;}

/* ══════════════════
   4. BUYER (CUSTOMER)
══════════════════ */
.buyer{
    border:2px solid #dc2626;
    border-radius:4px;
    margin-bottom:8px;
    overflow:hidden;
    background:#fff;
}
.buyer-hdr{
    background:#fef2f2;
    font-family:'Outfit', sans-serif;
    font-size:10.5px;font-weight:800;color:#991b1b;
    text-transform:uppercase;letter-spacing:.5px;
    padding:4px 9px;
    border-bottom:1.5px solid #dc2626;
}
.buyer-body{padding:5px 9px 6px;}
.buyer-ms{
    font-size:11.5px;font-weight:700;color:#0f172a;
    margin-bottom:4px;padding-bottom:3px;
    border-bottom:1px dashed #cbd5e1;
}
.buyer-ms .ms-pre{color:#64748b;font-weight:600;font-size:10.5px;}
.buyer-grid{
    display:grid;
    grid-template-columns:1fr 1fr;
    gap:2px 20px;
    font-size:10.5px;
}
.bg-r{display:flex;align-items:baseline;line-height:1.5;}
.bg-l{font-weight:600;color:#475569;min-width:75px;}
.bg-v{font-weight:600;color:#0f172a;}

/* ══════════════════
   5. RETURN REASON / NOTE
══════════════════ */
.subj{
    font-size:10.5px;color:#0f172a;
    margin-bottom:6px;
    padding:4px 8px;
    border-left:3px solid #dc2626;
    background:#fef2f2;
}
.subj strong{color:#991b1b;font-family:'Outfit', sans-serif;font-weight:700;}

/* ══════════════════
   6. RETURNED ITEMS TABLE
══════════════════ */
.itbl{
    width:100%;
    border-collapse:collapse;
    border:2px solid #dc2626;
}
.itbl th{
    background:#b91c1c !important;
    color:#fff !important;
    font-family:'Outfit', sans-serif;
    font-size:10px;font-weight:700;
    text-align:center;
    padding:5px 3px;
    border:1px solid #991b1b;
    line-height:1.2;
    letter-spacing:.3px;
    text-transform:uppercase;
    -webkit-print-color-adjust:exact;
    print-color-adjust:exact;
}
.itbl th.tl{text-align:left;padding-left:7px;}
.itbl td{
    border:1px solid #cbd5e1;
    padding:4.5px 5px;
    font-size:10px;
    vertical-align:top;
}
.itbl tbody tr:nth-child(odd) td {background:#fffafb;}
.itbl tbody tr:nth-child(even) td{background:#fff;}
.iname{font-family:'Plus Jakarta Sans', sans-serif;font-weight:700;font-size:10.5px;color:#0f172a;margin-bottom:2px;}
.ispec-lbl{font-family:'Outfit', sans-serif;font-size:9px;font-weight:700;color:#991b1b;margin:2px 0 1px;text-transform:uppercase;letter-spacing:.3px;}
.ispec-ul{
    list-style:none;
    padding:0;margin:0;
    font-size:9px;color:#334155;line-height:1.4;
}
.ispec-ul li{padding-left:8px;position:relative;}
.ispec-ul li::before{content:"–";position:absolute;left:0;color:#dc2626;}
.tc{text-align:center;}
.tr{text-align:right;padding-right:6px !important;}
.sn {text-align:center;font-weight:700;font-size:10px;color:#475569;}

/* Grand Total Row */
.gt-row td{
    background:#fef2f2 !important;
    font-weight:700;font-size:11px;
    padding:4px 6px;
    border-top:2px solid #dc2626;
    border-bottom:2px solid #dc2626;
    border-left:1px solid #cbd5e1;
    border-right:1px solid #cbd5e1;
    -webkit-print-color-adjust:exact;
    print-color-adjust:exact;
}
.gt-lbl{font-family:'Outfit', sans-serif;text-align:center;color:#991b1b;font-weight:800;letter-spacing:.5px;font-size:11px;text-transform:uppercase;}
.gt-val{font-family:'Plus Jakarta Sans', sans-serif;text-align:right;font-weight:800;font-size:12px;color:#991b1b;padding-right:5px;}

/* Amount in Words */
.awtbl{
    width:100%;
    border-collapse:collapse;
    border:2px solid #dc2626;
    border-top:none;
}
.awtbl td{
    border:1px solid #cbd5e1;
    border-top:0;
    padding:4px 8px;
    font-size:10px;
}
.aw-lbl{
    font-family:'Outfit', sans-serif;
    font-weight:800;
    background:#b91c1c;
    color:#fff;
    width:110px;white-space:nowrap;
    text-align:center;
    font-size:9.5px;text-transform:uppercase;
    letter-spacing:.4px;
    border:1px solid #991b1b;
    -webkit-print-color-adjust:exact;
    print-color-adjust:exact;
}
.aw-val{font-family:'Plus Jakarta Sans', sans-serif;text-align:center;font-weight:700;color:#991b1b;font-size:10.5px;letter-spacing:.3px;}

/* ══════════════════
   7. BOTTOM SECTION
══════════════════ */
.bottom{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:12px;
    margin-top:8px;
}
.terms{flex:1.2;font-size:9.5px;color:#334155;}
.terms .t-ttl{
    font-family:'Outfit', sans-serif;
    font-weight:800;font-size:10.5px;color:#991b1b;
    border-bottom:1.5px solid #dc2626;
    padding-bottom:2px;margin-bottom:4px;
    text-transform:uppercase;letter-spacing:.4px;
}
.terms ol{padding-left:12px;margin:0;}
.terms ol li{margin-bottom:1.5px;line-height:1.35;}
.ledger-box{
    margin-top:6px;
    border:1.5px solid #cbd5e1;
    padding:5px 8px;background:#f8fafc;border-radius:4px;
    font-size:9.5px;
}
.ledger-box .lb-row{display:flex;justify-content:space-between;padding:1.5px 0;}
.ledger-box .lb-lbl{color:#64748b;}
.ledger-box .lb-val{font-weight:700;color:#0f172a;}

/* Financial Summary Box */
.fin-box{
    flex:0 0 215px;
    border:2px solid #dc2626;
    border-radius:4px;
    overflow:hidden;
}
.fin-hdr{
    background:#b91c1c !important;
    color:#fff !important;
    font-family:'Outfit', sans-serif;
    font-weight:800;font-size:10.5px;
    text-align:center;
    padding:4px;
    text-transform:uppercase;letter-spacing:.4px;
    -webkit-print-color-adjust:exact;
    print-color-adjust:exact;
}
.fin-tbl{width:100%;border-collapse:collapse;}
.fin-tbl tr td{
    padding:3px 8px;
    border-bottom:1px solid #cbd5e1;
    border-right:1px solid #e2e8f0;
    font-size:9.5px;
}
.fin-tbl tr:last-child td{border-bottom:none;}
.fin-tbl tr td:last-child{border-right:none;}
.fin-tbl .fl{color:#475569;font-weight:600;}
.fin-tbl .fv{font-weight:700;color:#0f172a;text-align:right;}
.fin-tbl .fv.red  {color:#dc2626;}
.fin-tbl .fv.green{color:#059669;}
.fin-hl td{
    background:#fef2f2 !important;
    font-weight:800 !important;
    font-size:10.5px !important;
    border-top:1.5px solid #dc2626 !important;
    border-bottom:1.5px solid #dc2626 !important;
    -webkit-print-color-adjust:exact;
    print-color-adjust:exact;
}
.fin-hl .fv{font-size:11.5px !important;color:#991b1b !important;}

/* Signature */
.sig-area{display:flex;justify-content:space-between;margin-top:20px;}
.sig-blk{text-align:center;width:180px;}
.sig-co{
    font-family:'Outfit', sans-serif;
    font-size:9.5px;color:#1e3a8a;font-weight:700;
    margin-bottom:26px;text-transform:uppercase;letter-spacing:.3px;
}
.sig-line{
    border-top:1.5px solid #0f172a;
    padding-top:3px;
    font-family:'Outfit', sans-serif;
    font-size:9.5px;font-weight:800;
    text-transform:uppercase;letter-spacing:.3px;color:#0f172a;
}

/* ── PRINT MEDIA RULES ── */
@media print{
    @page{size:A4 portrait;margin:4mm 8mm;}
    body{background:#fff !important;padding:0 !important;margin:0 !important;}
    .no-print{display:none !important;}
    .pg{width:100% !important;min-height:auto !important;margin:0 !important;padding:0 !important;box-shadow:none !important;}
    .itbl th,.fin-hdr,.aw-lbl{background-color:#b91c1c !important;color:#fff !important;-webkit-print-color-adjust:exact !important;print-color-adjust:exact !important;}
    .gt-row td,.fin-hl td{background:#fef2f2 !important;-webkit-print-color-adjust:exact !important;print-color-adjust:exact !important;}
    .itbl tbody tr:nth-child(odd) td{background:#fffafb !important;-webkit-print-color-adjust:exact !important;print-color-adjust:exact !important;}
    .itbl td,.meta-tbl td,.buyer{border-color:#cbd5e1 !important;}
}
</style>
</head>
<body>

{{-- Top Action Buttons --}}
<div class="no-print">
    <button onclick="window.print()">
        <svg width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><polyline points="6 9 6 2 18 2 18 9"></polyline><path d="M6 18H4a2 2 0 0 1-2-2v-5a2 2 0 0 1 2-2h16a2 2 0 0 1 2 2v5a2 2 0 0 1-2 2h-2"></path><rect x="6" y="14" width="12" height="8"></rect></svg>
        Print Return Invoice
    </button>
    <a href="{{ route('sale.return.index') }}" class="btn-back">
        &#8592; Back to Returns
    </a>
</div>

<div class="pg">

    {{-- ═══ 1. COMPANY HEADER ═══ --}}
    <div class="hdr">
        <div class="hdr-logo">
            @if(!empty($coLogo))
                <img src="{{ $coLogo }}" alt="{{ $coName }}">
            @else
                <div class="logo-txt">{{ $coName }}</div>
            @endif
        </div>
        <div class="hdr-info">
            <div class="co-name">{{ $coName }}</div>
            @if(!empty($coAddr))
                <div class="co-line">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    <span>{{ $coAddr }}</span>
                </div>
            @endif
            @if($coEmail)
                <div class="co-line">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    <span>{{ $coEmail }}</span>
                </div>
            @endif
            @if($coPhone)
                <div class="co-line">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
                    <span>{{ $coPhone }}</span>
                </div>
            @endif
            @if($coNtn)
                <div class="hdr-badge">
                    <span class="badge-ntn-tag">NTN</span>
                    <span class="badge-val">{{ $coNtn }}</span>
                    @if($coStrn)
                        <span class="badge-divider">|</span>
                        <span class="badge-strn-tag">STRN</span>
                        <span class="badge-val">{{ $coStrn }}</span>
                    @endif
                </div>
            @endif
        </div>
    </div>

    {{-- ═══ 2. RED RETURN INVOICE TITLE ═══ --}}
    <div class="inv-title">
        <span>RETURN INVOICE</span>
    </div>

    {{-- ═══ 3. META TABLE (Balanced 4-Column Full Width) ═══ --}}
    <div class="meta-wrap">
        <table class="meta-tbl">
            <tr>
                <td class="ml">Return Invoice No.</td>
                <td class="mv font-monospace" style="color:#dc2626; font-weight:800;">{{ $return->return_invoice }}</td>
                <td class="ml">Original Sale No.</td>
                <td class="mv font-monospace fw-bold">{{ $sale ? $sale->invoice_no : 'N/A' }}</td>
            </tr>
            <tr>
                <td class="ml">Return Date</td>
                <td class="mv">{{ \Carbon\Carbon::parse($return->return_date)->format('d-M-Y') }}</td>
                <td class="ml">Original Sale Date</td>
                <td class="mv">{{ $sale && $sale->created_at ? $sale->created_at->format('d-M-Y') : '—' }}</td>
            </tr>
            <tr>
                <td class="ml">Return Status</td>
                <td class="mv">
                    @if($isFullReturn)
                        <span style="display:inline-block;padding:1px 8px;border-radius:3px;background:#fef2f2;color:#991b1b;border:1px solid #fecaca;font-size:9.5px;font-weight:700;">Full Return</span>
                    @else
                        <span style="display:inline-block;padding:1px 8px;border-radius:3px;background:#fffbeb;color:#b45309;border:1px solid #fde68a;font-size:9.5px;font-weight:700;">Partial Return</span>
                    @endif
                </td>
                <td class="ml">Warehouse</td>
                <td class="mv">{{ $return->warehouse->warehouse_name ?? 'Main Store' }}</td>
            </tr>
            <tr>
                <td class="ml">Settlement Status</td>
                <td class="mv">
                    @if((float)$return->balance <= 0.01)
                        <span style="display:inline-block;padding:1px 8px;border-radius:3px;background:#ecfdf5;color:#047857;border:1px solid #a7f3d0;font-size:9.5px;font-weight:700;">Fully Settled (0.00)</span>
                    @else
                        <span style="display:inline-block;padding:1px 8px;border-radius:3px;background:#eff6ff;color:#1e40af;border:1px solid #bfdbfe;font-size:9.5px;font-weight:700;">Store Credit: {{ $currency }} {{ number_format($return->balance, 2) }}</span>
                    @endif
                </td>
                <td class="ml">Original Bill Total</td>
                <td class="mv font-monospace fw-bold">{{ $sale ? $currency.' '.number_format($saleNet, 2) : '—' }}</td>
            </tr>
        </table>
    </div>

    {{-- ═══ 4. BUYER (CUSTOMER DETAILS) ═══ --}}
    <div class="buyer">
        <div class="buyer-hdr">CUSTOMER DETAILS (Returned By)</div>
        <div class="buyer-body">
            <div class="buyer-ms">
                <span class="ms-pre">M/S &nbsp;</span>
                {{ $custName }}
                @if($custType)<small style="color:#777;font-weight:400;">&nbsp;({{ ucfirst($custType) }})</small>@endif
                @if($isWalkin)<span style="display:inline-block;padding:0 5px;border-radius:3px;background:#f1f5f9;color:#64748b;font-size:9px;font-weight:600;margin-left:5px;">Walk-in Customer</span>@endif
            </div>
            <div class="buyer-grid">
                <div class="bg-r"><span class="bg-l">NTN / CNIC:</span> <span class="bg-v">{{ $custNtn ?: '—' }}</span></div>
                <div class="bg-r"><span class="bg-l">STRN No:</span>   <span class="bg-v">{{ $custStrn ?: '—' }}</span></div>
                <div class="bg-r"><span class="bg-l">Address:</span>   <span class="bg-v">{{ $custAddr ?: '—' }}</span></div>
                <div class="bg-r"><span class="bg-l">Mobile:</span>    <span class="bg-v">{{ $custMob ?: '—' }}</span></div>
                @if($custEmail)<div class="bg-r"><span class="bg-l">Email:</span> <span class="bg-v">{{ $custEmail }}</span></div>@endif
                @if($custAttn) <div class="bg-r"><span class="bg-l">Attn:</span>  <span class="bg-v">{{ $custAttn }}</span></div>@endif
            </div>
        </div>
    </div>

    {{-- ═══ 5. REMARKS / REASON NOTE ═══ --}}
    @if(!empty($return->remarks))
    <div class="subj">
        <strong>Return Remarks:</strong>&nbsp; {{ $return->remarks }}
    </div>
    @endif

    {{-- ═══ 6. RETURNED ITEMS TABLE ═══ --}}
    <table class="itbl">
        <thead>
            <tr>
                <th style="width:4%">S/N</th>
                <th class="tl" style="width:38%">Returned Product / Item Details</th>
                <th style="width:8%">Boxes</th>
                <th style="width:7%">Loose</th>
                <th style="width:9%">Total Pcs</th>
                <th style="width:11%">Unit Rate</th>
                <th style="width:11%">Gross Total</th>
                <th style="width:12%">Line Disc.</th>
                <th style="width:15%">Return Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($return->items as $item)
            @php
                $qtyPcs = (float)($item->qty ?? 0);
                $unitRate = (float)($item->price ?? 0);
                $itemDiscAmount = (float)($item->item_discount ?? 0);
                $lineGross = ($qtyPcs * $unitRate);
                $lineNet = (float)($item->line_total ?? max(0, $lineGross - $itemDiscAmount));

                // Parse variant specifications
                $variant = [];
                if (!empty($item->color)) {
                    $b64Decoded = base64_decode($item->color, true);
                    if ($b64Decoded !== false) {
                        $json = json_decode($b64Decoded, true);
                        if (is_array($json)) $variant = $json;
                    }
                    if (empty($variant)) {
                        $json = json_decode($item->color, true);
                        if (is_array($json)) $variant = $json;
                    }
                }
                $specs = [];
                $prodCode = $item->product->item_code ?? ($item->product->product_code ?? '');
                if (!empty($prodCode)) $specs[] = ['Item Code', $prodCode];
                if (!empty($variant['name'])) $specs[] = ['Variant', $variant['name']];
                if (!empty($variant['size']) && $variant['size'] !== '-') $specs[] = ['Size', $variant['size']];
                if (!empty($variant['color']) && $variant['color'] !== '-') $specs[] = ['Color', $variant['color']];
            @endphp
            <tr>
                <td class="sn">{{ $loop->iteration }}</td>
                <td>
                    <div class="iname">{{ $item->product->item_name ?? ($item->product->product_name ?? 'Returned Item') }}</div>
                    @if(count($specs) > 0)
                    <div class="ispec-lbl">Specifications</div>
                    <ul class="ispec-ul">
                        @foreach($specs as [$l,$v])
                            <li><strong>{{ $l }}:</strong> {{ $v }}</li>
                        @endforeach
                    </ul>
                    @endif
                </td>
                <td class="tc font-monospace">{{ number_format($item->boxes ?? 0, 0) }}</td>
                <td class="tc font-monospace">{{ number_format($item->loose_pieces ?? 0, 0) }}</td>
                <td class="tc font-monospace" style="font-weight:700;">{{ number_format($qtyPcs, 0) }}</td>
                <td class="tr font-monospace">{{ number_format($unitRate, 2) }}</td>
                <td class="tr font-monospace">{{ number_format($lineGross, 2) }}</td>
                <td class="tr font-monospace" style="color: {{ $itemDiscAmount > 0 ? '#dc2626' : '#64748b' }};">
                    {{ $itemDiscAmount > 0 ? '-'.number_format($itemDiscAmount, 2) : '0.00' }}
                </td>
                <td class="tr font-monospace" style="font-weight:700; color:#0f172a;">
                    {{ number_format($lineNet, 2) }}
                </td>
            </tr>
            @endforeach

            {{-- Grand Total Row --}}
            <tr class="gt-row">
                <td colspan="4" class="gt-lbl">Grand Return Total &nbsp;&nbsp; ({{ $currency }})</td>
                <td class="tc font-monospace" style="font-weight:800; color:#991b1b;">{{ number_format($return->items->sum('qty'), 0) }}</td>
                <td colspan="3" class="tc" style="font-weight:700; color:#991b1b;">*****</td>
                <td class="gt-val">{{ number_format($netReturnVal, 2) }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Amount in Words --}}
    <table class="awtbl">
        <tr>
            <td class="aw-lbl">Amount<br>in words</td>
            <td class="aw-val">*** {{ $amtWords }} ***</td>
        </tr>
    </table>

    {{-- ═══ 7. BOTTOM: Return Terms & Settlement | Financial Summary ═══ --}}
    <div class="bottom">

        {{-- Terms & Accounting Breakdown Left --}}
        <div class="terms">
            <div class="t-ttl">Return &amp; Refund Policy</div>
            <ol>
                <li>Returned items have been inspected and verified against the original invoice.</li>
                <li>Invoice debt adjustment directly credits the customer's outstanding balance.</li>
                <li>Disbursed cash refund represents eligible advance payment returns.</li>
                <li>Unclaimed refund balance remains securely credited to customer's ledger account.</li>
            </ol>

            {{-- Settlement Accounting Ledger Box --}}
            <div class="ledger-box">
                <div style="font-weight:800;color:#991b1b;font-size:9.5px;margin-bottom:3px;text-transform:uppercase;display:flex;align-items:center;gap:4px;">
                    <span>Settlement &amp; Ledger Allocation</span>
                </div>
                @if($sale)
                <div class="lb-row"><span class="lb-lbl">Original Invoice Total:</span><span class="lb-val font-monospace">{{ $currency }} {{ number_format($saleNet, 2) }}</span></div>
                <div class="lb-row"><span class="lb-lbl">Customer Advance Paid:</span><span class="lb-val font-monospace" style="color:#059669;">{{ $currency }} {{ number_format($salePaid, 2) }}</span></div>
                @endif
                <div class="lb-row"><span class="lb-lbl">Adjusted Against Unpaid Bill:</span><span class="lb-val font-monospace" style="color:#dc2626;">-{{ $currency }} {{ number_format($dueAdjusted, 2) }}</span></div>
                <div class="lb-row"><span class="lb-lbl">Cash Refund Disbursed:</span><span class="lb-val font-monospace" style="color:#059669;">{{ $currency }} {{ number_format($refundPaid, 2) }}</span></div>
                @if($storeCredit > 0.01)
                <div class="lb-row"><span class="lb-lbl">Remaining Store Credit:</span><span class="lb-val font-monospace" style="color:#2563eb;">{{ $currency }} {{ number_format($storeCredit, 2) }}</span></div>
                @endif
                @if(!$isWalkin && $customer)
                <div class="lb-row" style="border-top:1px dashed #cbd5e1; margin-top:2px; padding-top:2px;">
                    <span class="lb-lbl">Customer Current Balance:</span>
                    <span class="lb-val font-monospace" style="color: {{ $prevLedgerBal > 0 ? '#dc2626' : '#059669' }};">
                        {{ number_format(abs($prevLedgerBal), 2) }} {{ $prevLedgerBal >= 0 ? 'Dr (Receivable)' : 'Cr (Advance)' }}
                    </span>
                </div>
                @endif
            </div>
        </div>

        {{-- Financial Summary Right Box --}}
        <div class="fin-box">
            <div class="fin-hdr">Financial Summary</div>
            <table class="fin-tbl">
                <tr>
                    <td class="fl">Gross Goods:</td>
                    <td class="fv font-monospace">{{ number_format($grossBill, 2) }}</td>
                </tr>
                @if($itemDisc > 0.001)
                <tr>
                    <td class="fl">Line Discount:</td>
                    <td class="fv red font-monospace">-{{ number_format($itemDisc, 2) }}</td>
                </tr>
                @endif
                @if($extraDisc > 0.001)
                <tr>
                    <td class="fl">Extra Discount:</td>
                    <td class="fv red font-monospace">-{{ number_format($extraDisc, 2) }}</td>
                </tr>
                @endif

                {{-- Net Return Value Highlight --}}
                <tr class="fin-hl">
                    <td class="fl" style="font-weight:800; color:#991b1b;">Return Goods Value:</td>
                    <td class="fv font-monospace">{{ number_format($netReturnVal, 2) }}</td>
                </tr>

                @if($dueAdjusted > 0.001)
                <tr>
                    <td class="fl" style="font-size:9px;">Due Settled:</td>
                    <td class="fv red font-monospace">-{{ number_format($dueAdjusted, 2) }}</td>
                </tr>
                @endif

                <tr>
                    <td class="fl" style="font-weight:700;">Cash Refund Paid:</td>
                    <td class="fv green font-monospace">{{ number_format($refundPaid, 2) }}</td>
                </tr>

                <tr>
                    <td class="fl">Return Balance:</td>
                    <td class="fv font-monospace" style="color: {{ $storeCredit > 0.01 ? '#dc2626' : '#059669' }};">
                        @if($storeCredit <= 0.01)
                            <span style="font-size:9px; font-weight:800; color:#059669;">0.00 (Settled)</span>
                        @else
                            {{ number_format($storeCredit, 2) }}
                        @endif
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- ═══ 8. SIGNATURES ═══ --}}
    <div class="sig-area">
        <div class="sig-blk">
            <div class="sig-co">Customer Acknowledgment</div>
            <div class="sig-line">Customer Signature</div>
        </div>
        <div class="sig-blk">
            <div class="sig-co">For {{ $coName }}</div>
            <div class="sig-line">Authorized Signature</div>
        </div>
    </div>

</div>{{-- end pg --}}
</body>
</html>
