@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">Loan Details</h1>
                <p class="text-muted mb-0">
                    Reference: {{ $loan->loan_reference }}
                </p>
            </div>

            <div>
                <a href="{{ route('loans.index') }}" class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>

                @if($loan->status !== 'Cancelled')
                    <a href="{{ route('loans.edit', $loan->id) }}"
                       class="btn btn-primary">
                        <i class="fas fa-edit"></i> Edit Loan
                    </a>
                @endif
            </div>
        </div>

        {{-- Loan Summary --}}
        <div class="row mb-4">

            <div class="col-md-3 mb-3">
                <div class="card border-left-primary shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Original Loan Amount</div>
                        <h4 class="mb-0">
                            ₱{{ number_format($loan->total_loan_amount, 2) }}
                        </h4>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-left-success shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Total Paid</div>
                        <h4 class="mb-0 text-success">
                            ₱{{ number_format($loan->total_paid, 2) }}
                        </h4>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-left-danger shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Outstanding Balance</div>
                        <h4 class="mb-0 text-danger">
                            ₱{{ number_format($loan->outstanding_balance, 2) }}
                        </h4>
                    </div>
                </div>
            </div>

            <div class="col-md-3 mb-3">
                <div class="card border-left-info shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small">Monthly Amortization</div>
                        <h4 class="mb-0">
                            ₱{{ number_format($loan->monthly_amortization, 2) }}
                        </h4>
                    </div>
                </div>
            </div>

        </div>

        {{-- Loan Information --}}
        <div class="card shadow-sm mb-4">
            <div class="card-header">
                <h5 class="mb-0">Loan Information</h5>
            </div>

            <div class="card-body">
                <div class="row">

                    <div class="col-md-6 mb-3">
                        <strong>Employee</strong>
                        <div>
                            {{ optional($loan->user)->employee_no }}
                            -
                            {{ optional($loan->user)->firstname }}
                            {{ optional($loan->user)->lastname }}
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <strong>Loan Provider</strong>
                        <div>{{ $loan->loan_provider }}</div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <strong>Loan Type</strong>
                        <div>{{ $loan->loan_type }}</div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <strong>Loan Date</strong>
                        <div>
                            {{ $loan->loan_date
                                ? $loan->loan_date->format('M d, Y')
                                : '—' }}
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <strong>Deduction Start Date</strong>
                        <div>
                            {{ $loan->deduction_start_date
                                ? $loan->deduction_start_date->format('M d, Y')
                                : '—' }}
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <strong>Deduction End Date</strong>
                        <div>
                            {{ $loan->deduction_end_date
                                ? $loan->deduction_end_date->format('M d, Y')
                                : 'Not specified' }}
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <strong>Status</strong>
                        <div>
                            @if($loan->status === 'Active')
                                <span class="badge badge-success">Active</span>
                            @elseif($loan->status === 'Fully Paid')
                                <span class="badge badge-primary">Fully Paid</span>
                            @elseif($loan->status === 'Cancelled')
                                <span class="badge badge-danger">Cancelled</span>
                            @else
                                <span class="badge badge-secondary">
                                {{ $loan->status }}
                            </span>
                            @endif
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <strong>Principal Amount</strong>
                        <div>
                            ₱{{ number_format($loan->principal_amount, 2) }}
                        </div>
                    </div>

                    <div class="col-md-3 mb-3">
                        <strong>Interest Amount</strong>
                        <div>
                            ₱{{ number_format($loan->interest_amount, 2) }}
                        </div>
                    </div>

                    <div class="col-md-12 mb-3">
                        <strong>Remarks</strong>
                        <div>
                            {{ $loan->remarks ?: 'No remarks.' }}
                        </div>
                    </div>

                </div>
            </div>
        </div>

        {{-- Payment History --}}
        <div class="card shadow-sm">
            <div class="card-header">
                <h5 class="mb-0">Payment History</h5>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead>
                        <tr>
                            <th>#</th>
                            <th>Payment Date</th>
                            <th>Payroll Period</th>
                            <th>Payment Method</th>
                            <th class="text-right">Amount Paid</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($loan->payments as $payment)
                            <tr>
                                <td>{{ $loop->iteration }}</td>

                                <td>
                                    {{ $payment->payment_date
                                        ? $payment->payment_date->format('M d, Y')
                                        : '—' }}
                                </td>

                                <td>
                                    {{ optional($payment->payrollPeriod)->name ?? '—' }}
                                </td>

                                <td>{{ $payment->payment_method }}</td>

                                <td class="text-right">
                                    ₱{{ number_format($payment->amount, 2) }}
                                </td>
                            </tr>
                        @empty
                            <tr>
                                <td colspan="5" class="text-center text-muted">
                                    No payments recorded for this loan.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>

                        @if($loan->payments->count() > 0)
                            <tfoot>
                            <tr>
                                <th colspan="4" class="text-right">
                                    Total Payments
                                </th>
                                <th class="text-right">
                                    ₱{{ number_format(
                                        $loan->payments->sum('amount'),
                                        2
                                    ) }}
                                </th>
                            </tr>
                            </tfoot>
                        @endif
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection

