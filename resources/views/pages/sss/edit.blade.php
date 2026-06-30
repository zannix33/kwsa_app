@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')
    <div class="card">

        <div class="card-header">
            Edit SSS Contribution
        </div>

        <div class="card-body">

            <form
                action="{{ route('sss.update',$sss) }}"
                method="POST">

                @csrf
                @method('PUT')

                @include('pages.sss._form')

                <button class="btn btn-success">
                    Update
                </button>

            </form>

        </div>

    </div>
@endsection
