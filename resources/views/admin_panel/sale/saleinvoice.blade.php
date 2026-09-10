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
    $custName  = $sale->walkin_name ?? ($sale->customer_relation->customer_name ?? 'Walk-in Customer');
    $custNtn   = $sale->customer_relation->cnic         ?? ($sale->customer_relation->ntn ?? '');
    $custStrn  = $sale->customer_relation->strn         ?? '';
    $custAddr  = $sale->customer_relation->address      ?? '';
    $custMob   = $sale->customer_relation->mobile       ?? ($sale->customer_relation->mobile_2 ?? '');
    $custEmail = $sale->customer_relation->email_address ?? '';
    $custAttn  = $sale->customer_relation->contact_person ?? '';
    $custType  = $sale->customer_relation->customer_type  ?? '';
    $isWalkin  = empty($sale->customer_id);

    /* ── Exchange Returns ── */
    $exRet = \App\Models\SaleReturn::with('items.product')
        ->where('remarks','LIKE','%Invoice #'.$sale->invoice_no.'%')->first();
    $exAmt = $exRet ? $exRet->items->sum('line_total') : 0;

    /* ── Totals ── */
    $subTotal     = (float)$sale->total_bill_amount;
    $extraDisc    = (float)($sale->total_extradiscount ?? 0);
    $netPayable   = (float)$sale->total_net;
    $finalPayable = $netPayable - $exAmt;
    $paidCash     = (float)($sale->cash  ?? 0);
    $paidCard     = (float)($sale->card  ?? 0);
    $paidTotal    = $paidCash + $paidCard;
    $changeGiven  = (float)($sale->change ?? 0);
    $balanceDue   = max(0, $finalPayable - $paidTotal);
    $itemDisc     = collect($saleItems)->sum('discount_amount');
    $amtWords     = numberToWordsInvoice($finalPayable > 0 ? $finalPayable : $netPayable);

    /* ── Subject ── */
    $subject = $sale->reference;
    if (empty($subject) && count($saleItems) > 0)
        $subject = ($saleItems[0]['item_name'] ?? 'Product').(count($saleItems)>1?' & Related Items':'');

    /* ── Payment Mode ── */
    $payMode = $paidCash>0&&$paidCard>0?'Cash + Card':($paidCard>0?'Card':($paidCash>0?'Cash':'Credit'));

    /* ── Status Labels ── */
    $saleStatus  = ucfirst($sale->sale_status  ?? 'N/A');
    $orderStatus = ucfirst($sale->order_status ?? 'N/A');
@endphp
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Invoice {{ $sale->invoice_no }}</title>
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

