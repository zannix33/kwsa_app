@extends('layouts.app')

@section('title', 'Firearm Assignments')

@section('content')

    <div class="container-fluid">

        <!-- Header -->
        <div class="d-flex justify-content-between align-items-center mb-3">

            <div>

                <h3 class="mb-0">
                    <i class="fas fa-user-shield"></i>
                    Firearm Assignments
                </h3>

                <small class="text-muted">
                    Assign, return and monitor issued firearms
                </small>

            </div>

            <div>

                <a href="{{ route('arms.assignments.create') }}"
                   class="btn btn-primary">

                    <i class="fa fa-plus"></i>

                    New Assignment

                </a>

            </div>

        </div>

        <!-- Summary Cards -->
        <div class="row mb-3">

            <div class="col-md-3">

                <div class="small-box bg-primary">

                    <div class="inner">

                        <h3>{{ $summary['active'] ?? 0 }}</h3>

                        <p>Active Assignments</p>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="small-box bg-success">

                    <div class="inner">

                        <h3>{{ $summary['returned_today'] ?? 0 }}</h3>

                        <p>Returned Today</p>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="small-box bg-warning">

                    <div class="inner">

                        <h3>{{ $summary['issued_today'] ?? 0 }}</h3>

                        <p>Issued Today</p>

                    </div>

                </div>

            </div>

            <div class="col-md-3">

                <div class="small-box bg-danger">

                    <div class="inner">

                        <h3>{{ $summary['overdue'] ?? 0 }}</h3>

                        <p>Overdue Returns</p>

                    </div>

                </div>

            </div>

        </div>

        <!-- Filters -->
        <div class="card shadow mb-3">

            <div class="card-header">

                Search Filters

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-3">

                        <select
                            id="employee"
                            class="form-control select2">

                            <option value="">

                                All Employees

                            </option>

                            @foreach($users as $user)

                                <option value="{{ $user->id }}">

                                    {{ $user->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="col-md-3">

                        <select
                            id="branch"
                            class="form-control select2">

                            <option value="">

                                All Branches

                            </option>

                            @foreach($branches as $branch)

                                <option value="{{ $branch->id }}">

                                    {{ $branch->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="col-md-2">

                        <select
                            id="status"
                            class="form-control">

                            <option value="">

                                All Status

                            </option>

                            <option value="Active">

                                Active

                            </option>

                            <option value="Returned">

                                Returned

                            </option>

                        </select>

                    </div>

                    <div class="col-md-2">

                        <input
                            type="date"
                            id="assigned_date"
                            class="form-control">

                    </div>

                    <div class="col-md-2 text-right">

                        <button
                            id="search"
                            class="btn btn-primary">

                            Search

                        </button>

                        <button
                            id="reset"
                            class="btn btn-secondary">

                            Reset

                        </button>

                    </div>

                </div>

            </div>

        </div>

        <!-- DataTable -->
        <div class="card shadow">

            <div class="card-body">

                <table
                    id="assignmentTable"
                    class="table table-hover table-bordered">

                    <thead>

                    <tr>

                        <th>Firearm</th>

                        <th>Property No.</th>

                        <th>Employee</th>

                        <th>Branch</th>

                        <th>Assigned</th>

                        <th>Expected Return</th>

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

    @include('arms.assignments.datatable')

@endpush
