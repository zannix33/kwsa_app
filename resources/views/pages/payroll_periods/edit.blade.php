@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')
    <div class="card">

        <div class="card-header">
            Edit Payroll Period
        </div>

        <div class="card-body">

            <form
                method="POST"
                action="{{ route(
                'payroll-periods.update',
                $payrollPeriod
            ) }}">

                @csrf
                @method('PUT')

                @include(
                    'pages.payroll_periods._form'
                )

                <button
                    class="btn btn-success">

                    Update

                </button>

            </form>

        </div>

    </div>

@endsection