/* ── Buttons ── */
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
.no-print a     {background:#64748b;color:#fff;}

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
    border-bottom:2px solid #2d6385;
    margin-bottom:6px;
}
/* Logo / company placeholder */
.hdr-logo{flex:0 0 auto;}
.hdr-logo img{max-width:210px;max-height:65px;object-fit:contain;display:block;}
.logo-txt{
    font-family:'Outfit', sans-serif;
    font-size:24px;font-weight:800;color:#1e3a8a;
    text-transform:uppercase;letter-spacing:0.8px;line-height:1;
    border-left:4px solid #2d6385;
    padding-left:10px;
}
/* Company info right */
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
.hdr-info .co-line svg{
    flex-shrink:0;
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
   2. INVOICE TITLE
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
   4. META TABLE (Balanced 4-Column Full Width)
══════════════════ */
.meta-wrap{
    width:100%;
    margin-bottom:8px;
    border:2px solid #2d6385;
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
    border:1px solid #78909c;
    padding:4px 8px;
    font-size:10px;
}
.meta-tbl .ml{
    font-family:'Outfit', sans-serif;
    font-weight:700;
    color:#1e3a8a;
    background:#f1f5f9;
    white-space:nowrap;
    width:16%;
    border:1px solid #78909c;
    letter-spacing:0.2px;
}
.meta-tbl .mv{
    font-weight:600;
    color:#0f172a;
    width:34%;
    border:1px solid #78909c;
    font-size:10.5px;
}
.meta-tbl tr td:first-child{border-left:none;}
.meta-tbl tr td:last-child{border-right:none;}
.meta-tbl tr:first-child td{border-top:none;}
.meta-tbl tr:last-child td{border-bottom:none;}

/* ══════════════════
   5. BUYER
══════════════════ */
.buyer{
    border:2px solid #2d6385;
    border-radius:4px;
    margin-bottom:8px;
    overflow:hidden;
    background:#fff;
}
.buyer-hdr{
    background:#f1f5f9;
    font-family:'Outfit', sans-serif;
    font-size:10.5px;font-weight:800;color:#1e3a8a;
    text-transform:uppercase;letter-spacing:.5px;
    padding:4px 9px;
    border-bottom:1.5px solid #2d6385;
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
.bg-l{font-weight:600;color:#475569;min-width:65px;}
.bg-v{font-weight:600;color:#0f172a;}

/* ══════════════════
   6. SUBJECT
══════════════════ */
.subj{
    font-size:10.5px;color:#0f172a;
    margin-bottom:6px;
    padding:3px 8px;
    border-left:3px solid #2d6385;
    background:#f8fafc;
}
.subj strong{color:#1e3a8a;font-family:'Outfit', sans-serif;font-weight:700;}

/* ══════════════════
   7. ITEMS TABLE
══════════════════ */
.itbl{
    width:100%;
    border-collapse:collapse;
    border:2px solid #2d6385;
}
.itbl th{
    background:#2d6385 !important;
    color:#fff !important;
    font-family:'Outfit', sans-serif;
    font-size:10px;font-weight:700;
    text-align:center;
    padding:5px 3px;
    border:1px solid #1a3e54;
    line-height:1.2;
    letter-spacing:.3px;
    text-transform:uppercase;
    -webkit-print-color-adjust:exact;
    print-color-adjust:exact;
}
.itbl th.tl{text-align:left;padding-left:7px;}
.itbl td{
    border:1px solid #78909c;
    padding:4.5px 5px;
    font-size:10px;
    vertical-align:top;
}
.itbl tbody tr:nth-child(odd) td {background:#fafcff;}
.itbl tbody tr:nth-child(even) td{background:#fff;}
.iname{font-family:'Plus Jakarta Sans', sans-serif;font-weight:700;font-size:10.5px;color:#0f172a;margin-bottom:2px;}
.ispec-lbl{font-family:'Outfit', sans-serif;font-size:9px;font-weight:700;color:#1e3a8a;margin:2px 0 1px;text-transform:uppercase;letter-spacing:.3px;}
.ispec-ul{
    list-style:none;
    padding:0;margin:0;
    font-size:9px;color:#334155;line-height:1.4;
}
.ispec-ul li{padding-left:8px;position:relative;}
.ispec-ul li::before{content:"–";position:absolute;left:0;color:#2d6385;}
.tc{text-align:center;}
.tr{text-align:right;padding-right:6px !important;}
.sn {text-align:center;font-weight:700;font-size:10px;color:#475569;}
/* Grand Total */
.gt-row td{
    background:#f5f5e0 !important;
    font-weight:700;font-size:11px;
    padding:4px 6px;
    border-top:2px solid #2d6385;
    border-bottom:2px solid #2d6385;
    border-left:1px solid #78909c;
    border-right:1px solid #78909c;
    -webkit-print-color-adjust:exact;
    print-color-adjust:exact;
}
.gt-lbl{font-family:'Outfit', sans-serif;text-align:center;color:#1e3a8a;font-weight:800;letter-spacing:.5px;font-size:11px;text-transform:uppercase;}
.gt-val{font-family:'Plus Jakarta Sans', sans-serif;text-align:right;font-weight:800;font-size:12px;color:#0f172a;padding-right:5px;}
/* Amount in Words */
.awtbl{
    width:100%;
    border-collapse:collapse;
    border:2px solid #2d6385;
    border-top:none;
}
.awtbl td{
    border:1px solid #78909c;
    border-top:0;
    padding:4px 8px;
    font-size:10px;
}
.aw-lbl{
    font-family:'Outfit', sans-serif;
    font-weight:800;
    background:#2d6385;
    color:#fff;
    width:100px;white-space:nowrap;
    text-align:center;
    font-size:9.5px;text-transform:uppercase;
    letter-spacing:.4px;
    border:1px solid #1a3e54;
    -webkit-print-color-adjust:exact;
    print-color-adjust:exact;
}
.aw-val{font-family:'Plus Jakarta Sans', sans-serif;text-align:center;font-weight:700;color:#1e3a8a;font-size:10.5px;letter-spacing:.3px;}

/* ══════════════════
   8. BOTTOM
══════════════════ */
.bottom{
    display:flex;
    justify-content:space-between;
    align-items:flex-start;
    gap:12px;
    margin-top:8px;
}
/* Terms */
.terms{flex:1.3;font-size:9.5px;color:#334155;}
.terms .t-ttl{
    font-family:'Outfit', sans-serif;
    font-weight:800;font-size:10.5px;color:#1e3a8a;
    border-bottom:1.5px solid #2d6385;
    padding-bottom:2px;margin-bottom:4px;
    text-transform:uppercase;letter-spacing:.4px;
}
.terms ol{padding-left:12px;margin:0;}
.terms ol li{margin-bottom:1.5px;line-height:1.35;}
.ledger-box{
    margin-top:5px;
    border:1px solid #78909c;
    padding:3px 7px;background:#f8fafc;border-radius:3px;
    font-size:9px;
}
.ledger-box .lb-row{display:flex;justify-content:space-between;padding:1px 0;}
.ledger-box .lb-lbl{color:#64748b;}
.ledger-box .lb-val{font-weight:700;color:#0f172a;}

/* Financial Summary */
.fin-box{
    flex:0 0 195px;
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
    -webkit-print-color-adjust:exact;
    print-color-adjust:exact;
}
.fin-tbl{width:100%;border-collapse:collapse;}
.fin-tbl tr td{
    padding:2.5px 7px;
    border-bottom:1px solid #78909c;
    border-right:1px solid #e2e8f0;
    font-size:9.5px;
}
.fin-tbl tr:last-child td{border-bottom:none;}
.fin-tbl tr td:last-child{border-right:none;}
.fin-tbl .fl{color:#475569;font-weight:600;}
.fin-tbl .fv{font-weight:700;color:#0f172a;text-align:right;}
.fin-tbl .fv.red  {color:#c62828;}
.fin-tbl .fv.green{color:#2e7d32;}
.fin-tbl .fv.sub  {font-size:8.5px;color:#64748b;}
.fin-hl td{
    background:#f5f5e0 !important;
    font-weight:800 !important;
    font-size:10.5px !important;
    border-top:1.5px solid #2d6385 !important;
    border-bottom:1.5px solid #2d6385 !important;
    -webkit-print-color-adjust:exact;
    print-color-adjust:exact;
}
.fin-hl .fv{font-size:11.5px !important;}

/* Signature */
.sig-area{display:flex;justify-content:flex-end;margin-top:16px;}
.sig-blk{text-align:center;width:185px;}
.sig-co{
    font-family:'Outfit', sans-serif;
    font-size:9.5px;color:#1e3a8a;font-weight:700;
    margin-bottom:24px;text-transform:uppercase;letter-spacing:.3px;
}
.sig-line{
    border-top:1.5px solid #0f172a;
    padding-top:3px;
    font-family:'Outfit', sans-serif;
    font-size:9.5px;font-weight:800;
    text-transform:uppercase;letter-spacing:.3px;color:#0f172a;
}

/* ── PRINT ── */
@media print{
    @page{size:A4 portrait;margin:4mm 8mm;}
    body{background:#fff !important;padding:0 !important;margin:0 !important;}
    .no-print{display:none !important;}
    .pg{width:100% !important;min-height:auto !important;margin:0 !important;padding:0 !important;box-shadow:none !important;}
    .itbl th,.fin-hdr,.aw-lbl{background-color:#2d6385 !important;color:#fff !important;-webkit-print-color-adjust:exact !important;print-color-adjust:exact !important;}
    .gt-row td,.fin-hl td{background:#f5f5e0 !important;-webkit-print-color-adjust:exact !important;print-color-adjust:exact !important;}
    .itbl tbody tr:nth-child(odd) td{background:#fafcff !important;-webkit-print-color-adjust:exact !important;print-color-adjust:exact !important;}
    .itbl td,.meta-tbl td,.seller-left,.buyer{border-color:#78909c !important;}
}
</style>
</head>
<body>

<div class="no-print">
    <button onclick="window.print()">&#128438; Print Invoice</button>
    <a href="{{ route('sale.index') }}">&#8592; Back to Sales</a>
</div>

<div class="pg">

    {{-- ═══ 1. HEADER ═══ --}}
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
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#ef4444" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M21 10c0 7-9 13-9 13s-9-6-9-13a9 9 0 0 1 18 0z"></path><circle cx="12" cy="10" r="3"></circle></svg>
                    <span>{{ $coAddr }}</span>
                </div>
            @endif
            @if($coEmail)
                <div class="co-line">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#2563eb" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M4 4h16c1.1 0 2 .9 2 2v12c0 1.1-.9 2-2 2H4c-1.1 0-2-.9-2-2V6c0-1.1.9-2 2-2z"></path><polyline points="22,6 12,13 2,6"></polyline></svg>
                    <span>{{ $coEmail }}</span>
                </div>
            @endif
            @if($coPhone)
                <div class="co-line">
                    <svg width="11" height="11" viewBox="0 0 24 24" fill="none" stroke="#059669" stroke-width="2.2" stroke-linecap="round" stroke-linejoin="round" style="flex-shrink:0;"><path d="M22 16.92v3a2 2 0 0 1-2.18 2 19.79 19.79 0 0 1-8.63-3.07 19.5 19.5 0 0 1-6-6 19.79 19.79 0 0 1-3.07-8.67A2 2 0 0 1 4.11 2h3a2 2 0 0 1 2 1.72 12.84 12.84 0 0 0 .7 2.81 2 2 0 0 1-.45 2.11L8.09 9.91a16 16 0 0 0 6 6l1.27-1.27a2 2 0 0 1 2.11-.45 12.84 12.84 0 0 0 2.81.7A2 2 0 0 1 22 16.92z"></path></svg>
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

    {{-- ═══ 2. INVOICE TITLE ═══ --}}
    <div class="inv-title">
        <span>{{ ($isEstimate ?? false) ? 'ESTIMATE' : 'INVOICE' }}</span>
    </div>

    {{-- ═══ 4. META TABLE (Balanced 4-Column Full Width) ═══ --}}
    <div class="meta-wrap">
        <table class="meta-tbl">
            <tr>
                <td class="ml">Invoice No.</td>
                <td class="mv">{{ $sale->invoice_no }}</td>
                <td class="ml">Order No.</td>
                <td class="mv">{{ 'ORD-'.str_pad($sale->id,4,'0',STR_PAD_LEFT) }}</td>
            </tr>
            <tr>
                <td class="ml">Invoice Date</td>
                <td class="mv">{{ $sale->created_at ? $sale->created_at->format('d-M-Y') : date('d-M-Y') }}</td>
                <td class="ml">Delivery Date</td>
                <td class="mv">
                    @if($sale->delivery_date || $sale->estimated_delivery_date)
                        {{ $sale->delivery_date ? \Carbon\Carbon::parse($sale->delivery_date)->format('d-M-Y') : \Carbon\Carbon::parse($sale->estimated_delivery_date)->format('d-M-Y') }}
                    @else
                        —
                    @endif
                </td>
            </tr>
            <tr>
                <td class="ml">Sale Status</td>
                <td class="mv"><span style="display:inline-block;padding:1px 6px;border-radius:3px;background:#e0f2fe;color:#0369a1;font-size:9.5px;font-weight:700;">{{ $saleStatus }}</span></td>
                <td class="ml">Order Status</td>
                <td class="mv">
                    @if($sale->order_status && strtolower($sale->order_status) !== 'n/a')
                        <span style="display:inline-block;padding:1px 6px;border-radius:3px;background:#f1f5f9;color:#334155;font-size:9.5px;font-weight:700;">{{ $orderStatus }}</span>
                    @else
                        —
                    @endif
                </td>
            </tr>
            <tr>
                <td class="ml">Payment Mode</td>
                <td class="mv">{{ $payMode }}</td>
                <td class="ml">Due Date</td>
                <td class="mv">
                    @if($sale->due_date)
                        {{ \Carbon\Carbon::parse($sale->due_date)->format('d-M-Y') }}
                        @if($sale->credit_days > 0)
                            <span style="font-size:9px;color:#64748b;font-weight:400;">({{ $sale->credit_days }}d credit)</span>
                        @endif
                    @elseif($sale->credit_days > 0)
                        {{ $sale->credit_days }} Days Credit
                    @else
                        —
                    @endif
                </td>
            </tr>
        </table>
    </div>

    {{-- ═══ 5. BUYER ═══ --}}
    <div class="buyer">
        <div class="buyer-hdr">BUYER (Bill &amp; Ship To)</div>
        <div class="buyer-body">
            <div class="buyer-ms">
                <span class="ms-pre">M/S &nbsp;</span>
                {{ $custName }}
                @if($custType)<small style="color:#777;font-weight:400;">&nbsp;({{ ucfirst($custType) }})</small>@endif
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

    {{-- ═══ 6. SUBJECT ═══ --}}
    @if(!empty($subject))
    <div class="subj"><strong>Subject:</strong>&nbsp; {{ $subject }}</div>
    @endif

    @if($sale->return_note)
    <div style="font-size:10px;font-style:italic;margin-bottom:6px;padding:3px 8px;border-left:3px solid #78909c;background:#f8fafc;color:#333;">
        <strong>Note:</strong> {{ $sale->return_note }}
    </div>
    @endif

    {{-- ═══ 7. ITEMS TABLE ═══ --}}
    <table class="itbl">
        <thead>
            <tr>
                <th style="width:4%">S/N</th>
                <th class="tl" style="width:38%">Product/Services Details</th>
                <th style="width:7%">Qty.</th>
                <th style="width:10%">Unit-<br>Price</th>
                <th style="width:10%">Amount</th>
                <th style="width:6%">GS<br>T%</th>
                <th style="width:10%">GST<br>Amount</th>
                <th style="width:15%">Total Amount</th>
            </tr>
        </thead>
        <tbody>
            @foreach($saleItems as $item)
            @php
                $tp  = (int)($item['total_pieces']??0);
                $sm  = $item['size_mode'] ?? 'std';
                $ppb = max(1,(int)($item['pieces_per_box']??1));
                $wg  = (float)($item['weight_per_piece']??0);

                if (in_array($sm,['by_kg','by_gm','by_feet','by_meter'])) {
                    $ul = match($sm){'by_kg'=>'Kg','by_gm'=>'Gm','by_feet'=>'Ft','by_meter'=>'Mtr',default=>''};
                    $qv = (float)($item['qty_box']??$item['qty']??$tp);
                    $qd = ($qv==(int)$qv?(int)$qv:number_format($qv,2)).' '.$ul;
                } elseif ($sm=='by_cartons') {
                    $bx=$tp/$ppb; $lo=$tp%$ppb;
                    $qd = $bx>0?($lo>0?"$bx Ctn + $lo Pcs":"$bx Ctn"):"$lo Pcs";
                } elseif ($sm=='by_size') {
                    $h=(float)($item['height']??0);$w=(float)($item['width']??0);
                    $qd = ($h>0&&$w>0)?number_format(($h*$w/10000)*$tp,2).' m²':$tp.' Pcs';
                } else { $qd = $tp.' Pcs'; }

                $disc  = (float)($item['discount_amount']??0);
                $discP = (float)($item['discount_percent']??0);
                $gross = (float)$item['total'] + $disc;
                $net   = (float)$item['total'];

                $specs=[];
                if(!empty($item['model']))     $specs[]=['Model',$item['model']];
                if(!empty($item['serial_no'])) $specs[]=['Serial No',$item['serial_no']];
                if(!empty($item['brand']))     $specs[]=['Brand',$item['brand']];
                if(!empty($item['item_code'])) $specs[]=['Item Code',$item['item_code']];
                if(!empty($item['technical_name']))    $specs[]=['Tech Name',$item['technical_name']];
                if(!empty($item['technical_specs']))   $specs[]=['Specifications',$item['technical_specs']];
                if(!empty($item['technical_remarks'])) $specs[]=['QC Remarks',$item['technical_remarks']];
                if(!empty($item['color_val'])&&$item['color_val']!=='-') $specs[]=['Specification',$item['color_val']];
                if(!empty($item['size_val']) &&$item['size_val']!=='-')  $specs[]=['Size',$item['size_val']];
                $h2=(float)($item['height']??0);$w2=(float)($item['width']??0);
                if($sm=='by_size'&&$h2>0&&$w2>0) $specs[]=['Dimensions',number_format($w2,0).'×'.number_format($h2,0).' mm'];
                if($wg>0) $specs[]=['Weight',($wg==(int)$wg?(int)$wg:$wg).'g'];
                if($disc>0) $specs[]=['Discount',($discP>0?number_format($discP,1).'% — ':'').number_format($disc,2).' '.$currency];
                $itemTaxPct = (float)($item['tax_percent'] ?? 0);
                $itemTaxAmt = (float)($item['tax_amount'] ?? 0);
                if ($itemTaxPct > 0 && $itemTaxAmt <= 0) {
                    $itemTaxAmt = round(($net * $itemTaxPct) / 100, 2);
                }
                $itemTotalWithTax = $net + $itemTaxAmt;
            @endphp
            <tr>
                <td class="sn">{{ $loop->iteration }}</td>
                <td>
                    <div class="iname">{{ $item['item_name'] }}</div>
                    @if(count($specs)>0)
                    <div class="ispec-lbl">Specifications</div>
                    <ul class="ispec-ul">
                        @foreach($specs as [$l,$v])<li><strong>{{ $l }}:</strong> {{ $v }}</li>@endforeach
                    </ul>
                    @endif
                </td>
                <td class="tc">{{ $qd }}</td>
                <td class="tr">{{ number_format($item['price'],2) }}</td>
                <td class="tr">{{ number_format($gross,2) }}</td>
                <td class="tc">{{ $itemTaxPct > 0 ? (float)$itemTaxPct.'%' : '0%' }}</td>
                <td class="tr">{{ $itemTaxAmt > 0 ? number_format($itemTaxAmt,2) : '—' }}</td>
                <td class="tr" style="font-weight:700;">{{ $currency }} {{ number_format($itemTotalWithTax,0) }}</td>
            </tr>
            @endforeach

            {{-- Exchange returns --}}
            @if($exRet && $exRet->items->count()>0)
                @foreach($exRet->items as $ri)
                <tr style="background:#fff5f5 !important;">
                    <td class="sn" style="color:#c00">R</td>
                    <td><div class="iname" style="color:#c00">Return: {{ $ri->product->item_name??($ri->product_name??'Item') }}</div></td>
                    <td class="tc">{{ (float)$ri->qty }} Pcs</td>
                    <td class="tr">{{ number_format($ri->price,2) }}</td>
                    <td class="tr" style="color:#c00">-{{ number_format($ri->line_total,2) }}</td>
                    <td class="tc">—</td><td class="tc">—</td>
                    <td class="tr" style="font-weight:700;color:#c00">-{{ number_format($ri->line_total,0) }}</td>
                </tr>
                @endforeach
            @endif

            {{-- Grand Total --}}
            <tr class="gt-row">
                <td colspan="5" class="gt-lbl">Grand Total &nbsp;&nbsp; ({{ $currency }})</td>
                <td class="tc" style="font-weight:700">*****</td>
                <td class="tc">—</td>
                <td class="gt-val">{{ number_format($finalPayable>0?$finalPayable:$netPayable,0) }}</td>
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

    {{-- ═══ 8. BOTTOM: Terms | Financial Summary ═══ --}}
    <div class="bottom">

        {{-- Terms --}}
        <div class="terms">
            @php
                $tc = !empty($sale->terms_and_conditions) ? $sale->terms_and_conditions : \App\Models\Setting::get('invoice_terms',"Payment due within 30 days.\nGoods once sold will not be returned without original invoice.\nWarranty covers manufacturing defects only.");
                $tl = array_values(array_filter(array_map('trim',explode("\n",str_replace("\r","",$tc)))));
            @endphp
            <div class="t-ttl">Terms &amp; Conditions</div>
            <ol>@foreach($tl as $t)<li>{{ $t }}</li>@endforeach</ol>

            @if(!$isWalkin && (abs($previousBalance)>0 || $paidTotal>0))
            <div class="ledger-box" style="margin-top:8px;">
                <div style="font-weight:800;color:#1d4fa0;font-size:9.5px;margin-bottom:3px;text-transform:uppercase;">Ledger Summary</div>
                @if(abs($previousBalance)>0)
                <div class="lb-row"><span class="lb-lbl">Previous Balance:</span><span class="lb-val">{{ number_format(abs($previousBalance),2) }} {{ $previousBalance>=0?'Dr':'Cr' }}</span></div>
                @endif
                @if($paidTotal>0)
                <div class="lb-row"><span class="lb-lbl">Payment Received:</span><span class="lb-val" style="color:#2e7d32;">{{ number_format($paidTotal,2) }}</span></div>
                @endif
                @if($changeGiven>0)
                <div class="lb-row"><span class="lb-lbl">Change Returned:</span><span class="lb-val">{{ number_format($changeGiven,2) }}</span></div>
                @endif
            </div>
            @endif
        </div>

        {{-- Financial Summary --}}
        <div class="fin-box">
            <div class="fin-hdr">Financial Summary</div>
            <table class="fin-tbl">
                @if($subTotal!=$finalPayable || $extraDisc>0 || $itemDisc>0 || $exAmt>0 || ($sale->tax_amount ?? 0) > 0)
                <tr><td class="fl">Sub Total:</td><td class="fv">{{ number_format($subTotal,2) }}</td></tr>
                @if($itemDisc>0)
                <tr><td class="fl">Item Discount:</td><td class="fv red">-{{ number_format($itemDisc,2) }}</td></tr>
                @endif
                @if($extraDisc>0)
                <tr><td class="fl">Extra Discount:</td><td class="fv red">-{{ number_format($extraDisc,2) }}</td></tr>
                @endif
                @if(($sale->tax_amount ?? 0) > 0 || ($sale->tax_percent ?? 0) > 0)
                <tr><td class="fl">Sales Tax / GST ({{ (float)($sale->tax_percent ?? 0) }}%):</td><td class="fv green">+{{ number_format((float)($sale->tax_amount ?? 0),2) }}</td></tr>
                @endif
                @if($exAmt>0)
                <tr><td class="fl">Return Deduction:</td><td class="fv red">-{{ number_format($exAmt,2) }}</td></tr>
                @endif
                @endif
                <tr class="fin-hl"><td class="fl" style="font-weight:800;">Net Payable:</td><td class="fv">{{ number_format($finalPayable>0?$finalPayable:$netPayable,2) }}</td></tr>
                @if($paidTotal>0)
                <tr><td class="fl">Paid Amount:</td><td class="fv green">{{ number_format($paidTotal,2) }}</td></tr>
                @if($paidCash>0&&$paidCard>0)
                <tr><td class="fl" style="padding-left:14px;font-size:9px;">Cash:</td><td class="fv sub">{{ number_format($paidCash,2) }}</td></tr>
                <tr><td class="fl" style="padding-left:14px;font-size:9px;">Card:</td><td class="fv sub">{{ number_format($paidCard,2) }}</td></tr>
                @endif
                @if($changeGiven>0)
                <tr><td class="fl">Change Given:</td><td class="fv">{{ number_format($changeGiven,2) }}</td></tr>
                @endif
                <tr><td class="fl">Balance Due:</td><td class="fv {{ $balanceDue>0?'red':'green' }}">{{ number_format($balanceDue,2) }}</td></tr>
                @endif
                @if(!$isWalkin && abs($previousBalance)>0)
                <tr><td class="fl">Prev. Balance:</td><td class="fv">{{ number_format(abs($previousBalance),2) }} {{ $previousBalance>=0?'Dr':'Cr' }}</td></tr>
                @php $cb=$previousBalance+($finalPayable>0?$finalPayable:$netPayable)-$paidTotal; @endphp
                <tr class="fin-hl"><td class="fl" style="font-weight:800;">Closing Balance:</td><td class="fv {{ $cb>0?'red':'green' }}">{{ number_format(abs($cb),2) }} {{ $cb>=0?'Dr':'Cr' }}</td></tr>
                @endif
            </table>
        </div>
    </div>

    {{-- Signature --}}
    <div class="sig-area">
        <div class="sig-blk">
            <div class="sig-co">For {{ $coName }}</div>
            <div class="sig-line">Authorized Signature</div>
        </div>
    </div>

</div>{{-- end pg --}}
</body>
</html>