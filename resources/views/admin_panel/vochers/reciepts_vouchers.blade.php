@extends('admin_panel.layout.app')
@section('content')
<link rel="stylesheet" href="{{ asset('assets/vendors/bootstrap-icons/css/bootstrap-icons.min.css') }}">

<style>
    :root { --rv-primary:#4f46e5; --rv-border:#e2e8f0; --rv-text:#1e293b; --rv-muted:#64748b; }
    .rv-card { background:#fff; border-radius:12px; box-shadow:0 1px 4px rgba(0,0,0,0.07); border:1px solid var(--rv-border); padding:18px 20px; }
    .rv-page-header { display:flex; justify-content:space-between; align-items:center; margin-bottom:14px; padding-bottom:12px; border-bottom:1.5px solid var(--rv-border); }
    .rv-page-title { font-size:1.1rem; font-weight:700; color:var(--rv-text); display:flex; align-items:center; gap:9px; }
    .rv-page-title .title-icon { background:#e0e7ff; color:var(--rv-primary); width:32px; height:32px; border-radius:8px; display:flex; align-items:center; justify-content:center; font-size:0.95rem; }
    .rv-section-label { font-size:0.68rem; font-weight:700; color:var(--rv-primary); text-transform:uppercase; letter-spacing:0.06em; margin:10px 0 6px 0; display:flex; align-items:center; gap:7px; }
    .rv-section-label::after { content:''; flex:1; height:1px; background:var(--rv-border); }
    .rv-label { font-size:0.66rem; font-weight:700; text-transform:uppercase; letter-spacing:0.05em; color:var(--rv-muted); margin-bottom:3px; display:block; }
    .rv-input { background:#fff; border:1px solid var(--rv-border); border-radius:7px; padding:5px 9px; font-size:0.84rem; color:var(--rv-text); transition:border-color 0.15s,box-shadow 0.15s; width:100%; height:32px; }
    .rv-input:focus { border-color:var(--rv-primary); box-shadow:0 0 0 2px rgba(79,70,229,0.12); outline:none; }
    .rv-input::placeholder { color:#c4cdd6; }
    .rv-input[readonly] { background:#f8fafc; cursor:not-allowed; }
    select.rv-input { appearance:none; background-image:url("data:image/svg+xml,%3csvg xmlns='http://www.w3.org/2000/svg' fill='none' viewBox='0 0 20 20'%3e%3cpath stroke='%236b7280' stroke-linecap='round' stroke-linejoin='round' stroke-width='1.5' d='M6 8l4 4 4-4'/%3e%3c/svg%3e"); background-position:right 0.4rem center; background-repeat:no-repeat; background-size:1.1em 1.1em; padding-right:1.8rem; }
    .rv-balance { height:32px; border-radius:7px; font-weight:700; font-size:0.85rem; display:flex; align-items:center; justify-content:center; width:100%; }
    .rv-balance.dr { background:#fef2f2; color:#dc2626; border:1px solid #fca5a5; }
    .rv-balance.cr { background:#f0fdf4; color:#16a34a; border:1px solid #86efac; }
    .rv-table { border-radius:8px; overflow:hidden; border:1px solid var(--rv-border); }
    .rv-table thead th { background:#1e293b; color:#e2e8f0; font-size:0.68rem; font-weight:600; text-transform:uppercase; letter-spacing:0.05em; padding:7px 9px; border:none; white-space:nowrap; }
    .rv-table tbody td { padding:5px 7px; vertical-align:middle; border-color:var(--rv-border); font-size:0.82rem; }
    .rv-table tfoot td { padding:7px 9px; background:#f8fafc; border-color:var(--rv-border); font-size:0.83rem; }
    .btn-rv-save { background:var(--rv-primary); color:#fff; border:none; border-radius:7px; padding:6px 18px; font-weight:600; font-size:0.83rem; display:inline-flex; align-items:center; gap:5px; cursor:pointer; }
    .btn-rv-save:hover { background:#4338ca; color:#fff; }
    .btn-rv-list { background:#f1f5f9; color:var(--rv-text); border:1px solid var(--rv-border); border-radius:7px; padding:6px 14px; font-weight:600; font-size:0.83rem; display:inline-flex; align-items:center; gap:5px; text-decoration:none; }
    .btn-rv-list:hover { background:#e2e8f0; color:var(--rv-text); }
    .btn-rv-add { background:#f0fdf4; color:#059669; border:1.5px dashed #10b981; border-radius:7px; padding:4px 12px; font-weight:600; font-size:0.8rem; display:inline-flex; align-items:center; gap:4px; cursor:pointer; }
    .btn-rv-add:hover { background:#dcfce7; }
    .rv-info-bar { background:#eff6ff; border:1px solid #bfdbfe; border-radius:7px; padding:6px 11px; font-size:0.76rem; color:#1d4ed8; display:flex; align-items:center; gap:7px; margin-bottom:7px; }
    .rv-all-paid { background:#f0fdf4; border:1px solid #86efac; border-radius:7px; padding:6px 11px; font-size:0.76rem; color:#16a34a; display:none; align-items:center; gap:7px; margin-bottom:7px; }
    .rv-flash { border-radius:8px; padding:8px 13px; font-size:0.82rem; margin-bottom:9px; display:flex; align-items:center; gap:7px; }
    .rv-flash.ok  { background:#f0fdf4; border:1px solid #86efac; color:#16a34a; }
    .rv-flash.err { background:#fef2f2; border:1px solid #fca5a5; color:#dc2626; }
</style>

<div class="main-content">
  <div class="main-content-inner" style="padding:10px;">
    <div class="container-fluid p-0">

      @if(session('success'))
        <div class="rv-flash ok mb-2"><i class="bi bi-check-circle-fill"></i> {{ session('success') }}
          <button type="button" onclick="this.closest('.rv-flash').remove()" style="margin-left:auto;background:none;border:none;color:inherit;cursor:pointer;">&#x2715;</button></div>
      @endif
      @if(session('error'))
        <div class="rv-flash err mb-2"><i class="bi bi-x-circle-fill"></i> {{ session('error') }}</div>
      @endif

      <form action="{{ route('store_rec_vochers') }}" method="POST" id="receiptForm">
        @csrf
        <div class="rv-card">

          {{-- HEADER --}}
          <div class="rv-page-header">
            <div class="rv-page-title">
              <div class="title-icon"><i class="bi bi-receipt-cutoff"></i></div>
              Receipt Voucher
            </div>
            <div class="d-flex gap-2">
              <a href="{{ route('all_recepit_vochers') }}" class="btn-rv-list"><i class="bi bi-list-ul"></i> All Vouchers</a>
              <button type="submit" class="btn-rv-save"><i class="bi bi-check-lg"></i> Save Voucher</button>
            </div>
          </div>

          {{-- VOUCHER DETAILS --}}
          <div class="rv-section-label">Voucher Details</div>
          <div class="row g-2 mb-1">
            <div class="col-md-2">
              <label class="rv-label">RVID</label>
              <input type="text" class="rv-input" name="rvid" value="{{ $nextRvid }}" readonly>
            </div>
            <div class="col-md-2">
              <label class="rv-label">Receipt Date</label>
              <input type="date" name="receipt_date" class="rv-input" value="{{ now()->toDateString() }}">
            </div>
            <div class="col-md-2">
              <label class="rv-label">Entry Date</label>
              <input type="date" name="entry_date" class="rv-input" value="{{ now()->toDateString() }}">
            </div>
            <div class="col-md-6">
              <label class="rv-label">Remarks <span style="font-weight:400;text-transform:none;">(Optional)</span></label>
              <input type="text" name="remarks" class="rv-input" id="remarks" placeholder="Auto-generated if left blank">
            </div>
          </div>

          {{-- RECEIVED FROM --}}
          <div class="rv-section-label">Received From</div>
          <div class="row g-2 mb-1">
            <div class="col-md-2">
              <label class="rv-label">Type</label>
              <select name="vendor_type" class="rv-input" id="partyType">
                <option value="customer" selected>Customer</option>
                <option value="walkin">Walk-in</option>
                <option value="vendor">Vendor</option>
                @foreach ($AccountHeads as $head)
                  <option value="{{ $head->id }}">{{ $head->name }}</option>
                @endforeach
              </select>
            </div>
            <div class="col-md-4">
              <label class="rv-label">Party / Account</label>
              <select name="vendor_id" class="rv-input" id="partyId">
                <option disabled selected>Select Party</option>
              </select>
            </div>
            <div class="col-md-3">
              <label class="rv-label">Mobile</label>
              <input type="text" name="tel" id="tel" class="rv-input" readonly>
            </div>
            <div class="col-md-3">
              <label class="rv-label">Current Balance</label>
              <div id="balanceDisplay" class="rv-balance dr">0.00 <small style="font-weight:500;margin-left:2px;">Dr</small></div>
              <input type="hidden" id="openingBal">
            </div>
          </div>

          {{-- OUTSTANDING INVOICES --}}
          <div id="outstandingSection" style="display:none;">
            <div class="rv-section-label">
              <i class="bi bi-receipt-cutoff"></i>Outstanding Invoices
              <span id="dueInvoiceCount" style="font-size:0.66rem;font-weight:700;text-transform:uppercase;letter-spacing:0.05em;color:#b45309;background:#fef3c7;border:1px solid #fcd34d;padding:1px 7px;border-radius:5px;margin-left:3px;">0 Due</span>
            </div>
            <div class="rv-info-bar">
              <i class="bi bi-info-circle-fill" style="flex-shrink:0;"></i>
              <span>
                <strong>Select an invoice</strong> to apply payment to that specific invoice. &nbsp;|&nbsp;
                <strong>Leave unselected</strong> to auto-settle oldest invoices first <em>(FIFO)</em>.
              </span>
            </div>


            <div id="dueInvoicesLoading" class="text-center py-2" style="display:none;">
              <div class="spinner-border spinner-border-sm" style="color:var(--rv-primary);" role="status"></div>
              <span class="ms-2" style="color:var(--rv-muted);font-size:0.8rem;">Loading...</span>
            </div>
            <div class="rv-table mb-2" id="dueInvoicesWrapper" style="display:none;">
              <table class="table table-bordered align-middle mb-0" id="dueInvoicesTable">
                <thead>
                  <tr>
                    <th style="width:36px;text-align:center;">
                      <input type="checkbox" id="selectAllInvoices" style="width:12px;height:12px;cursor:pointer;accent-color:#4f46e5;" title="All">
                    </th>
                    <th>Invoice No</th><th>Date</th>
                    <th style="text-align:right;">Net Total</th>
                    <th style="text-align:right;">Paid</th>
                    <th style="text-align:right;color:#fca5a5;">Remaining</th>
                    <th style="width:130px;">Apply Amount</th>
                  </tr>
                </thead>
                <tbody id="dueInvoicesBody"></tbody>
                <tfoot>
                  <tr>
                    <td colspan="5" style="text-align:right;font-weight:700;color:var(--rv-text);">Total Selected:</td>
                    <td style="text-align:right;font-weight:700;color:#dc2626;" id="selectedDueTotal">Rs. 0.00</td>
                    <td style="text-align:right;font-weight:700;color:#4f46e5;" id="selectedApplyTotal">Rs. 0.00</td>
                  </tr>
                </tfoot>
              </table>
            </div>
            <div id="linkedSaleInputs"></div>
            <div id="noInvoicesMsg" class="rv-all-paid">
              <i class="bi bi-check-circle-fill"></i>
              All invoices for this customer are paid. This amount will be credited to the customer balance.
            </div>


          </div>

          {{-- PAYMENT DETAILS --}}
          <div class="rv-section-label">Payment Details</div>
          <div class="rv-table">
            <table class="table table-bordered align-middle mb-0" id="voucherTable">
              <thead>
                <tr>
                  <th style="width:30%;">Account Head</th>
                  <th style="width:38%;">Account</th>
                  <th style="width:24%;">Amount</th>
                  <th style="width:8%;text-align:center;">Action</th>
                </tr>
              </thead>
              <tbody>
                <tr>
                  <td>
                    <select name="row_account_head[]" class="rv-input rowAccountHead">
                      <option value="">Select Head</option>
                      @foreach ($AccountHeads as $head)
                        <option value="{{ $head->id }}">{{ ucfirst(strtolower($head->name)) }}</option>
                      @endforeach
                    </select>
                  </td>
                  <td>
                    <select name="row_account_id[]" class="rv-input rowAccountSub">
                      <option disabled selected>Select Account</option>
                    </select>
                  </td>
                  <td><input type="number" name="amount[]" class="rv-input text-end amount" placeholder="0.00" style="font-weight:600;"></td>
                  <td style="text-align:center;">
                    <button type="button" class="btn btn-outline-danger btn-sm removeRow" style="padding:2px 7px;font-size:0.78rem;" title="Remove">
                      <i class="bi bi-trash"></i>
                    </button>
                  </td>
                </tr>
              </tbody>
              <tfoot>
                <tr>
                  <td colspan="2" style="text-align:right;font-weight:700;font-size:0.86rem;color:var(--rv-text);">Total Amount:</td>
                  <td><input type="text" name="total_amount" class="rv-input text-end" id="totalAmount" readonly value="0.00"
                      style="background:#f0fdf4;border-color:#86efac;font-size:0.92rem;color:#16a34a;font-weight:700;"></td>
                  <td></td>
                </tr>
              </tfoot>
            </table>
          </div>

          <button type="button" class="btn-rv-add mt-3" id="addNewRow">
            <i class="bi bi-plus-circle"></i> Add Another Account
          </button>

          <input type="hidden" name="narration_id[]" value="">
          <input type="hidden" name="narration_text[]" value="">
          <input type="hidden" name="reference_no[]" value="">
          <input type="hidden" name="discount_value[]" value="0">
          <input type="hidden" name="rate[]" value="0">

        </div>
      </form>
    </div>
  </div>
</div>
@endsection

@section('js')
<script>
$(document).ready(function() {
    const dueUrl = '{{ url("/customer") }}';
    loadPartyList('customer');

    $('#partyType').on('change', function() { loadPartyList($(this).val()); resetOutstanding(); });

    function loadPartyList(type) {
        let $s = $('#partyId');
        $s.html('<option disabled selected>Loading...</option>');
        $('#tel').val(''); setBalance(0); resetOutstanding();
        if (['vendor','customer','walkin'].includes(type)) {
            $.get('{{ route("party.list") }}?type='+type, function(d) {
                $s.empty().append('<option disabled selected>Select Party</option>');
                (d||[]).forEach(i => $s.append(`<option value="${i.id}" data-phone="${i.mobile||''}" data-bal="${i.closing_balance}">${i.text}</option>`));
            }).fail(() => $s.html('<option disabled>Error</option>'));
        } else if (type) {
            $.get('{{ route("get.accounts.by.head", ":id") }}'.replace(':id',type), function(d) {
                $s.empty().append('<option disabled selected>Select Account</option>');
                (d||[]).forEach(a => $s.append(`<option value="${a.id}" data-phone="${a.account_code||''}" data-bal="${a.current_balance||0}">${a.title}</option>`));
            }).fail(() => $s.html('<option disabled>Error</option>'));
        }
    }

    $('#partyId').on('change', function() {
        let $o = $(this).find(':selected'), type = $('#partyType').val();
        $('#tel').val($o.data('phone'));
        setBalance(parseFloat($o.data('bal'))||0);
        if (!$('#remarks').val()) $('#remarks').val('Receipt from '+$o.text().trim());
        if (type==='customer'||type==='walkin') loadOutstanding($(this).val());
        else resetOutstanding();
    });

    function setBalance(bal) {
        let $b=$('#balanceDisplay'), a=Math.abs(bal).toFixed(2);
        $b.attr('class',bal>=0?'rv-balance dr':'rv-balance cr')
          .html(a+' <small style="font-weight:500;margin-left:2px;">'+(bal>=0?'Dr':'Cr')+'</small>');
    }

    function loadOutstanding(cid) {
        $('#outstandingSection').show();
        $('#dueInvoicesLoading').show();
        $('#dueInvoicesWrapper,#noInvoicesMsg').hide();
        $('#dueInvoicesBody').empty(); $('#linkedSaleInputs').empty(); calcSel();
        $.get(dueUrl+'/'+cid+'/due-invoices', function(inv) {
            $('#dueInvoicesLoading').hide();
            $('#dueInvoiceCount').text(inv.length+' Due');
            if (!inv.length) { $('#noInvoicesMsg').css('display','flex'); return; }
            let h='';
            inv.forEach((v,i) => {
                h+=`<tr data-sale-id="${v.id}" data-remaining="${v.remaining}">
                    <td style="text-align:center;"><input type="checkbox" class="invoiceCheck" data-idx="${i}" data-sale-id="${v.id}" data-remaining="${v.remaining}" style="width:12px;height:12px;cursor:pointer;accent-color:#4f46e5;"></td>
                    <td><strong style="color:#1e293b;font-size:0.82rem;">${v.invoice_no}</strong></td>
                    <td style="color:#64748b;font-size:0.76rem;">${v.date}</td>
                    <td style="text-align:right;">Rs. ${fmt(v.total_net)}</td>
                    <td style="text-align:right;color:#16a34a;">Rs. ${fmt(v.paid)}</td>
                    <td style="text-align:right;font-weight:700;color:#dc2626;">Rs. ${fmt(v.remaining)}</td>
                    <td><input type="number" class="rv-input text-end invoiceApplyAmt" data-idx="${i}" data-sale-id="${v.id}" placeholder="0.00" min="0" max="${v.remaining}" step="0.01" disabled style="font-size:0.8rem;background:#f8fafc;font-weight:600;color:#4f46e5;height:28px;"></td>
                    </tr>`;
            });
            $('#dueInvoicesBody').html(h); $('#dueInvoicesWrapper').show();
        }).fail(() => $('#dueInvoicesLoading').hide());
    }

    function resetOutstanding() {
        $('#outstandingSection').hide(); $('#dueInvoicesBody').empty(); $('#linkedSaleInputs').empty();
        $('#dueInvoicesWrapper,#noInvoicesMsg').hide(); $('#selectAllInvoices').prop('checked',false); calcSel();
    }

    $('#selectAllInvoices').on('change', function() { $('.invoiceCheck').prop('checked',$(this).is(':checked')).trigger('change'); });

    $(document).on('change','.invoiceCheck', function() {
        let i=$(this).data('idx'), r=parseFloat($(this).data('remaining')), $a=$(`.invoiceApplyAmt[data-idx="${i}"]`);
        $(this).is(':checked') ? $a.prop('disabled',false).val(r.toFixed(2)).css({background:'#fff'})
                               : $a.prop('disabled',true).val('').css({background:'#f8fafc'});
        buildHidden(); calcSel();
    });

    $(document).on('input','.invoiceApplyAmt', function() {
        let mx=parseFloat($(this).attr('max'));
        if ((parseFloat($(this).val())||0)>mx) $(this).val(mx.toFixed(2));
        buildHidden(); calcSel();
    });

    function buildHidden() {
        $('#linkedSaleInputs').empty();
        $('.invoiceCheck:checked').each(function() {
            let i=$(this).data('idx'), sid=$(this).data('sale-id'), a=parseFloat($(`.invoiceApplyAmt[data-idx="${i}"]`).val())||0;
            $('#linkedSaleInputs').append(`<input type="hidden" name="linked_sale_ids[]" value="${sid}"><input type="hidden" name="linked_sale_amounts[]" value="${a}">`);
        });
    }

    function calcSel() {
        let d=0,ap=0;
        $('.invoiceCheck:checked').each(function() {
            let i=$(this).data('idx');
            d+=parseFloat($(this).data('remaining'))||0;
            ap+=parseFloat($(`.invoiceApplyAmt[data-idx="${i}"]`).val())||0;
        });
        $('#selectedDueTotal').text('Rs. '+fmt(d)); $('#selectedApplyTotal').text('Rs. '+fmt(ap));
    }

    function fmt(n) { return parseFloat(n).toLocaleString('en-PK',{minimumFractionDigits:2,maximumFractionDigits:2}); }

    $(document).on('change','.rowAccountHead', function() {
        let $sub=$(this).closest('tr').find('.rowAccountSub'), hid=$(this).val();
        if (!hid) { $sub.html('<option value="">Select Account</option>'); return; }
        $sub.html('<option disabled selected>Loading...</option>');
        $.get('{{ route("get.accounts.by.head", ":id") }}'.replace(':id',hid), function(r) {
            let h='<option value="">Select Account</option>';
            (r||[]).forEach(a => h+=`<option value="${a.id}">${a.title}</option>`);
            $sub.html(h);
        }).fail(() => $sub.html('<option disabled>Error</option>'));
    });

    function calcTotal() { let t=0; $('.amount').each(function(){t+=parseFloat($(this).val())||0;}); $('#totalAmount').val(t.toFixed(2)); }
    $(document).on('input','.amount', calcTotal);

    $('#addNewRow').on('click', function() {
        $('#voucherTable tbody').append(`<tr>
          <td><select name="row_account_head[]" class="rv-input rowAccountHead"><option value="">Select Head</option>@foreach($AccountHeads as $head)<option value="{{ $head->id }}">{{ $head->name }}</option>@endforeach</select></td>
          <td><select name="row_account_id[]" class="rv-input rowAccountSub"><option disabled selected>Select Account</option></select></td>
          <td><input type="number" name="amount[]" class="rv-input text-end amount" placeholder="0.00" style="font-weight:600;"></td>
          <td style="text-align:center;"><button type="button" class="btn btn-outline-danger btn-sm removeRow" style="padding:2px 7px;font-size:0.78rem;"><i class="bi bi-trash"></i></button></td>
        </tr>`);
    });

    $(document).on('click','.removeRow', function() {
        if ($('#voucherTable tbody tr').length>1) { $(this).closest('tr').remove(); calcTotal(); }
    });
});
</script>
@endsection