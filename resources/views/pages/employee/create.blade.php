@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')

    <form action="{{ route('hr.employee.store') }}"
          method="POST"
          enctype="multipart/form-data">
        @csrf

        @include('pages.employee._form')

        <br>

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
