@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')

<div class="container">

    <div class="card">
        <div class="card-header">
            Create SSS Contribution
        </div>

        <div class="card-body">

            <form
                action="{{ route('sss.store') }}"
                method="POST">

                @csrf

                @include('pages.sss._form')

                <button class="btn btn-primary">
                    Save
                </button>

            </form>

        </div>
    </div>

</div>
@endsection
