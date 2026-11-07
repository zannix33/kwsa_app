@extends('layout.master')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Maintenance History — {{ $arm->full_name }}</h3>
        <a href="{{ route('arms.maintenances.create', ['arm_id' => $arm->id]) }}" class="btn btn-primary">New Maintenance</a>
    </div>
    <div class="card"><div class="card-body">
        <div class="table-responsive"><table class="table table-bordered table-striped">
            <thead><tr><th>Date</th><th>Type</th><th>Performed By</th><th>Condition</th><th>Cost</th><th>Status</th><th></th></tr></thead>
            <tbody>
            @forelse($arm->maintenances()->latest('maintenance_date')->get() as $item)
                <tr><td>{{ optional($item->maintenance_date)->format('M d, Y') }}</td><td>{{ $item->maintenance_type }}</td><td>{{ $item->performed_by }}</td><td>{{ $item->condition_after }}</td><td>{{ number_format($item->total_cost, 2) }}</td><td>{{ $item->completed ? 'Completed' : 'Pending' }}</td><td><a class="btn btn-sm btn-info" href="{{ route('arms.maintenances.show', $item) }}">View</a></td></tr>
            @empty<tr><td colspan="7" class="text-center text-muted">No maintenance records found.</td></tr>@endforelse
            </tbody>
        </table></div>
    </div></div>
</div>
@endsection
