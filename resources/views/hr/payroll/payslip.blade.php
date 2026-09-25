<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payslip - {{ $payroll->employee->full_name }} ({{ \Carbon\Carbon::parse($payroll->month.'-01')->format('M Y') }})</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 15mm;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 20px;
            color: #1e293b;
            font-size: 13px;
        }
        .payslip-box {
            border: 2px solid #1f497d;
            border-radius: 8px;
            padding: 25px;
            max-width: 800px;
            margin: 0 auto;
            background: #ffffff;
        }
        .header {
            text-align: center;
            border-bottom: 2px solid #1f497d;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        .header h2 {
            margin: 0;
            color: #1f497d;
            font-size: 24px;
        }
        .header p {
            margin: 5px 0 0 0;
            color: #64748b;
            font-weight: bold;
        }
        .info-table {
            width: 100%;
            margin-bottom: 20px;
            border-collapse: collapse;
        }
        .info-table td {
            padding: 6px 10px;
        }
        .info-label {
            font-weight: bold;
            color: #475569;
            width: 20%;
        }
        .info-val {
            color: #0f172a;
            width: 30%;
        }
        .salary-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .salary-table th {
            background-color: #1f497d;
            color: white;
            padding: 10px;
            text-align: left;
            font-weight: bold;
        }
        .salary-table td {
            border: 1px solid #cbd5e1;
            padding: 9px 10px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        
        .total-row td {
            font-weight: bold;
            background-color: #fde8e1;
            color: #000;
            font-size: 13px;
        }
        
        .net-pay-box {
            background: #8db4e2;
            color: #000;
            padding: 12px;
            border-radius: 4px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 12px;
            letter-spacing: 0.5px;
        }
        .balance-box {
            text-align: center;
            font-size: 15px;
            font-weight: bold;
            color: #000;
            margin-bottom: 25px;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 45px;
            padding-top: 15px;
        }
        .sig-line {
            border-top: 1px solid #94a3b8;
            width: 200px;
            text-align: center;
            padding-top: 5px;
            font-weight: bold;
            color: #475569;
        }
        .no-print {
            text-align: center;
            margin-bottom: 20px;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" style="padding: 10px 20px; background: #1f497d; color: white; border: none; border-radius: 6px; font-weight: bold; cursor: pointer;">
            🖨️ Print Payslip
        </button>
    </div>

    <div class="payslip-box">
        <div class="header" style="display: flex; align-items: center; justify-content: space-between; border-bottom: 2px solid #1f497d; padding-bottom: 15px; margin-bottom: 20px;">
            <div style="flex: 0 0 140px; text-align: left;">
                @php
                    $logoUrl = \App\Models\Setting::getLogoUrl();
                    if (!$logoUrl && file_exists(base_path('logo.png'))) {
                        $type = pathinfo(base_path('logo.png'), PATHINFO_EXTENSION);
                        $data = file_get_contents(base_path('logo.png'));
                        $logoUrl = 'data:image/' . $type . ';base64,' . base64_encode($data);
                    }
                @endphp
                @if($logoUrl)
                    <img src="{{ $logoUrl }}" alt="Logo" style="max-height: 60px; max-width: 140px; object-fit: contain;">
                @endif
            </div>
            <div style="flex: 1; text-align: center;">
                <h2 style="margin: 0; color: #1f497d; font-size: 24px; font-weight: bold; text-transform: uppercase;">LOGIC TECH ENGINEERING</h2>
                <p style="margin: 5px 0 0 0; color: #64748b; font-weight: bold;">SALARY PAYSLIP - {{ \Carbon\Carbon::parse($payroll->month.'-01')->format('F Y') }}</p>
            </div>
            <div style="flex: 0 0 140px;"></div>
        </div>

        <table class="info-table">
            <tr>
                <td class="info-label">Employee Name:</td>
                <td class="info-val"><strong>{{ strtoupper($payroll->employee->full_name) }}</strong></td>
                <td class="info-label">Payment Date:</td>
                <td class="info-val">{{ $payroll->payment_date ? $payroll->payment_date->format('d M, Y') : date('d M, Y') }}</td>
            </tr>
            <tr>
                <td class="info-label">Designation:</td>
                <td class="info-val">{{ $payroll->employee->designation->name ?? 'Staff' }}</td>
                <td class="info-label">Payment Account:</td>
                <td class="info-val">{{ $payroll->account ? $payroll->account->name : 'Cash' }}</td>
            </tr>
            <tr>
                <td class="info-label">Present Days:</td>
                <td class="info-val">{{ number_format($payroll->p_days, 0) }} Days</td>
                <td class="info-label">Overtime Hours:</td>
                <td class="info-val">{{ number_format($payroll->overtime_hours, 1) }} Hrs ({{ number_format($payroll->overtime_days, 2) }} Days)</td>
            </tr>
        </table>

        @php
            $basicSalary    = (float) $payroll->basic_salary;
            $pDays          = (float) $payroll->p_days;
            $salaryCount    = (float) ($payroll->salary_count ?: (($basicSalary / 30) * $pDays));
            $overtimePay    = (float) $payroll->overtime_pay;
            $otherAllowance = (float) ($payroll->other_allowance ?: $payroll->manual_allowances ?: 0);
            $totalPay       = (float) ($payroll->total_pay ?: ($salaryCount + $overtimePay + $otherAllowance));

            $advances        = (float) ($payroll->advances ?: $payroll->deductions ?: 0);
            $otherDeduction  = 0;
            $totalDeductions = $advances + $otherDeduction;

            $netPayable  = (float) ($payroll->net_salary ?: ($totalPay - $totalDeductions));
            $paidAmount  = (float) ($payroll->payment_this_month ?: $netPayable);
            $balanceAmt  = $payroll->closing_balance !== null ? (float)$payroll->closing_balance : ($netPayable - $paidAmount);
        @endphp

        <table class="salary-table">
            <thead>
                <tr>
                    <th style="width:35%;">Earnings & Pay Breakdown</th>
                    <th class="text-right" style="width:15%;">Amount (PKR)</th>
                    <th style="width:35%;">Deductions & Adjustments</th>
                    <th class="text-right" style="width:15%;">Amount (PKR)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Basic Salary</td>
                    <td class="text-right">Rs. {{ number_format($basicSalary, 0) }}</td>
                    <td>Advances / Loan Deductions</td>
                    <td class="text-right">Rs. {{ number_format($advances, 0) }}</td>
                </tr>
                <tr>
                    <td>Salary Count (For {{ number_format($pDays, 0) }} Days)</td>
                    <td class="text-right">Rs. {{ number_format($salaryCount, 0) }}</td>
                    <td>Other Deductions</td>
                    <td class="text-right">Rs. {{ number_format($otherDeduction, 0) }}</td>
                </tr>
                <tr>
                    <td>Overtime Pay</td>
                    <td class="text-right">Rs. {{ number_format($overtimePay, 0) }}</td>
                    <td><strong>Total Deductions</strong></td>
                    <td class="text-right"><strong>Rs. {{ number_format($totalDeductions, 0) }}</strong></td>
                </tr>
                <tr>
                    <td>Other Allowances (Eidi)</td>
                    <td class="text-right">Rs. {{ number_format($otherAllowance, 0) }}</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="total-row">
                    <td>TOTAL PAY</td>
                    <td class="text-right">Rs. {{ number_format($totalPay, 0) }}</td>
                    <td>NET PAYABLE</td>
                    <td class="text-right">Rs. {{ number_format($netPayable, 0) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="net-pay-box">
            NET PAID AMOUNT : Rs. {{ number_format($paidAmount, 0) }}
        </div>

        <div class="balance-box">
            BALANCE AMOUNT : Rs. {{ number_format(abs($balanceAmt), 0) }}
            @if($balanceAmt > 0)
                <span style="color:#15803d;">(Payable)</span>
            @elseif($balanceAmt < 0)
                <span style="color:#b91c1c;">(Receivable)</span>
            @endif
        </div>

        <div class="signatures">
            <div class="sig-line">Employee Signature</div>
            <div class="sig-line">Authorized Signature</div>
        </div>
    </div>

</body>
</html>
