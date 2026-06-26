@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')
    <div class="container-fluid">

        <div class="card">

            <div class="card-header d-flex justify-content-between align-items-center">

                <h5 class="mb-0">
                    Daily Time Records
                </h5>

                <a href="{{ route('dtr.create') }}"
                   class="btn btn-primary btn-sm">
                    Add DTR
                </a>

            </div>

            <div class="card-body">

                @if(session('success'))
                    <div class="alert alert-success">
                        {{ session('success') }}
                    </div>
                @endif

                <div class="table-responsive">

                    <table id="dtrTable" class="table table-bordered table-striped">

                        <thead>
                        <tr>
                            <th>Date</th>
                            <th>Employee</th>
                            <th>Time In</th>
                            <th>Time Out</th>
                            <th>Break</th>
                            <th>Regular</th>
                            <th>OT</th>
                            <th>ND</th>
                            <th>Total</th>
                            <th width="150">Action</th>
                        </tr>
                        </thead>

                        <tbody>

                        @forelse($dtrs as $dtr)

                            <tr>

                                <td>
                                    {{ \Carbon\Carbon::parse($dtr->work_date)->format('M d, Y') }}
                                </td>

                                <td>
                                    {{ $dtr->user->full_name }}
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($dtr->time_in)->format('M d, Y h:i A') }}
                                </td>

                                <td>
                                    {{ \Carbon\Carbon::parse($dtr->time_out)->format('M d, Y h:i A') }}
                                </td>

                                <td>
                                    {{ $dtr->break_minutes }}
                                </td>

                                <td>
                                    {{ number_format($dtr->regular_hours, 2) }}
                                </td>

                                <td>
                                    {{ number_format($dtr->overtime_hours, 2) }}
                                </td>

                                <td>
                                    {{ number_format($dtr->night_differential_hours, 2) }}
                                </td>

                                <td>
                                    {{ number_format($dtr->total_hours, 2) }}
                                </td>

                                <td>

                                    <a href="{{ route('dtr.edit', $dtr->id) }}"
                                       class="btn btn-warning btn-sm">
                                        Edit
                                    </a>

                                    <form action="{{ route('dtr.destroy', $dtr->id) }}"
                                          method="POST"
                                          style="display:inline-block">

                                        @csrf
                                        @method('DELETE')

                                        <button
                                            type="submit"
                                            onclick="return confirm('Delete this DTR?')"
                                            class="btn btn-danger btn-sm">
                                            Delete
                                        </button>

                                    </form>

                                </td>

                            </tr>

                        @empty

                            <tr>
                                <td colspan="10" class="text-center">
                                    No records found.
                                </td>
                            </tr>

                        @endforelse

                        </tbody>

                    </table>

                </div>

                <div class="mt-3">
                    {{ $dtrs->links() }}
                </div>

            </div>

        </div>

    </div>
@endsection

@push('custom-scripts')
    <script>
        $(document).ready(function () {
            $('#dtrTable').DataTable({
                pageLength: 25,
                responsive: true
            });
        });

    </script>
@endpush
