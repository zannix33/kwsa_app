@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')

    <div class="container-fluid">

        <div class="card">

            <div class="card-header">
                Generate Manual DTR
            </div>

            <div class="card-body">

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <form method="POST"
                      action="{{ route('dtr.bulk.generate') }}">

                    @csrf

                    <div class="row">

                        <div class="col-md-4">

                            <div class="form-group">
                                <label>Employee</label>

                                <select name="user_id"
                                        class="form-control"
                                        required>

                                    <option value="">
                                        Select Employee
                                    </option>

                                    @foreach($users as $user)

                                        <option value="{{ $user->id }}">
                                            {{ $user->full_name }}
                                        </option>

                                    @endforeach

                                </select>
                            </div>

                        </div>

                        <div class="col-md-3">

                            <div class="form-group">
                                <label>From Date</label>

                                <input type="date"
                                       name="from_date"
                                       class="form-control"
                                       required>

                            </div>

                        </div>

                        <div class="col-md-3">

                            <div class="form-group">
                                <label>To Date</label>

                                <input type="date"
                                       name="to_date"
                                       class="form-control"
                                       required>

                            </div>

                        </div>

                        <div class="col-md-2">

                            <div class="form-group">

                                <label>&nbsp;</label>

                                <button class="btn btn-primary btn-block">
                                    Generate
                                </button>

                            </div>

                        </div>

                    </div>

                </form>

            </div>

        </div>

    </div>

@endsection
