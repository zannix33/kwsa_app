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

        <form action="{{ route('hr.employee.update', $employee->id) }}"
              method="POST"
              enctype="multipart/form-data">
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

@push('custom-scripts')
    <script>
        document.getElementById('photo').addEventListener('change', function(e){

            const file = e.target.files[0];

            if(!file) return;

            const reader = new FileReader();

            reader.onload = function(ev){

                let preview = document.getElementById('photo-preview');

                preview.outerHTML =
                    '<img id="photo-preview" src="'+ev.target.result+'" class="img-thumbnail rounded mb-3" style="width:200px;height:200px;object-fit:cover;">';

            };

            reader.readAsDataURL(file);

        });
    </script>
@endpush
