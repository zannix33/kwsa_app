@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h1 class="h3 mb-1">Employee Loans</h1>
                <p class="text-muted mb-0">
                    Manage SSS, Pag-IBIG, and company employee loans.
                </p>
            </div>

            <a href="{{ route('loans.create') }}" class="btn btn-primary">
                <i class="fas fa-plus"></i> Add Loan
            </a>
        </div>

        {{-- Summary Cards --}}
        <div class="row mb-4">

            {{-- Total Loan Amount --}}
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-primary shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-1">
                            Total Loan Amount
                        </div>

                        <h4 class="mb-0">
                            ₱{{ number_format($totalLoanAmount ?? 0, 2) }}
                        </h4>
                    </div>
                </div>
            </div>

            {{-- Outstanding Balance --}}
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-danger shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-1">
                            Total Outstanding Balance
                        </div>

                        <h4 class="mb-0 text-danger">
                            ₱{{ number_format($totalOutstanding ?? 0, 2) }}
                        </h4>
                    </div>
                </div>
            </div>

            {{-- Active Loans --}}
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-success shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-1">
                            Active Loans
                        </div>

                        <h4 class="mb-0 text-success">
                            {{ number_format($activeLoans ?? 0) }}
                        </h4>
                    </div>
                </div>
            </div>

            {{-- Fully Paid Loans --}}
            <div class="col-xl-3 col-md-6 mb-3">
                <div class="card border-left-info shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small mb-1">
                            Fully Paid Loans
                        </div>

                        <h4 class="mb-0 text-info">
                            {{ number_format($fullyPaidLoans ?? 0) }}
                        </h4>
                    </div>
                </div>
            </div>

        </div>

        {{-- Loan DataTable --}}
        @include('pages.loans._datatable')

    </div>
@endsection

