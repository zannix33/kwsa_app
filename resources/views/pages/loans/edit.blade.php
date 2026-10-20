@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="row mb-3">
            <div class="col-md-8">
                <h1 class="h3">Edit Loan</h1>
                <p class="text-muted">
                    Update loan information and repayment schedule.
                </p>
            </div>

            <div class="col-md-4 text-md-right">
                <a href="{{ route('loans.show', $loan->id) }}"
                   class="btn btn-info">
                    <i class="fas fa-eye"></i> View Loan
                </a>

                <a href="{{ route('loans.index') }}"
                   class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        {{-- Validation Errors --}}
        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Please correct the following errors:</strong>

                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Loan Summary --}}
        <div class="row mb-3">

            <div class="col-md-4 mb-2">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase">
                            Original Loan Amount
                        </div>

                        <h4 class="mb-0">
                            ₱{{ number_format($loan->total_loan_amount, 2) }}
                        </h4>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-2">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase">
                            Total Paid
                        </div>

                        <h4 class="mb-0 text-success">
                            ₱{{ number_format($loan->total_paid, 2) }}
                        </h4>
                    </div>
                </div>
            </div>

            <div class="col-md-4 mb-2">
                <div class="card shadow-sm h-100">
                    <div class="card-body">
                        <div class="text-muted small text-uppercase">
                            Outstanding Balance
                        </div>

                        <h4 class="mb-0 text-danger">
                            ₱{{ number_format($loan->outstanding_balance, 2) }}
                        </h4>
                    </div>
                </div>
            </div>

        </div>

        {{-- Edit Form --}}
        <form action="{{ route('loans.update', $loan->id) }}"
              method="POST">

            @csrf
            @method('PUT')

            @include('pages.loans._form')

            <div class="card shadow-sm mt-3">
                <div class="card-body d-flex justify-content-between">

                    <a href="{{ route('loans.index') }}"
                       class="btn btn-secondary">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Update Loan
                    </button>

                </div>
            </div>

        </form>

    </div>
@endsection
