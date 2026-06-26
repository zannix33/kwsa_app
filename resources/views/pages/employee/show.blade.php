@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')

    <fieldset disabled>

    <form action="{{ route('hr.employee.store') }}" method="POST" disabled="true">
        @csrf

        @include('pages.employee._form')



    </form>
    </fieldset>
@endsection
