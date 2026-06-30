@extends('layouts.app')

@section('title','Assignment Details')

@section('content')

    <div class="container-fluid">

        <div class="row">

            <div class="col-lg-4">

                <div class="card shadow">

                    <div class="card-header bg-dark text-white">

                        Firearm

                    </div>

                    <div class="card-body text-center">

                        <img
                            src="{{ $assignment->arm->photo_url }}"
                            class="img-fluid img-thumbnail mb-3">

                        <h5>

                            {{ $assignment->arm->manufacturer }}

                            {{ $assignment->arm->model }}

                        </h5>

                        <p>

                            {{ $assignment->arm->serial_number }}

                        </p>

                        {!! $assignment->arm->status_badge !!}

                    </div>

                </div>

            </div>

            <div class="col-lg-8">

                <div class="card shadow">

                    <div class="card-header">

                        Assignment Information

                    </div>

                    <table class="table table-bordered mb-0">

                        <tr>

                            <th width="220">

                                Employee

                            </th>

                            <td>

                                {{ $assignment->user->name }}

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Firearm

                            </th>

                            <td>

                                {{ $assignment->arm->manufacturer }}

                                {{ $assignment->arm->model }}

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Property Number

                            </th>

                            <td>

                                {{ $assignment->arm->property_no }}

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Serial Number

                            </th>

                            <td>

                                {{ $assignment->arm->serial_number }}

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Assigned Date

                            </th>

                            <td>

                                {{ $assignment->assigned_at->format('F d, Y') }}

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Expected Return

                            </th>

                            <td>

                                {{ optional($assignment->expected_return)->format('F d, Y') }}

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Purpose

                            </th>

                            <td>

                                {{ $assignment->purpose }}

                            </td>

                        </tr>

                        <tr>

                            <th>

                                Status

                            </th>

                            <td>

                                @if($assignment->returned_at)

                                    <span class="badge badge-success">

Returned

</span>

                                @else

                                    <span class="badge badge-warning">

Active

</span>

                                @endif

                            </td>

                        </tr>

                        @if($assignment->returned_at)

                            <tr>

                                <th>

                                    Returned Date

                                </th>

                                <td>

                                    {{ $assignment->returned_at->format('F d, Y') }}

                                </td>

                            </tr>

                            <tr>

                                <th>

                                    Condition

                                </th>

                                <td>

                                    {{ $assignment->condition }}

                                </td>

                            </tr>

                            <tr>

                                <th>

                                    Return Remarks

                                </th>

                                <td>

                                    {{ $assignment->return_remarks }}

                                </td>

                            </tr>

                        @endif

                    </table>

                </div>

            </div>

        </div>

        <div class="row mt-3">

            <div class="col-md-12">

                <div class="card shadow">

                    <div class="card-body">

                        <a
                            href="{{ route('arms.assignments.print',$assignment) }}"
                            target="_blank"
                            class="btn btn-primary">

                            <i class="fa fa-print"></i>

                            Print Assignment

                        </a>

                        <a
                            href="{{ route('arms.assignments.accountability',$assignment) }}"
                            target="_blank"
                            class="btn btn-success">

                            <i class="fa fa-file-alt"></i>

                            Print Accountability Form

                        </a>

                        <a
                            href="{{ route('arms.assignments.index') }}"
                            class="btn btn-secondary">

                            Back

                        </a>

                        @if(!$assignment->returned_at)

                            <a
                                href="{{ route('arms.assignments.edit',$assignment) }}"
                                class="btn btn-warning float-right">

                                Return Firearm

                            </a>

                        @endif

                    </div>

                </div>

            </div>

        </div>

    </div>

@endsection
