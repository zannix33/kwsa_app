@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')

    <div class="container-fluid">

        <div class="d-flex justify-content-between mb-3">

            <h3>
                Incident Report
            </h3>

            <div>
                <a
                    href="{{ route('incidents.print',$incident) }}"
                    target="_blank"
                    class="btn btn-secondary">

                    <i class="fa fa-print"></i>

                    Print
                </a>


                <a
                    href="{{ route('incidents.edit',$incident) }}"
                    class="btn btn-warning">

                    Edit

                </a>

            </div>

        </div>

        <div class="card mt-3">

            <div class="card-header">

                Evidence Attachments

            </div>

            <div class="card-body">

                @forelse($incident->attachments as $attachment)

                    <div
                        class="d-flex justify-content-between border-bottom py-2">

                        <div>

                            <strong>

                                {{ $attachment->original_name }}

                            </strong>

                            <br>

                            <small>

                                {{ number_format($attachment->file_size/1024,2) }}

                                KB

                            </small>

                        </div>

                        <div>

                            <a
                                href="{{ asset('storage/'.$attachment->file_name) }}"
                                target="_blank"
                                class="btn btn-info btn-sm">
                                View
                            </a>
                            <div class="mb-3">

                                <a
                                    href="{{route('incidents.excel')}}"
                                    class="btn btn-success">

                                    Excel

                                </a>

                                <a
                                    href="{{route('incidents.pdf')}}"
                                    class="btn btn-danger">

                                    PDF

                                </a>

                                {{--

                                <a
                                    href="{{route('incidents.print')}}"
                                    class="btn btn-secondary">

                                    Print

                                </a>
                                --}}

                            </div>
                            <form
                                method="POST"
                                action="{{ route('incident.attachments.destroy',$attachment) }}"
                                style="display:inline">

                                @csrf

                                @method('DELETE')

                                <button
                                    class="btn btn-danger btn-sm"
                                    onclick="return confirm('Delete attachment?')">

                                    Delete

                                </button>

                            </form>

                        </div>


                    </div>

                @empty

                    <p>

                        No attachments uploaded.

                    </p>

                @endforelse

            </div>

        </div>


        <div class="card">

            <div class="card-header">
                Incident Information
            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-4">

                        <strong>Employee</strong>

                        <br>

                        {{ optional($incident->user)->full_name }}

                    </div>

                    <div class="col-md-4">

                        <strong>Branch</strong>

                        <br>

                        {{ optional($incident->branch)->name }}

                    </div>

                    <div class="col-md-4">

                        <strong>Area</strong>

                        <br>

                        {{ optional($incident->area)->name }}

                    </div>

                </div>

                <hr>

                <div class="row">

                    <div class="col-md-3">

                        <strong>Date</strong>

                        <br>

                        {{ $incident->incident_date->format('F d, Y') }}

                    </div>

                    <div class="col-md-3">

                        <strong>Time</strong>

                        <br>

                        {{ $incident->incident_time }}

                    </div>

                    <div class="col-md-3">

                        <strong>Category</strong>

                        <br>

                        @if($incident->category=="Major")

                            <span class="badge badge-danger">

                            Major

                        </span>

                        @else

                            <span class="badge badge-warning">

                            Minor

                        </span>

                        @endif

                    </div>

                    <div class="col-md-3">

                        <strong>Status</strong>

                        <br>

                        @switch($incident->status)

                            @case('Open')

                            <span class="badge badge-danger">

                            Open

                        </span>

                            @break

                            @case('Under Investigation')

                            <span class="badge badge-info">

                            Under Investigation

                        </span>

                            @break

                            @case('Resolved')

                            <span class="badge badge-success">

                            Resolved

                        </span>

                            @break

                            @default

                            <span class="badge badge-secondary">

                            Closed

                        </span>

                        @endswitch

                    </div>

                </div>

                <hr>

                <div class="row">

                    <div class="col-md-6">

                        <strong>Incident Type</strong>

                        <br>

                        {{ $incident->incident_type }}

                    </div>

                    <div class="col-md-6">

                        <strong>Location</strong>

                        <br>

                        {{ $incident->location }}

                    </div>

                </div>

            </div>

        </div>

        <br>

        <div class="card">

            <div class="card-header">

                Incident Description

            </div>

            <div class="card-body">

                {!! nl2br(e($incident->description)) !!}

            </div>

        </div>

        <br>

        <div class="card">

            <div class="card-header">

                Immediate Action Taken

            </div>

            <div class="card-body">

                {!! nl2br(e($incident->action_taken)) !!}

            </div>

        </div>

        <br>

        <div class="card">

            <div class="card-header">

                Recommendation

            </div>

            <div class="card-body">

                {!! nl2br(e($incident->recommendation)) !!}

            </div>

        </div>

        <br>

        <div class="card">

            <div class="card-header">

                Investigation

            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-6">

                        <strong>Reported By</strong>

                        <br>

                        {{ optional($incident->reporter)->name }}

                    </div>

                    <div class="col-md-6">

                        <strong>Investigated By</strong>

                        <br>

                        {{ optional($incident->investigator)->name }}

                    </div>

                </div>

                <hr>

                <strong>Created</strong>

                <br>

                {{ $incident->created_at->format('F d, Y h:i A') }}

                <hr>

                <strong>Last Updated</strong>

                <br>

                {{ $incident->updated_at->format('F d, Y h:i A') }}

            </div>

        </div>

        <br>

        <div class="text-right">

            <form
                action="{{ route('incidents.destroy',$incident) }}"
                method="POST">

                @csrf

                @method('DELETE')

                <button
                    class="btn btn-danger"
                    onclick="return confirm('Delete this incident report?')">

                    Delete Incident

                </button>

            </form>

        </div>

    </div>

@endsection
