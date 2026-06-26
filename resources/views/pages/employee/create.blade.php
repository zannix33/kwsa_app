@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')

    <form action="{{ route('hr.employee.store') }}" method="POST">
        @csrf

        @include('employee._form')

        <div class="text-right">
            <a href="{{ route('hr.employee.index') }}"
               class="btn btn-secondary">
                Cancel
            </a>

            <button type="submit"
                    class="btn btn-primary">
                Create Employee
            </button>
        </div>



    </form>
@endsection
