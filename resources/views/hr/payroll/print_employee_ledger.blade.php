<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Employee Ledger Statement - {{ $selectedEmployee ? $selectedEmployee->full_name : '' }}</title>
    <style>
        @page {
            size: A4 portrait;
            margin: 10mm;
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
        
        .header {
            text-align: center;
            border-bottom: 2px solid #1f497d;
            padding-bottom: 10px;
            margin-bottom: 15px;
        }
        .header h2 {
            margin: 0;
            color: #1f497d;
            font-size: 20px;
        }
        .header p {
            margin: 3px 0 0 0;
            color: #475569;
            font-weight: bold;
            font-size: 12px;
        }
        
        .emp-info {
            width: 100%;
            margin-bottom: 15px;
            border-collapse: collapse;
        }
        .emp-info td {
            padding: 4px 8px;
            font-size: 11px;
        }
        
        .ledger-table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }
        .ledger-table th, .ledger-table td {
            border: 1px solid #000;
            padding: 6px 8px;
            font-size: 10px;
        }
        .ledger-table th {
            background-color: #8db4e2;
            color: #000;
            font-weight: bold;
            text-align: center;
        }
        
        .total-row td {
            font-weight: bold;
            background-color: #f2f2f2;
        }

        .no-print {
            margin-bottom: 15px;
            text-align: center;
        }
        @media print {
            .no-print { display: none; }
        }
    </style>
</head>
<body>

    <div class="no-print">
        <button onclick="window.print()" style="padding: 8px 18px; background: #1f497d; color: white; border: none; border-radius: 4px; font-weight: bold; cursor: pointer;">
            🖨️ Print Employee Statement
        </button>
    </div>

    @if($selectedEmployee)
        <div class="header">
            <h2>LOGICTECH SOFTWARE</h2>
            <p>EMPLOYEE LEDGER & SALARY / ADVANCE STATEMENT</p>
        </div>

        <table class="emp-info">
            <tr>
                <td style="width: 15%;" class="fw-bold">Employee Name:</td>
                <td style="width: 35%;"><strong>{{ strtoupper($selectedEmployee->full_name) }}</strong></td>
                <td style="width: 15%;" class="fw-bold">Statement Period:</td>
                <td style="width: 35%;">{{ $startDate }} to {{ $endDate }}</td>
            </tr>
            <tr>
                <td class="fw-bold">Designation:</td>
                <td>{{ $selectedEmployee->designation->name ?? 'Staff' }}</td>
                <td class="fw-bold">Opening Balance:</td>
                <td>Rs. {{ number_format($summary['opening_balance'], 0) }}</td>
            </tr>
            <tr>
                <td class="fw-bold">Department:</td>
                <td>{{ $selectedEmployee->department->name ?? 'General' }}</td>
                <td class="fw-bold">Closing Balance:</td>
                <td><strong>Rs. {{ number_format($summary['closing_balance'], 0) }}</strong></td>
            </tr>
        </table>

        <table class="ledger-table">
            <thead>
                <tr>
                    <th style="width: 70px;">Date</th>
                    <th style="width: 70px;">Ref #</th>
                    <th style="width: 130px;">Type</th>
                    <th>Description</th>
                    <th style="width: 90px;">Advances Given</th>
                    <th style="width: 90px;">Salary Earned</th>
                    <th style="width: 90px;">Advances Deducted</th>
                    <th style="width: 90px;">Net Paid</th>
                    <th style="width: 100px;">Running Balance</th>
                </tr>
            </thead>
            <tbody>
                <tr class="fw-bold" style="background: #f8fafc;">
                    <td>{{ $startDate }}</td>
                    <td>-</td>
                    <td>Opening</td>
                    <td>Opening Advance Balance</td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right">-</td>
                    <td class="text-right">Rs. {{ number_format($summary['opening_balance'], 0) }}</td>
                </tr>

                @foreach($ledgerEntries as $entry)
                    <tr>
                        <td>{{ $entry['date'] }}</td>
                        <td class="fw-bold">{{ $entry['ref'] }}</td>
                        <td class="fw-bold">{{ $entry['type'] }}</td>
                        <td>{{ $entry['description'] }}</td>
                        <td class="text-right">{{ $entry['advance_given'] > 0 ? number_format($entry['advance_given'], 0) : '-' }}</td>
                        <td class="text-right">{{ $entry['salary_earned'] > 0 ? number_format($entry['salary_earned'], 0) : '-' }}</td>
                        <td class="text-right">{{ $entry['advance_deducted'] > 0 ? number_format($entry['advance_deducted'], 0) : '-' }}</td>
                        <td class="text-right fw-bold">{{ $entry['net_paid'] > 0 ? number_format($entry['net_paid'], 0) : '-' }}</td>
                        <td class="text-right fw-bold">Rs. {{ number_format($entry['running_balance'], 0) }}</td>
                    </tr>
                @endforeach

                <tr class="total-row">
                    <td colspan="4" class="text-left">TOTALS</td>
                    <td class="text-right">{{ number_format($summary['total_advances_given'], 0) }}</td>
                    <td class="text-right">{{ number_format($summary['total_salary_earned'], 0) }}</td>
                    <td class="text-right">{{ number_format($summary['total_advances_deducted'], 0) }}</td>
                    <td class="text-right">{{ number_format($summary['total_net_paid'], 0) }}</td>
                    <td class="text-right">Rs. {{ number_format($summary['closing_balance'], 0) }}</td>
                </tr>
            </tbody>
        </table>
    @else
        <div class="text-center p-5">
            <p>No Employee Selected.</p>
        </div>
    @endif

</body>
</html>
