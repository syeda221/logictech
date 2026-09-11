<!doctype html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width,initial-scale=1">
<title>Receipt Voucher - {{ $voucher->rvid }}</title>
<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
<link href="https://fonts.googleapis.com/css2?family=Outfit:wght@500;600;700;800;900&family=Plus+Jakarta+Sans:wght@400;500;600;700;800&display=swap" rel="stylesheet">
<style>
*{margin:0;padding:0;box-sizing:border-box;}
body{
    font-family:'Plus Jakarta Sans',-apple-system,BlinkMacSystemFont,'Segoe UI',Roboto,Helvetica,Arial,sans-serif;
    font-size:10.5px;color:#1e293b;background:#cdd5df;
    padding:10px 0;
    -webkit-font-smoothing:antialiased;
    -webkit-print-color-adjust:exact;
    print-color-adjust:exact;
}
.no-print{position:fixed;top:12px;right:20px;z-index:9999;display:flex;gap:8px;}
.no-print button,.no-print a{
    padding:6px 16px;font-size:12px;font-weight:700;border-radius:5px;
    cursor:pointer;border:none;text-decoration:none;display:inline-block;
    font-family:'Plus Jakarta Sans',sans-serif;
}
.no-print button{background:#2563eb;color:#fff;box-shadow:0 3px 8px rgba(37,99,235,.35);}
.no-print a{background:#64748b;color:#fff;}
.pg{
    width:210mm;min-height:297mm;margin:0 auto;background:#fff;
    padding:5mm 10mm 10mm;box-shadow:0 8px 32px rgba(0,0,0,.2);position:relative;
}
/* ── HEADER ── */
.hdr{display:flex;align-items:center;justify-content:space-between;padding-bottom:6px;border-bottom:2px solid #2d6385;margin-bottom:6px;}
.hdr-logo{flex:0 0 auto;}
.hdr-logo img{max-width:210px;max-height:65px;object-fit:contain;display:block;}
.logo-txt{font-family:'Outfit',sans-serif;font-size:24px;font-weight:800;color:#1e3a8a;text-transform:uppercase;letter-spacing:0.8px;line-height:1;border-left:4px solid #2d6385;padding-left:10px;}
.hdr-info{text-align:right;display:flex;flex-direction:column;align-items:flex-end;gap:3px;}
.hdr-info .co-name{font-family:'Outfit',sans-serif;font-size:18px;font-weight:800;color:#1e3a8a;text-transform:uppercase;letter-spacing:0.4px;margin-bottom:1px;line-height:1.2;}
.hdr-info .co-line{font-size:10.5px;color:#334155;font-weight:500;line-height:1.4;display:inline-flex;align-items:center;gap:6px;}
.hdr-badge{display:inline-flex;align-items:center;gap:6px;background:#f1f5f9;border:1px solid #cbd5e1;padding:2px 8px;border-radius:4px;font-size:10.5px;margin-top:2px;}
.badge-ntn-tag{background:#1e3a8a;color:#fff;font-size:9px;font-weight:800;padding:1.5px 6px;border-radius:3px;letter-spacing:0.5px;text-transform:uppercase;font-family:'Outfit',sans-serif;}
.badge-val{font-weight:700;color:#0f172a;font-size:10.5px;}
/* ── TITLE ── */
.inv-title{text-align:center;padding:6px 0 10px;margin-bottom:8px;}
.inv-title span{font-family:'Outfit',sans-serif;font-size:22px;font-weight:800;color:#1e3a8a;letter-spacing:7px;text-transform:uppercase;padding-bottom:3px;border-bottom:2.5px solid #2d6385;display:inline-block;}
/* ── INFO TABLE ── */
.info-tbl{width:100%;border-collapse:collapse;margin-bottom:8px;}
.info-tbl td{padding:4px 8px;font-size:10.5px;border:1px solid #b0bec5;}
.info-tbl .lbl{font-weight:700;background:#1e3a8a;color:#fff;white-space:nowrap;width:1%;}
.info-tbl .val{font-weight:600;color:#0f172a;}
/* ── PARTY BOX ── */
.party-section{border:1.5px solid #2d6385;border-radius:4px;padding:7px 10px;margin-bottom:8px;}
.party-section .sec-title{font-size:9.5px;font-weight:800;text-transform:uppercase;letter-spacing:0.06em;color:#1e3a8a;margin-bottom:5px;}
.party-section .prow{display:flex;gap:6px;font-size:10.5px;margin-bottom:2px;}
.party-section .plabel{font-weight:700;min-width:100px;color:#334155;}
.party-section .pval{font-weight:600;color:#0f172a;}
/* ── SECTION TITLE ── */
.sec-lbl{font-size:9.5px;font-weight:800;text-transform:uppercase;letter-spacing:0.06em;color:#1e3a8a;margin:8px 0 4px;border-bottom:1.5px solid #2d6385;padding-bottom:3px;}
/* ── TABLE ── */
.pay-tbl{width:100%;border-collapse:collapse;font-size:10.5px;margin-bottom:6px;}
.pay-tbl thead tr{background:#1e3a8a;}
.pay-tbl thead th{color:#fff;padding:5px 8px;font-weight:700;font-size:9.5px;text-transform:uppercase;letter-spacing:0.05em;border:none;}
.pay-tbl tbody tr{border-bottom:1px solid #e2e8f0;}
.pay-tbl tbody tr:last-child{border-bottom:none;}
.pay-tbl tbody td{padding:5px 8px;vertical-align:middle;}
.pay-tbl tfoot td{padding:5px 8px;font-weight:700;border-top:2px solid #1e3a8a;background:#f1f5f9;}
/* ── SUMMARY ── */
.summary-wrap{display:flex;justify-content:flex-end;margin-bottom:8px;}
.summary-inner{border:1.5px solid #2d6385;border-radius:4px;padding:6px 12px;min-width:240px;}
.summary-inner table{width:100%;border-collapse:collapse;font-size:10.5px;}
.summary-inner td{padding:3px 4px;}
.summary-inner td:last-child{text-align:right;font-weight:700;color:#0f172a;}
.summary-inner .lbl-col{font-weight:600;color:#334155;}
.summary-inner tr.total-row td{font-size:12px;font-weight:800;color:#1e3a8a;border-top:1.5px solid #2d6385;padding-top:5px;}
/* ── AMOUNT WORDS ── */
.words-box{background:#f8fafc;border:1px dashed #94a3b8;border-radius:4px;padding:5px 10px;font-size:10px;font-style:italic;font-weight:600;margin-bottom:8px;color:#334155;}
/* ── FOOTER ── */
.vch-footer{display:flex;justify-content:space-between;font-size:10px;color:#64748b;border-top:1px solid #e2e8f0;padding-top:5px;margin-top:6px;}
.vch-footer .thank{font-weight:700;color:#1e3a8a;}
.sig-row{display:flex;justify-content:space-between;margin-top:20px;}
.sig-box{text-align:center;width:30%;}
.sig-line{border-top:1.5px solid #334155;margin-bottom:3px;}
.sig-label{font-size:9.5px;font-weight:700;color:#334155;text-transform:uppercase;letter-spacing:0.04em;}
@media print{
    .no-print{display:none!important;}
    body{background:#fff;padding:0;}
    .pg{margin:0;box-shadow:none;padding:8mm 10mm;width:100%;}
}
</style>
</head>
<body>
@php
    $coName  = \App\Models\Setting::get('company_name', 'LOGIC TECH ENGINEERING');
    $coEmail = \App\Models\Setting::get('company_email', 'info@logictech.com.pk');
    $coPhone = \App\Models\Setting::get('company_phone', '92 300 5308035, 92 336 1500082');
    $coNtn   = \App\Models\Setting::get('company_ntn', '5561761-4');
    $coLogo  = \App\Models\Setting::getLogoUrl();

    // Party name
    $partyName = '-';
    $partyPhone = '';
    $partyAddr  = '';
    if ($party) {
        if (in_array($voucher->type, ['customer','walkin'])) {
            $partyName  = $party->customer_name ?? $party->name ?? '-';
            $partyPhone = $party->mobile ?? '';
            $partyAddr  = $party->address ?? '';
        } elseif ($voucher->type === 'vendor') {
            $partyName  = $party->name ?? '-';
            $partyPhone = $party->phone ?? '';
        } else {
            $partyName = $party->title ?? $party->name ?? '-';
        }
    }

    // Balance
    $balanceAfter = $previousBalance - $voucher->total_amount;

    // Amount in words
    if (!function_exists('numWordsRv')) {
        function numWordsRv($n) {
            $d=[0=>'Zero',1=>'One',2=>'Two',3=>'Three',4=>'Four',5=>'Five',6=>'Six',7=>'Seven',8=>'Eight',9=>'Nine',10=>'Ten',11=>'Eleven',12=>'Twelve',13=>'Thirteen',14=>'Fourteen',15=>'Fifteen',16=>'Sixteen',17=>'Seventeen',18=>'Eighteen',19=>'Nineteen',20=>'Twenty',30=>'Thirty',40=>'Forty',50=>'Fifty',60=>'Sixty',70=>'Seventy',80=>'Eighty',90=>'Ninety',100=>'Hundred',1000=>'Thousand',1000000=>'Million',1000000000=>'Billion'];
            $v=(float)$n; [$ip,$fp]=explode('.',number_format($v,2,'.',''));
            $x=(int)$ip;
            $cg=function($x)use(&$cg,$d){
                if($x<21)return $d[$x];
                if($x<100){$t=((int)($x/10))*10;$u=$x%10;return $d[$t].($u?'-'.$d[$u]:'');}
                if($x<1000){$h=(int)($x/100);$r=$x%100;return $d[$h].' '.$d[100].($r?' '.$cg($r):'');}
                if($x<1e6){$t=(int)($x/1000);$r=$x%1000;return $cg($t).' '.$d[1000].($r?' '.$cg($r):'');}
                $m=(int)($x/1e6);$r=$x%1e6;return $cg($m).' '.$d[1000000].($r?' '.$cg($r):'');
            };
            $w=$x===0?'Zero':$cg($x);
            $f=(int)$fp;
            return $f>0?trim($w).' Rupees and '.trim($cg($f)).' Paisas Only':trim($w).' Only';
        }
    }
    $amtWords = numWordsRv($voucher->total_amount);
@endphp

<div class="no-print">
    <button onclick="window.print()">&#128438; Print Voucher</button>
    <a href="javascript:history.back()">&#8592; Back</a>
</div>

<div class="pg">

    {{-- ── HEADER ── --}}
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
            @if($coEmail)
            <div class="co-line">
                <svg width="11" height="11" viewBox="0 0 20 20" fill="none"><rect x="1" y="4" width="18" height="12" rx="2" stroke="#2d6385" stroke-width="1.5"/><path d="M1 7l9 5 9-5" stroke="#2d6385" stroke-width="1.5"/></svg>
                {{ $coEmail }}
            </div>
            @endif
            @if($coPhone)
            <div class="co-line">
                <svg width="11" height="11" viewBox="0 0 20 20" fill="none"><path d="M6.5 2h7l1 4-3 1.5c.5 1.5 1.5 3 3 3.5L16 8l4 1v7c0 1.1-.9 2-2 2C4 18 2 6.9 2 4c0-1.1.9-2 2-2h2.5z" stroke="#2d6385" stroke-width="1.3"/></svg>
                {{ $coPhone }}
            </div>
            @endif
            @if($coNtn)
            <div class="hdr-badge">
                <span class="badge-ntn-tag">NTN</span>
                <span class="badge-val">{{ $coNtn }}</span>
            </div>
            @endif
        </div>
    </div>

    {{-- ── TITLE ── --}}
    <div class="inv-title">
        <span>Receipt Voucher</span>
    </div>

    {{-- ── VOUCHER INFO ── --}}
    <table class="info-tbl">
        <tr>
            <td class="lbl">Voucher No.</td>
            <td class="val">{{ $voucher->rvid }}</td>
            <td class="lbl">Receipt Date</td>
            <td class="val">{{ \Carbon\Carbon::parse($voucher->receipt_date)->format('d-M-Y') }}</td>
        </tr>
        <tr>
            <td class="lbl">Type</td>
            <td class="val" style="text-transform:capitalize;">{{ $voucher->type }}</td>
            <td class="lbl">Entry Date</td>
            <td class="val">{{ \Carbon\Carbon::parse($voucher->entry_date ?? $voucher->created_at)->format('d-M-Y') }}</td>
        </tr>
        @if(!empty($voucher->remarks))
        <tr>
            <td class="lbl">Remarks</td>
            <td class="val" colspan="3">{{ $voucher->remarks }}</td>
        </tr>
        @endif
    </table>

    {{-- ── PARTY ── --}}
    @if($party)
    <div class="party-section">
        <div class="sec-title">Received From</div>
        <div class="prow">
            <div class="plabel">Name:</div>
            <div class="pval"><strong>{{ $partyName }}</strong></div>
        </div>
        @if($partyPhone)
        <div class="prow">
            <div class="plabel">Phone:</div>
            <div class="pval">{{ $partyPhone }}</div>
        </div>
        @endif
        @if($partyAddr)
        <div class="prow">
            <div class="plabel">Address:</div>
            <div class="pval">{{ $partyAddr }}</div>
        </div>
        @endif
    </div>
    @endif

    {{-- ── PAYMENT ROWS TABLE ── --}}
    <div class="sec-lbl">Payment Details</div>
    <table class="pay-tbl">
        <thead>
            <tr>
                <th style="width:38px;text-align:center;">#</th>
                <th>Account</th>
                <th style="text-align:right;width:140px;">Amount (Rs.)</th>
            </tr>
        </thead>
        <tbody>
            @foreach($rows as $key => $row)
            <tr>
                <td style="text-align:center;color:#64748b;">{{ $key + 1 }}</td>
                <td><strong>{{ $row['account_name'] ?? '-' }}</strong></td>
                <td style="text-align:right;font-weight:700;">{{ number_format($row['amount'], 2) }}</td>
            </tr>
            @endforeach
        </tbody>
        <tfoot>
            <tr>
                <td colspan="2" style="text-align:right;">Total Received:</td>
                <td style="text-align:right;color:#1e3a8a;font-size:11.5px;">
                    Rs. {{ number_format($voucher->total_amount, 2) }}
                </td>
            </tr>
        </tfoot>
    </table>

    {{-- Amount in words --}}
    <div class="words-box">
        Amount in Words: <strong>{{ $amtWords }}</strong>
    </div>

    {{-- ── BALANCE SUMMARY ── --}}
    <div class="summary-wrap">
        <div class="summary-inner">
            <table>
                <tr>
                    <td class="lbl-col">Previous Balance</td>
                    <td>{{ number_format(abs($previousBalance), 2) }} {{ $previousBalance >= 0 ? 'Dr' : 'Cr' }}</td>
                </tr>
                <tr>
                    <td class="lbl-col">Amount Received (−)</td>
                    <td style="color:#16a34a;">{{ number_format($voucher->total_amount, 2) }} Cr</td>
                </tr>
                <tr class="total-row">
                    <td class="lbl-col">Balance Remaining</td>
                    <td style="color:{{ $balanceAfter >= 0 ? '#dc2626' : '#16a34a' }};">
                        {{ number_format(abs($balanceAfter), 2) }} {{ $balanceAfter >= 0 ? 'Dr' : 'Cr' }}
                    </td>
                </tr>
            </table>
        </div>
    </div>

    {{-- ── SIGNATURES ── --}}
    <div class="sig-row">
        <div class="sig-box">
            <div class="sig-line"></div>
            <div class="sig-label">Received By</div>
        </div>
        <div class="sig-box">
            <div class="sig-line"></div>
            <div class="sig-label">Prepared By</div>
        </div>
        <div class="sig-box">
            <div class="sig-line"></div>
            <div class="sig-label">Authorized Signatory</div>
        </div>
    </div>

    {{-- ── FOOTER ── --}}
    <div class="vch-footer">
        <span>Printed: {{ now()->format('d/m/Y') }} at {{ now()->format('H:i') }}</span>
        <span class="thank">Thank You ✓</span>
    </div>

</div>
</body>
</html>