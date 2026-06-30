@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')

    <div class="container-fluid">

        <div class="row mb-3">

            <div class="col-md-12">

                <h2>

                    <i class="fa fa-shield-alt"></i>

                    Arms Management Dashboard

                </h2>

            </div>

        </div>

        @include('arms.partials.cards')

        <div class="row mt-4">

            <div class="col-md-6">

                <div class="card shadow">

                    <div class="card-header bg-primary text-white">

                        Monthly Maintenance Cost

                    </div>

                    <div class="card-body">

                        <canvas
                            id="maintenanceChart"
                            height="120"
                        ></canvas>

                    </div>

                </div>

            </div>

            <div class="col-md-6">

                <div class="card shadow">

                    <div class="card-header bg-success text-white">

                        Monthly Inspections

                    </div>

                    <div class="card-body">

                        <canvas
                            id="inspectionChart"
                            height="120"
                        ></canvas>

                    </div>

                </div>

            </div>

        </div>

        <div class="row mt-4">

            <div class="col-md-6">

                <div class="card shadow">

                    <div class="card-header bg-warning">

                        Expiring Licenses

                    </div>

                    <div class="card-body">

                        <table class="table table-bordered table-sm">

                            <thead>

                            <tr>

                                <th>Firearm</th>

                                <th>License</th>

                                <th>Expiry</th>

                            </tr>

                            </thead>

                            <tbody>

                            @foreach($expiredLicenses as $license)

                                <tr>

                                    <td>

                                        {{ $license->arm->full_name }}

                                    </td>

                                    <td>

                                        {{ $license->license_number }}

                                    </td>

                                    <td>

                                        {{ $license->expiry_date }}

                                    </td>

                                </tr>

                            @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

            <div class="col-md-6">

                <div class="card shadow">

                    <div class="card-header bg-danger text-white">

                        Low Stock Ammunition

                    </div>

                    <div class="card-body">

                        <table class="table table-bordered table-sm">

                            <thead>

                            <tr>

                                <th>Caliber</th>

                                <th>Available</th>

                            </tr>

                            </thead>

                            <tbody>

                            @foreach($lowStock as $ammo)

                                <tr>

                                    <td>

                                        {{ $ammo->caliber }}

                                    </td>

                                    <td>

                                        {{ $ammo->quantity_on_hand }}

                                    </td>

                                </tr>

                            @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection

@section('custom-scripts')

    @include('arms.partials.scripts')

@endsection
