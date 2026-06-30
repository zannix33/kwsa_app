@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush
@section('content')

    <div class="card">

        <div class="card-header">

            New Firearm

        </div>

        <div class="card-body">

            <form action="{{ route('arms.store') }}"
                  method="POST"
                  enctype="multipart/form-data">

                @include('pages.arms.arms._form')

            </form>

        </div>

    </div>

@endsection
