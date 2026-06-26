@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')

    <div class="card">

        <div class="card-header">
            Create Payroll Period
        </div>

        <div class="card-body">

            <form
                method="POST"
                action="{{ route('payroll-periods.store') }}">

                @csrf

                @include(
                    'pages.payroll_periods._form'
                )

                <button
                    class="btn btn-primary">

                    Save

                </button>

            </form>

        </div>

    </div>

@endsection
