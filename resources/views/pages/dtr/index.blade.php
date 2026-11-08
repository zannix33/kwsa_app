@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush


@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Daily Time Records</h3>
        <a href="{{ route('dtr.bulk.create') }}" class="btn btn-primary">Bulk DTR Entry</a>
    </div>
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
    <form method="GET" action="{{ route('dtr.index') }}" class="card card-body mb-3">
        <div class="row">
            <div class="col-md-3"><label>Employee</label><input name="employee" class="form-control" value="{{ request('employee') }}" placeholder="First or last name"></div>
            <div class="col-md-3"><label>From</label><input type="date" name="from_date" class="form-control" value="{{ request('from_date') }}"></div>
            <div class="col-md-3"><label>To</label><input type="date" name="to_date" class="form-control" value="{{ request('to_date') }}"></div>
            <div class="col-md-3 d-flex align-items-end"><button class="btn btn-secondary mr-2">Search</button><a class="btn btn-light" href="{{ route('dtr.index') }}">Clear</a></div>
        </div>
    </form>
    <div class="table-responsive">
    <table class="table table-bordered table-striped table-sm">
        <thead><tr>
            <th>Date</th><th>Employee</th><th>Area / Branch</th><th>Time In</th><th>Time Out</th>
            <th>Regular Hrs</th><th>OT Hrs</th><th>Extended Hrs</th><th>Night Diff Hrs</th><th>Late Hrs</th><th>Undertime Hrs</th><th>Payroll</th><th>Action</th>
        </tr></thead>
        <tbody>
        @forelse($records as $record)
            <tr>
                <td>{{ optional($record->work_date)->format('M d, Y') }}</td>
                <td>{{ optional($record->user)->firstname }} {{ optional($record->user)->lastname }}</td>
                <td>{{ optional($record->area)->name ?: '—' }} @if($record->branch) / {{ $record->branch->name }} @endif</td>
                <td>{{ $record->time_in ? \Carbon\Carbon::parse($record->time_in)->format('h:i A') : '—' }}</td>
                <td>{{ $record->time_out ? \Carbon\Carbon::parse($record->time_out)->format('h:i A') : '—' }}</td>
                <td class="text-right">{{ number_format((float)$record->regular_hours,2) }}</td>
                <td class="text-right">{{ number_format((float)$record->overtime_hours,2) }}</td>
                <td class="text-right">{{ number_format((float)$record->extended_hours,2) }}</td>
                <td class="text-right">{{ number_format((float)$record->night_differential_hours,2) }}</td>
                <td class="text-right">{{ number_format((float)$record->late_hours,2) }}</td>
                <td class="text-right">{{ number_format((float)$record->undertime_hours,2) }}</td>
                <td>{{ $record->payroll ? '#'.$record->payroll->id.' ('.$record->payroll->status.')' : 'Unprocessed' }}</td>
                <td class="text-nowrap">
                    <a class="btn btn-sm btn-info" href="{{ route('dtr.edit',$record) }}">Edit</a>
                    @if($record->payroll && strtolower($record->payroll->status) !== 'paid')
                    <form method="POST" action="{{ route('dtr.payroll.reprocess',$record->payroll) }}" class="d-inline" onsubmit="return confirm('Reprocess attendance items for this employee payroll? Existing loan and other scheduled deductions will be preserved.')">
                        @csrf <button class="btn btn-sm btn-warning">Reprocess</button>
                    </form>
                    @endif
                </td>
            </tr>
        @empty <tr><td colspan="13" class="text-center">No DTR records found.</td></tr> @endforelse
        </tbody>
    </table>
    </div>
    {{ $records->links() }}
</div>
@endsection
