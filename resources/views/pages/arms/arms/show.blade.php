@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')

    <div class="container-fluid">

        <div class="row mb-3">

            <div class="col-md-8">

                <h2>

                    <i class="fas fa-crosshairs"></i>

                    {{ $arm->manufacturer }}
                    {{ $arm->model }}

                </h2>

                <small class="text-muted">

                    Property No:
                    {{ $arm->property_no }}

                </small>

            </div>

            <div class="col-md-4 text-right">

                <a href="{{ route('arms.edit',$arm) }}"
                   class="btn btn-primary">

                    <i class="fa fa-edit"></i>

                    Edit

                </a>

                <a href="{{ route('arms.index') }}"
                   class="btn btn-secondary">

                    Back

                </a>

            </div>

        </div>

        <div class="row">

            <!-- LEFT SIDE -->

            <div class="col-lg-4">

                <div class="card shadow">

                    <div class="card-body text-center">

                        <img

                            src="{{ $arm->photo_url }}"

                            class="img-fluid img-thumbnail mb-3">

                        <h4>

                            {{ $arm->manufacturer }}

                        </h4>

                        <p>

                            {{ $arm->model }}

                        </p>

                        {!! $arm->status_badge !!}

                        <hr>

                        {!! $arm->qr_code !!}

                        <hr>

                        {!! $arm->barcode !!}

                    </div>

                </div>

            </div>

            <!-- RIGHT SIDE -->

            <div class="col-lg-8">

                <div class="card shadow">

                    <div class="card-header">

                        Firearm Information

                    </div>

                    <table class="table table-bordered mb-0">

                        <tr>

                            <th width="220">

                                Property Number

                            </th>

                            <td>

                                {{ $arm->property_no }}

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Serial Number

                            </th>

                            <td>

                                {{ $arm->serial_number }}

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Type

                            </th>

                            <td>

                                {{ $arm->type }}

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Caliber

                            </th>

                            <td>

                                {{ $arm->caliber }}

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Manufacturer

                            </th>

                            <td>

                                {{ $arm->manufacturer }}

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Model

                            </th>

                            <td>

                                {{ $arm->model }}

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Purchase Date

                            </th>

                            <td>

                                {{ optional($arm->purchase_date)->format('M d, Y') }}

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Purchase Cost

                            </th>

                            <td>

                                ₱ {{ number_format($arm->purchase_cost,2) }}

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Branch

                            </th>

                            <td>

                                {{ optional($arm->branch)->name }}

                            </td>

                        </tr>

                    </table>

                </div>

            </div>

        </div>

        <br>

        <!-- CURRENT ASSIGNMENT -->

        <div class="card shadow">

            <div class="card-header bg-primary text-white">

                Current Assignment

            </div>

            <div class="card-body">

                @if($arm->activeAssignment)

                    <table class="table table-bordered">

                        <tr>

                            <th width="220">

                                Employee

                            </th>

                            <td>

                                {{ $arm->activeAssignment->user->name }}

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Assigned Date

                            </th>

                            <td>

                                {{ $arm->activeAssignment->assigned_at }}

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Purpose

                            </th>

                            <td>

                                {{ $arm->activeAssignment->purpose }}

                            </td>

                        </tr>

                    </table>

                @else

                    <div class="alert alert-success">

                        Firearm is currently available.

                    </div>

                @endif

            </div>

        </div>

        <br>

        <!-- TABS -->

        <div class="card shadow">

            <div class="card-header">

                Asset History

            </div>

            <div class="card-body">

                <ul class="nav nav-tabs">

                    <li class="nav-item">

                        <a
                            class="nav-link active"
                            data-toggle="tab"
                            href="#assignment">

                            Assignment

                        </a>

                    </li>

                    <li class="nav-item">

                        <a
                            class="nav-link"
                            data-toggle="tab"
                            href="#maintenance">

                            Maintenance

                        </a>

                    </li>

                    <li class="nav-item">

                        <a
                            class="nav-link"
                            data-toggle="tab"
                            href="#inspection">

                            Inspection

                        </a>

                    </li>

                    <li class="nav-item">

                        <a
                            class="nav-link"
                            data-toggle="tab"
                            href="#license">

                            License

                        </a>

                    </li>

                </ul>

                <div class="tab-content mt-3">

                    <div
                        class="tab-pane fade show active"
                        id="assignment">

                        @include('arms.arms.tabs.assignment')

                    </div>

                    <div
                        class="tab-pane fade"
                        id="maintenance">

                        @include('pages.arms.arms.tabs.maintenance')

                    </div>

                    <div
                        class="tab-pane fade"
                        id="inspection">

                        @include('pages.arms.arms.tabs.inspection')

                    </div>

                    <div
                        class="tab-pane fade"
                        id="license">

                        @include('pages.arms.arms.tabs.license')

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
