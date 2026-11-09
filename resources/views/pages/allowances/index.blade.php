@extends('layout.master')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4"><div><h1 class="h3">Employee Allowances</h1><p class="text-muted mb-0">Agency-funded allowances included in payroll, not client billing.</p></div><a href="{{ route('allowances.create') }}" class="btn btn-primary">Assign Allowance</a></div>
    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
    @if($errors->any())<div class="alert alert-danger">{{ $errors->first() }}</div>@endif
    <div class="card mb-4"><div class="card-header"><h5 class="mb-0">Allowance Types</h5></div><div class="card-body">
        <form method="POST" action="{{ route('allowances.types.store') }}" class="form-row align-items-end mb-4">@csrf
            <div class="form-group col-md-3"><label>Name</label><input name="name" class="form-control" maxlength="120" required></div>
            <div class="form-group col-md-4"><label>Description (optional)</label><input name="description" class="form-control" maxlength="255"></div>
            <div class="form-group col-md-3"><label>Default amount per payroll (₱)</label><input name="default_amount" type="number" min="0.01" step="0.01" class="form-control" required></div>
            <div class="form-group col-md-2"><button class="btn btn-primary btn-block">Add Type</button></div>
        </form>
        <div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Allowance</th><th>Description</th><th>Default / Payroll</th><th>Assignments</th><th>Status</th><th></th></tr></thead><tbody>
        @forelse($types as $type)<tr><td>{{ $type->name }}</td><td>{{ $type->description ?: '—' }}</td><td>₱{{ number_format($type->default_amount,2) }}</td><td>{{ $type->employee_allowances_count }}</td><td><span class="badge badge-{{ $type->is_active ? 'success' : 'secondary' }}">{{ $type->is_active ? 'Active' : 'Inactive' }}</span></td><td><form method="POST" action="{{ route('allowances.types.toggle',$type) }}">@csrf<button class="btn btn-sm btn-outline-secondary">{{ $type->is_active ? 'Deactivate' : 'Activate' }}</button></form></td></tr>
        @empty<tr><td colspan="6" class="text-center">No allowance types created yet.</td></tr>@endforelse
        </tbody></table></div>
    </div></div>
    <div class="card mb-4"><div class="card-header"><h5 class="mb-0">Employee Assignments</h5></div><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Employee</th><th>Allowance</th><th>Amount / Payroll</th><th>Effective Period</th><th>Status</th><th></th></tr></thead><tbody>
    @forelse($assignments as $a)<tr><td>{{ optional($a->user)->lastname }}, {{ optional($a->user)->firstname }}<br><small class="text-muted">{{ optional($a->user)->employee_no }}</small></td><td>{{ optional($a->allowanceType)->name }}</td><td>₱{{ number_format($a->amount,2) }}</td><td>{{ $a->effective_from->format('M d, Y') }} – {{ $a->effective_to ? $a->effective_to->format('M d, Y') : 'Until ended' }}</td><td><span class="badge badge-{{ $a->status === 'Active' ? 'success' : 'secondary' }}">{{ $a->status }}</span></td><td><form method="POST" action="{{ route('allowances.toggle',$a) }}">@csrf<button class="btn btn-sm btn-outline-secondary">{{ $a->status === 'Active' ? 'Deactivate' : 'Activate' }}</button></form></td></tr>
    @empty<tr><td colspan="6" class="text-center">No employee allowances assigned.</td></tr>@endforelse
    </tbody></table></div><div class="card-body">{{ $assignments->links() }}</div></div>
    <div class="card"><div class="card-header"><h5 class="mb-0">Recent Payroll Allowance History</h5></div><div class="table-responsive"><table class="table table-striped mb-0"><thead><tr><th>Payroll Period</th><th>Employee</th><th>Description</th><th>Amount</th><th>Payroll Status</th></tr></thead><tbody>
    @forelse($history as $item)<tr><td>{{ optional(optional($item->payroll)->period)->name ?: (optional(optional($item->payroll)->period)->date_from ? optional($item->payroll->period)->date_from->format('M d, Y') : '—') }}</td><td>{{ optional(optional($item->payroll)->user)->lastname }}, {{ optional(optional($item->payroll)->user)->firstname }}</td><td>{{ $item->description }}</td><td>₱{{ number_format($item->amount,2) }}</td><td>{{ optional($item->payroll)->status }}</td></tr>
    @empty<tr><td colspan="5" class="text-center">No allowance payroll items have been generated yet.</td></tr>@endforelse
    </tbody></table></div></div>
</div>
@endsection
