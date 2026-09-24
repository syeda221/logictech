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
            margin-bottom: 25px;
        }
        .salary-table th {
            background-color: #1f497d;
            color: white;
            padding: 10px;
            text-align: left;
        }
        .salary-table td {
            border: 1px solid #cbd5e1;
            padding: 10px;
        }
        .text-right { text-align: right; }
        .text-center { text-align: center; }
        .total-row td {
            font-weight: bold;
            background-color: #f1f5f9;
            font-size: 14px;
        }
        .net-pay-box {
            background: #8db4e2;
            color: #000;
            padding: 15px;
            border-radius: 6px;
            text-align: center;
            font-size: 18px;
            font-weight: bold;
            margin-bottom: 30px;
        }
        .signatures {
            display: flex;
            justify-content: space-between;
            margin-top: 50px;
            padding-top: 20px;
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

        <table class="salary-table">
            <thead>
                <tr>
                    <th>Earnings & Pay Breakdown</th>
                    <th class="text-right">Amount (PKR)</th>
                    <th>Deductions & Adjustments</th>
                    <th class="text-right">Amount (PKR)</th>
                </tr>
            </thead>
            <tbody>
                <tr>
                    <td>Basic Salary</td>
                    <td class="text-right">Rs. {{ number_format($payroll->basic_salary, 0) }}</td>
                    <td>Advances / Loan Deductions</td>
                    <td class="text-right text-danger">Rs. {{ number_format($payroll->advances ?: $payroll->deductions, 0) }}</td>
                </tr>
                <tr>
                    <td>Salary Count (For {{ number_format($payroll->p_days, 0) }} Days)</td>
                    <td class="text-right">Rs. {{ number_format($payroll->salary_count, 0) }}</td>
                    <td>Other Deductions</td>
                    <td class="text-right">Rs. 0</td>
                </tr>
                <tr>
                    <td>Overtime Pay</td>
                    <td class="text-right">Rs. {{ number_format($payroll->overtime_pay, 0) }}</td>
                    <td>Remaining Advance Balance</td>
                    <td class="text-right">Rs. {{ number_format(abs($payroll->closing_balance), 0) }}</td>
                </tr>
                <tr>
                    <td>Other Allowances / Eidi</td>
                    <td class="text-right">Rs. {{ number_format($payroll->other_allowance ?: $payroll->manual_allowances, 0) }}</td>
                    <td></td>
                    <td></td>
                </tr>
                <tr class="total-row">
                    <td>TOTAL PAY</td>
                    <td class="text-right">Rs. {{ number_format($payroll->total_pay ?: $payroll->gross_salary, 0) }}</td>
                    <td>TOTAL DEDUCTIONS</td>
                    <td class="text-right">Rs. {{ number_format($payroll->advances ?: $payroll->deductions, 0) }}</td>
                </tr>
            </tbody>
        </table>

        <div class="net-pay-box">
            NET AMOUNT PAID: Rs. {{ number_format($payroll->payment_this_month ?: $payroll->net_salary, 0) }}
        </div>

        <div class="signatures">
            <div class="sig-line">Employee Signature</div>
            <div class="sig-line">Authorized Signature</div>
        </div>
    </div>

</body>
</html>
