@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')
    <div class="container-fluid">

        <div class="card">
            <div class="card-header">
                Edit Daily Time Record
            </div>

            <div class="card-body">

                @if($errors->any())
                    <div class="alert alert-danger">
                        <ul class="mb-0">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form action="{{ route('dtr.update', $dtr->id) }}"
                      method="POST">

                    @csrf
                    @method('PUT')

                    @include('pages.dtr._form')

                    <button class="btn btn-primary">
                        Update
                    </button>

                    <a href="{{ route('dtr.index') }}"
                       class="btn btn-secondary">
                        Cancel
                    </a>

                </form>

            </div>
        </div>

    </div>
@endsection
