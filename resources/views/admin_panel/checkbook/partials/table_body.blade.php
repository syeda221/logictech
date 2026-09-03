@foreach ($transactions as $txn)
    <tr>
        <td class="text-secondary fw-medium">{{ \Carbon\Carbon::parse($txn->entry_date)->format('d/m/Y') }}</td>
        <td class="text-secondary fw-medium">
            @php
                $desc = $txn->description ?? '-';
                if ($txn->source && method_exists($txn->source, 'party') && $txn->source->party) {
                    $party = $txn->source->party;
                    if (class_basename($party) === 'Customer') {
                        $customerName = $party->customer_name ?? null;
                        if ($customerName) {
                            if (preg_match('/Payment received from (Invoice\s*#[A-Za-z0-9\-]+)/i', $desc, $matches)) {
                                $desc = "Payment received from {$customerName} against {$matches[1]}";
                            } elseif (isset($txn->source->voucher_type) && $txn->source->voucher_type === 'receipt' && $txn->debit > 0) {
                                $desc = "Receipt received from {$customerName}";
                            }
                        }
                    } elseif (class_basename($party) === 'Vendor') {
                        $vendorName = $party->name ?? null;
                        if ($vendorName) {
                            if (preg_match('/Payment for (Purchase\s*#[A-Za-z0-9\-]+)/i', $desc, $matches)) {
                                $desc = "Payment made to {$vendorName} against {$matches[1]}";
                            } elseif (isset($txn->source->voucher_type) && $txn->source->voucher_type === 'payment' && $txn->credit > 0) {
                                $desc = "Payment made to {$vendorName}";
                            }
                        }
                    }
                }
            @endphp
            {{ $desc }}
        </td>
        <td class="text-secondary fw-medium">
            <span class="badge bg-light text-dark border">
                {{ $txn->account->title ?? 'Unknown' }}
            </span>
        </td>
        <td class="text-secondary fw-medium">
            @if($txn->source)
                @php
                    $sourceType = class_basename($txn->source_type);
                @endphp
                <span class="badge bg-info text-white">
                    {{ $sourceType }} #{{ $txn->source_id }}
                </span>
            @else
                -
            @endif
        </td>
        <td class="text-end fw-bold text-success">
            {{ $txn->debit > 0 ? number_format($txn->debit, 2) : '-' }}
        </td>
        <td class="text-end fw-bold text-danger">
            {{ $txn->credit > 0 ? number_format($txn->credit, 2) : '-' }}
        </td>
        <td class="text-end fw-bold {{ $txn->running_balance >= 0 ? 'text-primary' : 'text-danger' }}">
            {{ number_format($txn->running_balance, 2) }}
        </td>
    </tr>
@endforeach
