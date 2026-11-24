@php
    if (!function_exists('numberToWordsQuotation')) {
        function numberToWordsQuotation($number) {
            $d = [0=>'Zero',1=>'One',2=>'Two',3=>'Three',4=>'Four',5=>'Five',6=>'Six',7=>'Seven',8=>'Eight',9=>'Nine',10=>'Ten',11=>'Eleven',12=>'Twelve',13=>'Thirteen',14=>'Fourteen',15=>'Fifteen',16=>'Sixteen',17=>'Seventeen',18=>'Eighteen',19=>'Nineteen',20=>'Twenty',30=>'Thirty',40=>'Forty',50=>'Fifty',60=>'Sixty',70=>'Seventy',80=>'Eighty',90=>'Ninety',100=>'Hundred',1000=>'Thousand',1000000=>'Million',1000000000=>'Billion'];
            if (!is_numeric($number)) return '';
            $v = (float)$number;
            if ($v < 0) return 'Negative '.numberToWordsQuotation(abs($v));
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

    /* ── Company Settings ── */
    $coName    = \App\Models\Setting::get('company_name', 'LOGIC TECH ENGINEERING');
    $coAddr    = \App\Models\Setting::get('company_address') ?: \App\Models\Setting::get('web_contact_address', '01-km Sharaqpur Road Sheikhupura, Punjab, Pakistan');
    $coEmail   = \App\Models\Setting::get('company_email') ?: \App\Models\Setting::get('web_contact_email', 'info@logictech.com.pk');
    $coPhone   = trim(str_replace(['92 300 5308035,', '92 300 5308035', '0300-5308035'], '', \App\Models\Setting::get('company_phone') ?: \App\Models\Setting::get('web_contact_phone', '+92 336 1500082')), " \t\n\r\0\x0B,") ?: '+92 336 1500082';
    $coLogo    = \App\Models\Setting::getLogoUrl();
    $coNtn     = \App\Models\Setting::get('company_ntn',  '5561761-4');
    $coStrn    = \App\Models\Setting::get('company_strn', '3277876171582');
    $currency  = \App\Models\Setting::get('currency_symbol', 'PKR');

    /* ── Customer / Buyer ── */
    $custName  = $quotation->walkin_name ?? ($quotation->customer_relation->customer_name ?? 'Walk-in Customer');
    $custNtn   = $quotation->customer_relation->cnic ?? ($quotation->customer_relation->ntn ?? '');
    $custStrn  = $quotation->customer_relation->strn ?? '';
    $custAddr  = $quotation->customer_relation->address ?? '';
    $custMob   = $quotation->customer_relation->mobile ?? ($quotation->customer_relation->mobile_2 ?? '');
    $custEmail = $quotation->customer_relation->email_address ?? '';
    $custAttn  = $quotation->customer_relation->contact_person ?? '';
    $custType  = $quotation->customer_relation->customer_type ?? '';

    /* ── Totals ── */
    $subTotal     = (float)$quotation->total_bill_amount;
    $totalDisc    = (float)$quotation->total_discount;
    $totalTax     = (float)$quotation->tax_amount;
    $netPayable   = (float)$quotation->total_net;
    $amtWords     = numberToWordsQuotation($netPayable);

    /* ── Subject ── */
    $subject = $quotation->reference;
    if (empty($subject) && count($items) > 0) {
        $subject = ($items[0]['item_name'] ?? 'Product').(count($items)>1?' & Related Items':'');
    }
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Quotation {{ $quotation->quote_no }}</title>
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

/* ── Action Buttons ── */
.no-print{
    position:fixed;top:12px;right:20px;z-index:9999;
    display:flex;gap:8px;
}
.no-print button,.no-print a{
    padding:6px 16px;font-size:12px;font-weight:700;
    border-radius:5px;cursor:pointer;border:none;
    text-decoration:none;display:inline-block;
    font-family:'Plus Jakarta Sans', sans-serif;
}
.no-print button{background:#2563eb;color:#fff;box-shadow:0 3px 8px rgba(37,99,235,.35);}
.no-print a.btn-back{background:#64748b;color:#fff;}
.no-print a.btn-convert{background:#059669;color:#fff;}

/* ── A4 Sheet ── */
.pg{
    width:210mm;
    min-height:297mm;
    margin:0 auto;
    background:#fff;
    padding:6mm 10mm 10mm;
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
    border-bottom:2px solid #2d6385;
    margin-bottom:6px;
}
.hdr-logo{flex:0 0 auto;}
.hdr-logo img{max-width:210px;max-height:65px;object-fit:contain;display:block;}
.logo-txt{
    font-family:'Outfit', sans-serif;
    font-size:24px;font-weight:800;color:#1e3a8a;
    text-transform:uppercase;letter-spacing:0.8px;line-height:1;
    border-left:4px solid #2d6385;
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
    font-size:10px;
    color:#334155;
    font-weight:500;
    line-height:1.4;
    display:inline-flex;
    align-items:center;
    gap:6px;
}
.hdr-info .co-line svg{flex-shrink:0;}
.hdr-badge{
    display:inline-flex;
    align-items:center;
    gap:6px;
    background:#f1f5f9;
    border:1px solid #cbd5e1;
    padding:2px 8px;
    border-radius:4px;
    font-size:10px;
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
    font-size:10px;
}
.badge-divider{
    color:#94a3b8;
    margin:0 2px;
}

/* ══════════════════
   2. QUOTATION TITLE (Image 4: Q U O T A T I O N)
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
    color:#1e3a8a;
    letter-spacing:7px;
    text-transform:uppercase;
    padding-bottom:3px;
    border-bottom:2.5px solid #2d6385;
    display:inline-block;
}

/* ══════════════════
   3. META TABLE (Image 4: Quote No., Quotation Date, Est. Delivery Date. NO Order No!)
══════════════════ */
.meta-wrap{
    width:100%;
    margin-bottom:8px;
    border:2px solid #2d6385;
    border-radius:4px;
    overflow:hidden;
    background:#fff;
}
.meta-tbl{
    width:100%;
    border-collapse:collapse;
    margin:0;
}
.meta-tbl td{
    border:1px solid #78909c;
    padding:4.5px 8px;
    font-size:10px;
}
.meta-tbl .ml{
    font-family:'Outfit', sans-serif;
    font-weight:700;
    color:#1e3a8a;
    background:#f1f5f9;
    white-space:nowrap;
    width:18%;
    border:1px solid #78909c;
    letter-spacing:0.2px;
}
.meta-tbl .mv{
    font-weight:600;
    color:#0f172a;
    width:32%;
    border:1px solid #78909c;
}

/* ══════════════════
   4. BUYER
══════════════════ */
.buyer{
    width:100%;
    border:2px solid #2d6385;
    border-radius:4px;
    margin-bottom:8px;
    overflow:hidden;
}
.buyer-hdr{
    background:#2d6385;
    color:#fff;
    font-family:'Outfit', sans-serif;
    font-weight:800;font-size:10px;
    padding:3.5px 8px;
    text-transform:uppercase;letter-spacing:.4px;
}
.buyer-body{padding:6px 8px;}
.buyer-ms{
    font-size:12px;font-weight:800;color:#0f172a;
    margin-bottom:5px;
}
.ms-pre{color:#1e3a8a;font-weight:700;}
.buyer-grid{
    display:grid;grid-template-columns:1fr 1fr;
    gap:2px 14px;font-size:10px;
}
.bg-r{display:flex;gap:4px;}
.bg-l{color:#475569;font-weight:600;min-width:62px;flex-shrink:0;}
.bg-v{color:#0f172a;font-weight:600;}

/* ══════════════════
   5. SUBJECT
══════════════════ */
.subj{
    font-size:10.5px;
    padding:4px 0 6px;
    border-bottom:1px dashed #cbd5e1;
    margin-bottom:8px;
    color:#0f172a;
}
.subj strong{color:#1e3a8a;}

/* ══════════════════
   6. ITEMS TABLE (Image 4)
══════════════════ */
.itbl{
    width:100%;
    border-collapse:collapse;
    margin-bottom:8px;
    border:2px solid #2d6385;
}
.itbl th{
    background-color:#2d6385 !important;
    color:#fff !important;
    font-family:'Outfit', sans-serif;
    font-weight:800;
    font-size:9px;
    text-transform:uppercase;
    letter-spacing:.3px;
    padding:5px 6px;
    border:1px solid #78909c;
    text-align:center;
}
.itbl th.tl{text-align:left;padding-left:8px;}
.itbl td{
    border:1px solid #78909c;
    padding:4px 6px;
    vertical-align:top;
    font-size:10px;
}
.itbl tbody tr:nth-child(odd) td{background:#fafcff;}
.itbl .sn{text-align:center;font-weight:700;color:#1e3a8a;width:4%;}
.itbl .tc{text-align:center;}
.itbl .tr{text-align:right;}

/* Specifications inside row */
.ispec-box{
    margin-top:4px;
    padding:4px 8px;
    background:#f8fafc;
    border-left:3px solid #2d6385;
    border-radius:3px;
    font-size:9px;
}
.ispec-lbl{
    font-family:'Outfit', sans-serif;
    font-size:8.5px;
    font-weight:800;
    color:#1e3a8a;
    text-transform:uppercase;
    letter-spacing:0.4px;
    margin-bottom:3px;
}
.ispec-ul{list-style:none;padding:0;margin:0;line-height:1.45;}
.ispec-ul li{display:flex;align-items:baseline;margin-bottom:2px;}
.ispec-bullet{color:#2563eb;font-size:8px;margin-right:5px;flex-shrink:0;}

/* Grand Total Row */
.gt-row td{
    background:#f5f5e0 !important;
    font-weight:800;
    border-top:2px solid #2d6385 !important;
    border-bottom:2px solid #2d6385 !important;
    padding:5px 6px;
}
.gt-lbl{
    font-family:'Outfit', sans-serif;
    color:#1e3a8a;
    font-size:10px;
    text-transform:uppercase;
    letter-spacing:.3px;
}
.gt-val{
    font-family:'Outfit', sans-serif;
    font-size:11px;
    font-weight:900;
    color:#0f172a;
    text-align:right;
}

/* Amount in words */
.awtbl{width:100%;border-collapse:collapse;margin-bottom:10px;border:1.5px solid #2d6385;}
.aw-lbl{
    background:#2d6385 !important;
    color:#fff !important;
    font-family:'Outfit', sans-serif;
    font-weight:800;font-size:9px;
    text-transform:uppercase;letter-spacing:.3px;
    padding:4px 8px;width:15%;text-align:center;
}
.aw-val{
    padding:4px 10px;font-style:italic;
    color:#1e3a8a;font-weight:700;font-size:10px;
    background:#f8fafc;
}

/* ══════════════════
   7. BOTTOM SECTION: Terms & Conditions Left | Financial Summary Right
══════════════════ */
.bottom{
    display:flex;
    gap:14px;
    align-items:flex-start;
    margin-top:6px;
}
.terms{
    flex:1.4;
    font-size:9.5px;
    color:#334155;
}
.terms .t-ttl{
    font-family:'Outfit', sans-serif;
    font-weight:800;
    font-size:10.5px;
    color:#1e3a8a;
    border-bottom:1.5px solid #2d6385;
    padding-bottom:3px;
    margin-bottom:5px;
    text-transform:uppercase;
    letter-spacing:.4px;
}
.terms-content{
    white-space:pre-line;
    line-height:1.45;
    font-size:9px;
    color:#1e293b;
}

/* Financial Summary Box (Image 4) */
.fin-box{
    flex:0 0 210px;
    border:2px solid #2d6385;
    border-radius:4px;
    overflow:hidden;
}
.fin-hdr{
    background:#2d6385 !important;
    color:#fff !important;
    font-family:'Outfit', sans-serif;
    font-weight:800;font-size:10px;
    text-align:center;
    padding:4px;
    text-transform:uppercase;letter-spacing:.4px;
}
.fin-tbl{width:100%;border-collapse:collapse;}
.fin-tbl tr td{
    padding:3.5px 8px;
    border-bottom:1px solid #cbd5e1;
    font-size:9.5px;
}
.fin-tbl tr:last-child td{border-bottom:none;}
.fin-tbl .fl{color:#475569;font-weight:600;}
.fin-tbl .fv{font-weight:700;color:#0f172a;text-align:right;}
.fin-tbl .fv.red{color:#c62828;}
.fin-tbl .fv.green{color:#2e7d32;}
.fin-hl td{
    background:#f5f5e0 !important;
    font-weight:800 !important;
    border-top:1.5px solid #2d6385 !important;
    border-bottom:1.5px solid #2d6385 !important;
}
.fin-hl .fv{font-size:11px !important;color:#0f172a;}

/* Signature (Image 4) */
.sig-area{
    display:flex;
    justify-content:flex-end;
    margin-top:35px;
}
.sig-blk{text-align:center;width:220px;position:relative;}
.sig-co{
    font-family:'Outfit', sans-serif;
    font-size:10px;color:#1e3a8a;font-weight:700;
    margin-bottom:60px;text-transform:uppercase;letter-spacing:.3px;
}
.sig-line{
    border-top:1.5px solid #0f172a;
    padding-top:4px;
    font-family:'Outfit', sans-serif;
    font-size:9.5px;font-weight:800;
    text-transform:uppercase;letter-spacing:.3px;color:#0f172a;
}
.sig-img-container{
    position:absolute;
    bottom:20px;
    left:50%;
    transform:translateX(-50%);
    width:100%;
    height:55px;
    display:flex;
    align-items:center;
    justify-content:center;
    pointer-events:none;
}
.sig-stamp-img{max-height:55px;max-width:120px;object-fit:contain;opacity:0.85;}
.sig-sign-img{max-height:42px;max-width:110px;object-fit:contain;position:absolute;top:5px;}

/* ── Print Media ── */
@media print{
    @page{size:A4 portrait;margin:4mm 8mm;}
    body{background:#fff !important;padding:0 !important;margin:0 !important;}
    .no-print{display:none !important;}
    .pg{width:100% !important;min-height:auto !important;margin:0 !important;padding:0 !important;box-shadow:none !important;}
    .itbl th,.fin-hdr,.aw-lbl,.buyer-hdr{background-color:#2d6385 !important;color:#fff !important;-webkit-print-color-adjust:exact !important;print-color-adjust:exact !important;}
    .gt-row td,.fin-hl td{background:#f5f5e0 !important;-webkit-print-color-adjust:exact !important;print-color-adjust:exact !important;}
    .itbl tbody tr:nth-child(odd) td{background:#fafcff !important;-webkit-print-color-adjust:exact !important;print-color-adjust:exact !important;}
    .itbl td,.meta-tbl td,.buyer{border-color:#78909c !important;}
}
</style>
</head>
<body>

<div class="no-print">
    <button onclick="window.print()">&#128438; Print Quotation</button>
    <a href="{{ route('quotations.convert_to_order', $quotation->id) }}" class="btn-convert">&#10140; Convert to Order</a>
    <a href="{{ route('quotations.index') }}" class="btn-back">&#8592; Back to Quotations</a>
</div>

<div class="pg">

    {{-- ═══ 1. HEADER (Matches Image 4) ═══ --}}
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

    {{-- ═══ 2. QUOTATION TITLE (Image 4: Q U O T A T I O N) ═══ --}}
    <div class="inv-title">
        <span>{{ strtoupper($quotation->quote_title ?: 'QUOTATION') }}</span>
    </div>

    {{-- ═══ 3. META TABLE (Image 4: Quote No., Quotation Date, Est. Delivery Date. NO Order No!) ═══ --}}
    <div class="meta-wrap">
        <table class="meta-tbl">
            <tr>
                <td class="ml">Quote No.</td>
                <td class="mv" style="font-weight: 700; color: #1e3a8a;">{{ $quotation->quote_no }}</td>
                <td class="ml">Est. Delivery Date</td>
                <td class="mv">
                    {{ $quotation->estimated_delivery_date ? \Carbon\Carbon::parse($quotation->estimated_delivery_date)->format('d-M-Y') : '—' }}
                </td>
            </tr>
            <tr>
                <td class="ml">Quotation Date</td>
                <td class="mv" colspan="3">{{ $quotation->quotation_date ? \Carbon\Carbon::parse($quotation->quotation_date)->format('d-M-Y') : $quotation->created_at->format('d-M-Y') }}</td>
            </tr>
        </table>
    </div>

    {{-- ═══ 4. BUYER (Bill & Ship To) ═══ --}}
    <div class="buyer">
        <div class="buyer-hdr">BUYER (Bill &amp; Ship To)</div>
        <div class="buyer-body">
            <div class="buyer-ms">
                <span class="ms-pre">M/S &nbsp;</span>
                {{ $custName }}
                @if($custType)<small style="color:#64748b;font-weight:500;">&nbsp;({{ ucfirst($custType) }})</small>@endif
            </div>
            <div class="buyer-grid">
                <div class="bg-r"><span class="bg-l">NTN No:</span> <span class="bg-v">{{ $custNtn ?: '—' }}</span></div>
                <div class="bg-r"><span class="bg-l">STRN No:</span><span class="bg-v">{{ $custStrn ?: '—' }}</span></div>
                <div class="bg-r"><span class="bg-l">Address:</span><span class="bg-v">{{ $custAddr ?: '—' }}</span></div>
                <div class="bg-r"><span class="bg-l">Mobile:</span> <span class="bg-v">{{ $custMob ?: '—' }}</span></div>
                @if($custEmail)<div class="bg-r"><span class="bg-l">Email:</span>  <span class="bg-v">{{ $custEmail }}</span></div>@endif
                @if($custAttn) <div class="bg-r"><span class="bg-l">Attn:</span>   <span class="bg-v">{{ $custAttn }}</span></div>@endif
            </div>
        </div>
    </div>

    {{-- ═══ 5. SUBJECT ═══ --}}
    @if(!empty($subject))
        <div class="subj"><strong>Subject:</strong>&nbsp; {{ $subject }}</div>
    @endif

    {{-- ═══ 6. ITEMS TABLE (Image 4 Columns) ═══ --}}
    <table class="itbl">
        <thead>
            <tr>
                <th style="width:4%">S/N</th>
                <th class="tl" style="width:38%">Product/Services Details</th>
                <th style="width:7%">Qty.</th>
                <th style="width:11%">Unit-<br>Price</th>
                <th style="width:11%">Amount</th>
                <th style="width:6%">GST<br>%</th>
                <th style="width:10%">GST<br>Amount</th>
                <th style="width:13%">Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @php
                $totalTableTax = 0;
            @endphp
            @foreach($items as $item)
                @php
                    $qty = (float)($item['qty'] ?? 1);
                    $price = (float)($item['price'] ?? 0);
                    $disc = (float)($item['discount_amount'] ?? 0);
                    $discP = (float)($item['discount_percent'] ?? 0);
                    $gross = $qty * $price;
                    $taxPct = (float)($item['tax_percent'] ?? 0);
                    $taxAmt = (float)($item['tax_amount'] ?? 0);
                    $afterDisc = max(0, $gross - $disc);
                    $itemTotalWithTax = $afterDisc + $taxAmt;
                    $totalTableTax += $taxAmt;

                    // Specs list
                    $specs = [];
                    if (!empty($item['technical_name'])) {
                        $specs[] = ['Equipment Title', $item['technical_name']];
                    }
                    if (!empty($item['technical_specs'])) {
                        $tLines = array_values(array_filter(array_map('trim', preg_split('/\r\n|\r|\n/', (string)$item['technical_specs'])), 'strlen'));
                        foreach ($tLines as $tIdx => $tLine) {
                            $cleanT = ltrim($tLine, "•-* \t");
                            if ($cleanT !== '') {
                                $specs[] = [$tIdx === 0 ? 'Specifications' : '', $cleanT];
                            }
                        }
                    }
                    if (!empty($item['brand'])) $specs[] = ['Input Power / Brand', $item['brand']];
                    if (!empty($item['color_val'])) $specs[] = ['Duty Cycle / Rating', $item['color_val']];
                    if (!empty($item['model'])) $specs[] = ['Cooling Type / Model', $item['model']];
                    if ($disc > 0) {
                        $discLabel = 'Discount' . ($discP > 0 ? ' (' . number_format($discP, 2) . '%)' : '');
                        $specs[] = [$discLabel, '-' . number_format($disc, 2), true];
                    }
                @endphp
                <tr>
                    <td class="sn">{{ $loop->iteration }}</td>
                    <td>
                        <div style="font-weight: 700; font-size: 11px; color: #0f172a;">
                            {{ $item['item_name'] }}
                        </div>
                        @if(count($specs) > 0)
                            <div class="ispec-box">
                                <div class="ispec-lbl">Specifications &amp; Details:</div>
                                <ul class="ispec-ul">
                                    @foreach($specs as $sp)
                                        @php
                                            $isSpDisc = !empty($sp[2]);
                                        @endphp
                                        <li>
                                            <span class="ispec-bullet" style="{{ $isSpDisc ? 'color: #dc2626 !important;' : '' }}">•</span>
                                            <div>
                                                @if(!empty($sp[0]))
                                                    <strong style="color: {{ $isSpDisc ? '#dc2626' : '#1e3a8a' }} !important;">{{ $sp[0] }}:</strong>
                                                @endif
                                                <span style="color: {{ $isSpDisc ? '#dc2626' : '#334155' }} !important; {{ $isSpDisc ? 'font-weight: 700;' : '' }}">{{ $sp[1] }}</span>
                                            </div>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif
                    </td>
                    <td class="tc">{{ $qty }} {{ $item['unit'] ?? 'Pcs' }}</td>
                    <td class="tr">{{ number_format($price, 2) }}</td>
                    <td class="tr" style="vertical-align: top; height: 100%;">
                        <div style="display: flex; flex-direction: column; justify-content: space-between; height: 100%; min-height: {{ count($specs) > 0 ? '70px' : 'auto' }};">
                            <div>{{ number_format($gross, 2) }}</div>
                            @if($disc > 0)
                                <div style="color: #dc2626; font-weight: 700; font-size: 9px; margin-top: auto; padding-top: 8px;">
                                    -{{ number_format($disc, 2) }}
                                </div>
                            @endif
                        </div>
                    </td>
                    <td class="tc">{{ $taxPct > 0 ? (float)$taxPct.'%' : '0%' }}</td>
                    <td class="tr">{{ $taxAmt > 0 ? number_format($taxAmt, 2) : '0.00' }}</td>
                    <td class="tr" style="font-weight: 700; color: #0f172a;">
                        {{ $currency }} {{ number_format($itemTotalWithTax, 0) }}
                    </td>
                </tr>
            @endforeach

            {{-- Grand Total Row --}}
            <tr class="gt-row">
                <td colspan="4" class="gt-lbl" style="text-align:right;padding-right:10px;">Grand Total &nbsp;&nbsp; ({{ $currency }})</td>
                <td class="tr" style="font-weight:700;">{{ number_format($subTotal - $totalDisc, 2) }}</td>
                <td class="tc" style="font-weight:700;">{{ (float)($quotation->tax_percent ?? 0) > 0 ? (float)$quotation->tax_percent.'%' : '0%' }}</td>
                <td class="tr" style="font-weight:700;">{{ number_format($totalTax, 2) }}</td>
                <td class="gt-val">{{ number_format($netPayable, 0) }}</td>
            </tr>
        </tbody>
    </table>

    {{-- Amount in words --}}
    <table class="awtbl">
        <tr>
            <td class="aw-lbl">Amount<br>in words</td>
            <td class="aw-val">*** {{ $amtWords }} ***</td>
        </tr>
    </table>

    {{-- ═══ 7. BOTTOM: Terms & Conditions Left | Financial Summary Right (NO Payment Remarks! As in Image 4) ═══ --}}
    <div class="bottom">
        {{-- Terms & Conditions --}}
        <div class="terms">
            <div class="t-ttl">Terms &amp; Conditions</div>
            <div class="terms-content">{{ $quotation->terms_and_conditions ?: \App\Models\Quotation::DEFAULT_TERMS }}</div>
        </div>

        {{-- Financial Summary Box (Image 4) --}}
        <div class="fin-box">
            <div class="fin-hdr">Financial Summary</div>
            <table class="fin-tbl">
                <tr>
                    <td class="fl">Sub Total:</td>
                    <td class="fv">{{ number_format($subTotal, 2) }}</td>
                </tr>
                @if($totalDisc > 0)
                    <tr>
                        <td class="fl">Item Discount:</td>
                        <td class="fv red">-{{ number_format($totalDisc, 2) }}</td>
                    </tr>
                @endif
                @if($totalTax > 0)
                    <tr>
                        <td class="fl">Sales Tax / GST:</td>
                        <td class="fv green">+{{ number_format($totalTax, 2) }}</td>
                    </tr>
                @endif
                <tr class="fin-hl">
                    <td class="fl" style="font-weight:800;">Net Payable:</td>
                    <td class="fv">{{ number_format($netPayable, 2) }}</td>
                </tr>
            </table>
        </div>
    </div>

    {{-- ═══ 8. AUTHORIZED SIGNATURE (Image 4) ═══ --}}
    @php
        $coStamp = \App\Models\Setting::getStampUrl();
        $coSignature = \App\Models\Setting::getSignatureUrl();
    @endphp
    <div class="sig-area">
        <div class="sig-blk">
            <div class="sig-co">For {{ $coName }}</div>
            @if($coStamp || $coSignature)
                <div class="sig-img-container">
                    @if($coStamp)
                        <img src="{{ $coStamp }}" alt="Company Stamp" class="sig-stamp-img">
                    @endif
                    @if($coSignature)
                        <img src="{{ $coSignature }}" alt="E-Signature" class="sig-sign-img">
                    @endif
                </div>
            @endif
            <div class="sig-line">Authorized Signature</div>
        </div>
    </div>

</div>{{-- end pg --}}
</body>
</html>
