@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')
    <div class="container">

        <div class="row mb-3">
            <div class="col-md-12">
                <h2>Edit Employee</h2>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <strong>Please fix the following errors:</strong>
                <ul class="mb-0 mt-2">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <form action="{{ route('hr.employee.update', $employee->id) }}" method="POST">
            @csrf
            @method('PUT')

            @include('pages.employee._form')

            <div class="text-right">
                <a href="{{ route('hr.employee.index') }}"
                   class="btn btn-secondary">
                    Cancel
                </a>

                <button type="submit"
                        class="btn btn-primary">
                    Update Employee
                </button>
            </div>



        </form>
    </div>


@endsection
