@extends('layouts.app')

@section('title','Firearm Inspections')

@section('content')

    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>

                <h3>
                    <i class="fas fa-search"></i>
                    Firearm Inspections
                </h3>

                <small class="text-muted">
                    Inspection records and scheduled inspections
                </small>

            </div>

            <div>

                <a href="{{ route('arms.inspections.create') }}"
                   class="btn btn-primary">

                    <i class="fa fa-plus"></i>

                    New Inspection

                </a>

            </div>

        </div>

        @include('arms.inspections.cards')

        <div class="card shadow">

            <div class="card-body">

                <table id="inspectionTable"
                       class="table table-hover table-bordered">

                    <thead>

                    <tr>

                        <th>Firearm</th>

                        <th>Property No.</th>

                        <th>Inspection Date</th>

                        <th>Inspector</th>

                        <th>Result</th>

                        <th>Next Inspection</th>

                        <th width="180">Actions</th>

                    </tr>

                    </thead>

                </table>

            </div>

        </div>

    </div>

@endsection

@section('custom-scripts')

    @include('arms.inspections.datatable')

@endsection
