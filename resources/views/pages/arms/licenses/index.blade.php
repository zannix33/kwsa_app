@extends('layouts.app')

@section('title','Firearm Licenses')

@section('content')

    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>

                <h3>

                    <i class="fas fa-id-card"></i>

                    Firearm Licenses

                </h3>

                <small class="text-muted">

                    License Registration & Renewal Monitoring

                </small>

            </div>

            <div>

                <a href="{{ route('arms.licenses.create') }}"
                   class="btn btn-primary">

                    <i class="fa fa-plus"></i>

                    Register License

                </a>

            </div>

        </div>

        @include('arms.licenses.cards')

        <div class="card shadow">

            <div class="card-body">

                <table
                    id="licenseTable"
                    class="table table-hover table-bordered">

                    <thead>

                    <tr>

                        <th>Firearm</th>

                        <th>License No.</th>

                        <th>Registration</th>

                        <th>Issue Date</th>

                        <th>Expiry Date</th>

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

    @include('arms.licenses.datatable')

@endpush
