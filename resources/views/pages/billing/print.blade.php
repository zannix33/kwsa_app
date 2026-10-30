<!doctype html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Billing Statement {{ $billing->invoice_number ?: 'Draft-' . $billing->id }}</title>
    <style>
        @page { size: A4; margin: 14mm; }
        * { box-sizing: border-box; }
        body { font-family: Arial, Helvetica, sans-serif; color: #222; font-size: 12px; margin: 0; }
        .toolbar { max-width: 900px; margin: 18px auto; display: flex; gap: 8px; }
        .button { border: 1px solid #555; border-radius: 4px; padding: 8px 12px; color: #222; background: #fff; text-decoration: none; cursor: pointer; font-size: 13px; }
        .page { max-width: 900px; margin: 0 auto 24px; padding: 28px; background: white; }
        .top { display: flex; justify-content: space-between; gap: 24px; border-bottom: 2px solid #222; padding-bottom: 18px; }
        h1 { margin: 0 0 6px; font-size: 25px; letter-spacing: .4px; }
        h2 { margin: 0 0 8px; font-size: 14px; }
        .muted { color: #666; }
        .meta { text-align: right; min-width: 200px; }
        .status { display: inline-block; border: 1px solid #888; padding: 4px 7px; margin-top: 6px; font-size: 10px; text-transform: uppercase; }
        .details { display: grid; grid-template-columns: 1fr 1fr; gap: 18px; margin: 20px 0; }
        .detail-box { border: 1px solid #ddd; padding: 12px; min-height: 84px; }
        .label { font-size: 10px; color: #666; text-transform: uppercase; letter-spacing: .5px; margin-bottom: 5px; }
        table { width: 100%; border-collapse: collapse; margin-top: 14px; }
        th { background: #f1f1f1; text-align: left; font-weight: bold; }
        th, td { border: 1px solid #d7d7d7; padding: 7px 8px; vertical-align: top; }
        .num { text-align: right; white-space: nowrap; }
        .summary-wrap { display: flex; justify-content: flex-end; margin-top: 18px; }
        .summary { width: 330px; }
        .sum-row { display: flex; justify-content: space-between; gap: 16px; padding: 5px 0; }
        .sum-row.total { border-top: 2px solid #222; margin-top: 5px; padding-top: 10px; font-size: 16px; font-weight: bold; }
        .notes { margin-top: 24px; border-top: 1px solid #ddd; padding-top: 12px; }
        .footer { margin-top: 38px; display: grid; grid-template-columns: 1fr 1fr; gap: 70px; }
        .signature { border-top: 1px solid #777; padding-top: 7px; margin-top: 35px; }
        @media print { body { font-size: 10px; } .toolbar { display: none; } .page { max-width: none; margin: 0; padding: 0; } th { background: #f1f1f1 !important; -webkit-print-color-adjust: exact; print-color-adjust: exact; } tr { page-break-inside: avoid; } }
    </style>
</head>
<body>
    <div class="toolbar">
        <button class="button" onclick="window.print()">Print / Save as PDF</button>
        <a class="button" href="{{ route('billing.show', $billing) }}">Back to bill details</a>
    </div>
    <main class="page">
        <header class="top">
            <div>
                <h1>BILLING STATEMENT</h1>
                <div class="muted">Security Services</div>
                <p class="muted" style="margin-bottom:0">Company name, address, and contact details can be added after confirming your official invoice header.</p>
            </div>
            <div class="meta">
                <h2>{{ $billing->invoice_number ?: 'DRAFT BILL #' . $billing->id }}</h2>
                <div><strong>Billing date:</strong> {{ optional($billing->billing_date)->format('M d, Y') ?: '—' }}</div>
                <div><strong>Due date:</strong> {{ optional($billing->due_date)->format('M d, Y') ?: '—' }}</div>
                <span class="status">{{ strtoupper($billing->status ?: 'draft') }}</span>
            </div>
        </header>

        <section class="details">
            <div class="detail-box">
                <div class="label">Bill To</div>
                <strong>{{ optional($billing->client)->name ?: 'Client not specified' }}</strong><br>
                @if(optional($billing->client)->address) {{ $billing->client->address }} @endif
            </div>
            <div class="detail-box">
                <div class="label">Service / Billing Details</div>
                <strong>Company:</strong> {{ optional($billing->company)->name ?: 'Legacy / not assigned' }}<br>
                <strong>Area:</strong> {{ optional($billing->area)->name ?: '—' }}<br>
                <strong>Payroll period:</strong> {{ optional($billing->payrollPeriod)->name ?: '—' }}<br>
                <strong>Coverage:</strong>
                {{ optional(optional($billing->payrollPeriod)->date_from)->format('M d, Y') ?: '—' }} –
                {{ optional(optional($billing->payrollPeriod)->date_to)->format('M d, Y') ?: '—' }}<br>
                <strong>Tax type:</strong> {{ strtoupper(str_replace('_', ' ', $billing->tax_type ?: 'non_vat')) }}
            </div>
        </section>

        <h2>Itemized Charges</h2>
        <table>
            <thead><tr><th style="width:16%">Code</th><th>Description</th><th class="num">Qty</th><th>Unit</th><th class="num">Rate</th><th class="num">Amount</th></tr></thead>
            <tbody>
            @forelse($billing->items as $item)
                <tr>
                    <td>{{ $item->code ?: '—' }}</td>
                    <td>{{ $item->description ?: '—' }}@if($item->branch)<br><span class="muted">Branch: {{ $item->branch->name }}</span>@endif</td>
                    <td class="num">{{ number_format((float)$item->quantity, 2) }}</td>
                    <td>{{ $item->unit ?: '—' }}</td>
                    <td class="num">₱{{ number_format((float)$item->rate, 2) }}</td>
                    <td class="num">₱{{ number_format((float)$item->amount, 2) }}</td>
                </tr>
            @empty
                <tr><td colspan="6" style="text-align:center">No line items recorded.</td></tr>
            @endforelse
            </tbody>
        </table>

        <div class="summary-wrap"><div class="summary">
            <div class="sum-row"><span>Subtotal</span><strong>₱{{ number_format((float)$billing->subtotal, 2) }}</strong></div>
            <div class="sum-row"><span>Agency Fee</span><span>₱{{ number_format((float)$billing->agency_fee_amount, 2) }}</span></div>
            <div class="sum-row"><span>Retirement Fund</span><span>₱{{ number_format((float)$billing->retirement_fund_amount, 2) }}</span></div>
            <div class="sum-row"><span>Penalties</span><span>₱{{ number_format((float)$billing->penalties_amount, 2) }}</span></div>
            <div class="sum-row"><span>EWT</span><span>₱{{ number_format((float)$billing->ewt_amount, 2) }}</span></div>
            <div class="sum-row"><span>VAT</span><span>₱{{ number_format((float)$billing->vat_amount, 2) }}</span></div>
            <div class="sum-row total"><span>Total Due</span><span>₱{{ number_format((float)$billing->total_amount, 2) }}</span></div>
        </div></div>

        @if($billing->notes)
            <div class="notes"><strong>Notes:</strong><br>{{ $billing->notes }}</div>
        @endif
        <div class="footer">
            <div><div class="signature">Prepared by / Date</div></div>
            <div><div class="signature">Authorized signature / Date</div></div>
        </div>
        <p class="muted" style="margin-top:24px">{{ strtolower($billing->status ?: 'draft') === 'draft' ? 'DRAFT — for review only; not an issued invoice.' : 'Please retain this statement for your records.' }}</p>
    </main>
</body>
</html>
