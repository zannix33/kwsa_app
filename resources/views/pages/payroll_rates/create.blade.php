@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')
<div class="container">

    <h4>Create Payroll Rate</h4>

    <form method="POST" action="{{ route('payroll-rates.store') }}">
        @include('pages.payroll_rates._form')

        <button class="btn btn-primary">Save</button>
        <a href="{{ route('payroll-rates.index') }}" class="btn btn-secondary">Back</a>
    </form>

</div>
@endsection
