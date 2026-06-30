@extends('layouts.app')

@section('title','Firearm Maintenance')

@section('content')

    <div class="container-fluid">

        <div class="d-flex justify-content-between mb-3">

            <div>

                <h3>

                    <i class="fas fa-tools"></i>

                    Firearm Maintenance

                </h3>

                <small class="text-muted">

                    Preventive & Corrective Maintenance

                </small>

            </div>

            <div>

                <a
                    href="{{ route('arms.maintenances.create') }}"
                    class="btn btn-primary">

                    <i class="fa fa-plus"></i>

                    Schedule Maintenance

                </a>

            </div>

        </div>

        @include('arms.maintenances.cards')

        <div class="card shadow">

            <div class="card-body">

                <table
                    id="maintenanceTable"
                    class="table table-hover table-bordered">

                    <thead>

                    <tr>

                        <th>Firearm</th>

                        <th>Property No.</th>

                        <th>Maintenance Type</th>

                        <th>Date</th>

                        <th>Technician</th>

                        <th>Cost</th>

                        <th>Status</th>

                        <th width="170">

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

    @include('pages.arms.maintenances.datatable')

@endpush
