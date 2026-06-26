@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->

    @include('pages.dtr.partials.styles')

@endpush

@section('content')

    <div class="container-fluid">

        <form method="POST"
              action="{{ route('dtr.bulk.store') }}">

            @csrf

            <input type="hidden"
                   name="user_id"
                   value="{{ $user->id }}">

            <div class="card mb-3">

                <div class="card-header">
                    Manual DTR Entry
                </div>

                <div class="card-body">

                    <h5>
                        {{ $user->full_name }}
                    </h5>

                </div>

            </div>

            @include('pages.dtr.partials._default-schedule')

            @include('pages.dtr.partials.dtr-table')

            @include('pages.dtr.partials.summary')

            <div class="text-right mt-3">

                <button class="btn btn-success btn-lg">
                    Save DTR
                </button>

            </div>

        </form>

    </div>

@endsection

@include('pages.dtr.partials.scripts')
