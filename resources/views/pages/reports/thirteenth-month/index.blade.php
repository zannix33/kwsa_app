@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')

    <div class="card">
        <div class="card-header">
            <h4>13th Month Pay Report</h4>
        </div>

        <div class="card-body">

            <form method="POST" action="{{ route('reports.13th.generate') }}">
                @csrf

                <div class="row">

                    <div class="col-md-4">
                        <label>Area</label>

                        <select name="area_id" class="form-control" required>

                            <option value="">Select Area</option>

                            @foreach($areas as $area)

                                <option value="{{ $area->id }}">
                                    {{ $area->name }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="col-md-3">

                        <label>From</label>

                        <input
                            type="date"
                            name="from"
                            class="form-control"
                            required>

                    </div>

                    <div class="col-md-3">

                        <label>To</label>

                        <input
                            type="date"
                            name="to"
                            class="form-control"
                            required>

                    </div>

                    <div class="col-md-2">

                        <label>&nbsp;</label>

                        <button class="btn btn-primary btn-block">
                            Generate
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

@endsection
