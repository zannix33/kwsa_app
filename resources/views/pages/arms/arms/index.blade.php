@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')

    <div class="container-fluid">

        {{-- Page Header --}}
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>

                <h3 class="mb-0">
                    <i class="fas fa-crosshairs"></i>
                    Firearms Inventory
                </h3>

                <small class="text-muted">
                    Manage all registered firearms
                </small>

            </div>

            <div>

                <a href="{{ route('arms.create') }}"
                   class="btn btn-primary">

                    <i class="fa fa-plus"></i>

                    Add Firearm

                </a>

                {{--

                <a href="{{ route('arms.dashboard') }}"
                   class="btn btn-dark">

                    Dashboard

                </a>
                --}}

            </div>

        </div>

        {{-- Statistics --}}
        <div class="row mb-3">

            <div class="col-md-2">
                <div class="small-box bg-info">
                    <div class="inner">
                        <h3 id="totalFirearms">0</h3>
                        <p>Total</p>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="small-box bg-success">
                    <div class="inner">
                        <h3 id="availableFirearms">0</h3>
                        <p>Available</p>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="small-box bg-warning">
                    <div class="inner">
                        <h3 id="issuedFirearms">0</h3>
                        <p>Issued</p>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="small-box bg-danger">
                    <div class="inner">
                        <h3 id="maintenanceFirearms">0</h3>
                        <p>Maintenance</p>
                    </div>
                </div>
            </div>

            <div class="col-md-2">
                <div class="small-box bg-secondary">
                    <div class="inner">
                        <h3 id="retiredFirearms">0</h3>
                        <p>Retired</p>
                    </div>
                </div>
            </div>

        </div>

        {{-- Filters --}}
        <div class="card shadow mb-3">

            <div class="card-header">

                Advanced Search

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-3">

                        <select
                            id="branch"
                            class="form-control">

                            <option value="">All Branches</option>

                            @foreach($branches as $branch)

                                <option
                                    value="{{ $branch->id }}">

                                    {{ $branch->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="col-md-2">

                        <select
                            id="status"
                            class="form-control">

                            <option value="">Status</option>

                            <option>Available</option>

                            <option>Issued</option>

                            <option>Maintenance</option>

                            <option>Lost</option>

                            <option>Retired</option>

                        </select>

                    </div>

                    <div class="col-md-2">

                        <input

                            type="text"

                            id="caliber"

                            class="form-control"

                            placeholder="Caliber"

                        >

                    </div>

                    <div class="col-md-2">

                        <input

                            type="text"

                            id="make"

                            class="form-control"

                            placeholder="Make"

                        >

                    </div>

                    <div class="col-md-3">

                        <button
                            class="btn btn-primary"
                            id="search">

                            Search

                        </button>

                        <button
                            class="btn btn-secondary"
                            id="reset">

                            Reset

                        </button>

                    </div>

                </div>

            </div>

        </div>

        {{-- DataTable --}}
        <div class="card shadow">

            <div class="card-body">

                <table
                    id="armsTable"
                    class="table table-bordered table-hover">

                    <thead>

                    <tr>

                        <th width="70">Photo</th>

                        <th>Property No</th>

                        <th>Serial No</th>

                        <th>Firearm</th>

                        <th>Caliber</th>

                        <th>Branch</th>

                        <th>Status</th>

                        <th>Current Holder</th>

                        <th width="180">
                            Actions
                        </th>

                    </tr>

                    </thead>

                </table>

            </div>

        </div>

    </div>

@endsection

@push('custom-scripts')

    @include('pages.arms.arms.datatable')

@endpush
