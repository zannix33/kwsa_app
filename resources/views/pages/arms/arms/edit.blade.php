@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')

    <div class="card">

        <div class="card-header">

            Edit Firearm

        </div>

        <div class="card-body">

            @extends('layouts.app')

            @section('title', 'Edit Firearm')

            @section('content')
                <form action="{{ route('arms.update', $arm) }}"
                      method="POST"
                      enctype="multipart/form-data">

                    @include('pages.arms.arms._form')

                </form>
            @endsection

        </div>

    </div>

@endsection
