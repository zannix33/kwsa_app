@extends('layout.master')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <div><h1 class="h3">Fixed Employee Deductions</h1><p class="text-muted">Scheduled payroll deductions, separate from employee loans.</p></div>
        <a class="btn btn-primary" href="{{ route('employee-deductions.create') }}">Add Fixed Deduction</a>
    </div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
    <div class="card"><div class="table-responsive"><table class="table table-striped mb-0">
        <thead><tr><th>Employee</th><th>Description</th><th>Amount</th><th>Frequency</th><th>Processed</th><th>Limit</th><th>Status</th><th></th></tr></thead>
        <tbody>@forelse($deductions as $d)
            <tr><td>{{ $d->user->lastname }}, {{ $d->user->firstname }}</td><td>{{ $d->description }}</td><td>₱{{ number_format($d->amount,2) }}</td><td>{{ ucwords(str_replace('_',' ',$d->frequency)) }}</td><td>{{ $d->periods->where('status','Processed')->count() }}</td><td>{{ (int) $d->installment_count === 0 ? 'Unlimited' : $d->installment_count }}</td><td><span class="badge badge-{{ $d->payment_status === 'Fully Paid' ? 'success' : ($d->status === 'Active' ? 'primary' : 'secondary') }}">{{ $d->payment_status === 'Fully Paid' ? 'Fully Paid · Inactive' : $d->status }}</span></td><td><a class="btn btn-sm btn-info" href="{{ route('employee-deductions.show',$d) }}">History</a> @if($d->payment_status !== 'Fully Paid')<form class="d-inline" method="POST" action="{{ route('employee-deductions.mark-paid',$d) }}">@csrf<button class="btn btn-sm btn-outline-success" onclick="return confirm('Mark this deduction fully paid?')">Mark Paid</button></form><form class="d-inline" method="POST" action="{{ route('employee-deductions.toggle',$d) }}">@csrf<button class="btn btn-sm btn-outline-secondary">{{ $d->status === 'Active' ? 'Deactivate' : 'Activate' }}</button></form>@endif</td></tr>
        @empty<tr><td colspan="8" class="text-center">No fixed deductions found.</td></tr>@endforelse</tbody>
    </table></div><div class="card-body">{{ $deductions->links() }}</div></div>
</div>
@endsection
