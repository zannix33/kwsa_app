@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div>
            <h1 class="h3 mb-1">Billing Details</h1>
            <div class="text-muted">{{ $billing->invoice_number ?: 'Draft #' . $billing->id }}</div>
        </div>
        <div class="d-flex gap-2">
            <a href="{{ route('billing.print', $billing) }}" class="btn btn-primary" target="_blank" rel="noopener">Print / Save as PDF</a>
            <a href="{{ route('billing.index') }}" class="btn btn-outline-secondary">Back to Billing</a>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><strong>Bill Information</strong></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6 mb-3"><strong>Client</strong><br>{{ optional($billing->client)->name ?: '—' }}</div>
                <div class="col-md-6 mb-3"><strong>Area</strong><br>{{ optional($billing->area)->name ?: '—' }}</div>
                <div class="col-md-6 mb-3"><strong>Payroll Period</strong><br>
                    {{ optional($billing->payrollPeriod)->name ?: '—' }}
                    @if ($billing->payrollPeriod)
                        <br><small class="text-muted">{{ optional($billing->payrollPeriod->date_from)->format('M d, Y') }} – {{ optional($billing->payrollPeriod->date_to)->format('M d, Y') }}</small>
                    @endif
                </div>
                <div class="col-md-6 mb-3"><strong>Status</strong><br>{{ ucfirst($billing->status) }}</div>
                <div class="col-md-6 mb-3"><strong>Billing Date</strong><br>{{ optional($billing->billing_date)->format('M d, Y') ?: '—' }}</div>
                <div class="col-md-6 mb-3"><strong>Due Date</strong><br>{{ optional($billing->due_date)->format('M d, Y') ?: '—' }}</div>
                <div class="col-md-6 mb-3"><strong>Tax Type</strong><br>{{ strtoupper(str_replace('_', ' ', $billing->tax_type ?: 'non_vat')) }}</div>
                <div class="col-md-6 mb-3"><strong>Notes</strong><br>{{ $billing->notes ?: '—' }}</div>
            </div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header"><strong>Line Items</strong></div>
        <div class="card-body table-responsive">
            <table class="table table-striped align-middle">
                <thead>
                    <tr>
                        <th>Code</th>
                        <th>Description</th>
                        <th>Employee / Branch</th>
                        <th class="text-end">Quantity</th>
                        <th>Unit</th>
                        <th class="text-end">Rate</th>
                        <th class="text-end">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse ($billing->items as $item)
                        <tr>
                            <td>{{ $item->code }}</td>
                            <td>{{ $item->description }}</td>
                            <td>
                                {{ optional($item->user)->name ?: '—' }}
                                @if ($item->branch)
                                    <br><small class="text-muted">{{ $item->branch->name }}</small>
                                @endif
                            </td>
                            <td class="text-end">{{ number_format((float) $item->quantity, 2) }}</td>
                            <td>{{ $item->unit ?: '—' }}</td>
                            <td class="text-end">₱{{ number_format((float) $item->rate, 2) }}</td>
                            <td class="text-end">₱{{ number_format((float) $item->amount, 2) }}</td>
                        </tr>
                    @empty
                        <tr><td colspan="7" class="text-center text-muted py-4">No line items found.</td></tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <div class="row justify-content-end">
        <div class="col-md-5 col-lg-4">
            <div class="card">
                <div class="card-header"><strong>Summary</strong></div>
                <div class="card-body">
                    <div class="d-flex justify-content-between mb-2"><span>Subtotal</span><strong>₱{{ number_format((float) $billing->subtotal, 2) }}</strong></div>
                    <div class="d-flex justify-content-between mb-2"><span>Agency Fee</span><strong>₱{{ number_format((float) $billing->agency_fee_amount, 2) }}</strong></div>
                    <div class="d-flex justify-content-between mb-2"><span>Retirement Fund</span><strong>₱{{ number_format((float) $billing->retirement_fund_amount, 2) }}</strong></div>
                    <div class="d-flex justify-content-between mb-2"><span>Penalties</span><strong>₱{{ number_format((float) $billing->penalties_amount, 2) }}</strong></div>
                    <div class="d-flex justify-content-between mb-2"><span>EWT</span><strong>₱{{ number_format((float) $billing->ewt_amount, 2) }}</strong></div>
                    <div class="d-flex justify-content-between mb-2"><span>VAT</span><strong>₱{{ number_format((float) $billing->vat_amount, 2) }}</strong></div>
                    <hr>
                    <div class="d-flex justify-content-between"><span class="h5 mb-0">Total</span><strong class="h5 mb-0">₱{{ number_format((float) $billing->total_amount, 2) }}</strong></div>
                </div>
            </div>
        </div>
    </div>

    <div class="alert alert-warning mt-4">
        This page displays a draft calculation. Validate rates and formulas against the approved billing spreadsheet before issuing the invoice.
    </div>
</div>
@endsection
