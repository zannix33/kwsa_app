@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')
    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="row mb-3">
            <div class="col-md-8">
                <h1 class="h3">Register New Loan</h1>
                <p class="text-muted">
                    Create a loan record for an employee.
                </p>
            </div>

            <div class="col-md-4 text-md-right">
                <a href="{{ route('loans.index') }}"
                   class="btn btn-secondary">
                    <i class="fas fa-arrow-left"></i> Back to Loans
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

        <form action="{{ route('loans.store') }}"
              method="POST">

            @csrf

            @include('pages.loans._form')

            <div class="card shadow-sm mt-3">
                <div class="card-body text-right">

                    <a href="{{ route('loans.index') }}"
                       class="btn btn-secondary">
                        Cancel
                    </a>

                    <button type="submit" class="btn btn-primary">
                        <i class="fas fa-save"></i> Save Loan
                    </button>

                </div>
            </div>

        </form>

    </div>
@endsection
