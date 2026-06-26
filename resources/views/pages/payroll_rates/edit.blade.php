@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')
    <div class="container">

        <h4>Edit Payroll Rate</h4>

        <form method="POST" action="{{ route('payroll-rates.update', $rate->id) }}">
            @method('PUT')

            @include('pages.payroll_rates._form', ['rate' => $rate])

            <button class="btn btn-primary">Update</button>
            <a href="{{ route('payroll-rates.index') }}" class="btn btn-secondary">Back</a>
        </form>

    </div>
@endsection
