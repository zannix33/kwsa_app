@extends('layouts.app')

@section('title','Ammunition Releases')

@section('content')

    <div class="container-fluid">

        <div class="d-flex justify-content-between mb-3">

            <div>

                <h3>

                    <i class="fas fa-box-open"></i>

                    Ammunition Releases

                </h3>

                <small class="text-muted">

                    Issue and Accountability

                </small>

            </div>

            <a
                href="{{ route('arms.ammunition-releases.create') }}"
                class="btn btn-primary">

                <i class="fa fa-plus"></i>

                New Release

            </a>

        </div>

        @include('arms.ammunition-releases.cards')

        <div class="card shadow">

            <div class="card-body">

                <table
                    id="releaseTable"
                    class="table table-bordered table-hover">

                    <thead>

                    <tr>

                        <th>Release #</th>

                        <th>Date</th>

                        <th>Employee</th>

                        <th>Branch</th>

                        <th>Purpose</th>

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
