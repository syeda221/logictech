<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Payroll Summary - {{ \Carbon\Carbon::parse($month.'-01')->format('M-y') }}</title>
    <style>
        @page {
            size: A4 landscape;
            margin: 8mm;
        }
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 10px;
            color: #000;
            background: #fff;
            font-size: 11px;
        }
        .text-center { text-align: center; }
        .text-right { text-align: right; }
        .text-left { text-align: left; }
        .fw-bold { font-weight: bold; }
        
        .excel-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .excel-table th, .excel-table td {
            border: 1px solid #000;
            padding: 5px 6px;
            text-align: center;
            font-size: 11px;
        }
        
        /* Banner Header */
        .main-header {
            background-color: #1f497d;
            color: #fff;
            font-size: 18px;
            font-weight: bold;
            padding: 8px;
            text-align: center;
        }
        .sub-header {
            background-color: #8db4e2;
            color: #000;
            font-size: 14px;
            font-weight: bold;
            padding: 5px;
            text-align: center;
        }
        
        .excel-table th {
            background-color: #ffffff;
            font-weight: bold;
        }
        
        /* Highlighted Columns from Excel */
        .bg-total-pay {
            background-color: #8db4e2 !important;
            font-weight: bold;
        }
        .bg-net-payable {
            background-color: #b8cce4 !important;
            font-weight: bold;
        }
        .total-row td {
            font-weight: bold;
            background-color: #f2f2f2;
        }

        .no-print {
            margin-bottom: 15px;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>
    <div class="no-print text-center">
        <button onclick="window.print()" style="padding: 10px 20px; background: #1f497d; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">
            🖨️ Print Payroll Summary
        </button>
    </div>

    <table class="excel-table">
        <thead>
            <tr>
                <th colspan="15" class="main-header">Payroll Summary</th>
            </tr>
            <tr>
                <th colspan="15" class="sub-header">{{ \Carbon\Carbon::parse($month.'-01')->format('M-y') }}</th>
            </tr>
            <tr style="background-color: #f2f2f2;">
                <th style="width: 30px;">SN</th>
                <th style="width: 140px;" class="text-left">Name</th>
                <th>P-Days</th>
                <th>Basic Salary</th>
                <th>Salary Count this month</th>
                <th>Overtime Days</th>
                <th>Overtime Pay</th>
                <th class="bg-total-pay">Total Pay</th>
                <th>Previous Balance</th>
                <th>Advances this month</th>
                <th>Other Allowance /Eidi</th>
                <th class="bg-net-payable">Net Payable this month</th>
                <th>Payment this month</th>
                <th>Closing Balance</th>
                <th>Payment Date</th>
            </tr>
        </thead>
        <tbody>
            @foreach($payrollItems as $item)
                <tr>
                    <td>{{ $item['sn'] }}</td>
                    <td class="text-left fw-bold">{{ strtoupper($item['employee_name']) }}</td>
                    <td class="fw-bold">{{ number_format($item['p_days'], 0) }}</td>
                    <td class="fw-bold text-right">{{ number_format($item['basic_salary'], 0) }}</td>
                    <td class="text-right">{{ number_format($item['salary_count'], 0) }}</td>
                    <td>{{ $item['overtime_days'] > 0 ? number_format($item['overtime_days'], 1) : '' }}</td>
                    <td class="text-right">{{ $item['overtime_pay'] > 0 ? number_format($item['overtime_pay'], 0) : '-' }}</td>
                    <td class="bg-total-pay text-right">{{ number_format($item['total_pay'], 0) }}</td>
                    <td class="text-right fw-bold">@if($item['prev_closing'] > 0)<span style="color: #16a34a;">+{{ number_format($item['prev_closing'], 0) }}</span>@elseif($item['prev_closing'] < 0)<span style="color: #dc2626;">-{{ number_format(abs($item['prev_closing']), 0) }}</span>@else-@endif</td>
                    <td class="text-right">{{ $item['advances'] > 0 ? '-' . number_format($item['advances'], 0) : '-0' }}</td>
                    <td class="text-right">{{ $item['other_allowance'] > 0 ? number_format($item['other_allowance'], 0) : '-' }}</td>
                    <td class="bg-net-payable text-right">{{ number_format($item['net_payable'], 0) }}</td>
                    <td class="text-right fw-bold">{{ number_format($item['payment_this_month'], 0) }}</td>
                    <td class="text-right">{{ $item['closing_balance'] != 0 ? number_format($item['closing_balance'], 0) : '-' }}</td>
                    <td>{{ $item['payment_date'] }}</td>
                </tr>
            @endforeach
            <tr class="total-row">
                <td colspan="2" class="fw-bold text-left">TOTAL</td>
                <td></td>
                <td></td>
                <td class="text-right">{{ number_format($totals['salary_count'], 0) }}</td>
                <td></td>
                <td class="text-right">{{ number_format($totals['overtime_pay'], 0) }}</td>
                <td class="bg-total-pay text-right">{{ number_format($totals['total_pay'], 0) }}</td>
                <td class="text-right fw-bold">@if($totals['prev_closing'] > 0)<span style="color: #16a34a;">+{{ number_format($totals['prev_closing'], 0) }}</span>@elseif($totals['prev_closing'] < 0)<span style="color: #dc2626;">-{{ number_format(abs($totals['prev_closing']), 0) }}</span>@else-@endif</td>
                <td class="text-right">{{ number_format($totals['advances'], 0) }}</td>
                <td class="text-right">{{ number_format($totals['other_allowance'], 0) }}</td>
                <td class="bg-net-payable text-right">{{ number_format($totals['net_payable'], 0) }}</td>
                <td class="text-right">{{ number_format($totals['payment_this_month'], 0) }}</td>
                <td class="text-right">{{ $totals['closing_balance'] != 0 ? number_format($totals['closing_balance'], 0) : '-' }}</td>
                <td></td>
            </tr>
        </tbody>
    </table>



</body>
</html>
