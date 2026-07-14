@extends('layouts.app')

@section('title','Ammunition Inventory')

@section('content')

    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>

                <h3>

                    <i class="fas fa-bullseye"></i>

                    Ammunition Inventory

                </h3>

                <small class="text-muted">

                    Inventory, Stock Control and Batch Tracking

                </small>

            </div>

            <div>

                <a href="{{ route('arms.ammunition.create') }}"
                   class="btn btn-primary">

                    <i class="fa fa-plus"></i>

                    Add Stock

                </a>

            </div>

        </div>

        @include('arms.ammunition.cards')

        <div class="card shadow">

            <div class="card-body">

                <table
                    id="ammunitionTable"
                    class="table table-bordered table-hover">

                    <thead>

                    <tr>

                        <th>Caliber</th>

                        <th>Brand</th>

                        <th>Lot No.</th>

                        <th>Quantity</th>

                        <th>Unit Cost</th>

                        <th>Total Value</th>

                        <th>Expiry</th>

                        <th>Status</th>

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

    @include('arms.ammunition.datatable')

@endpush
