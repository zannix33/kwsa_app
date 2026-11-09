@extends('layout.master')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-4"><div><h1 class="h3">Assign Employee Allowance</h1><p class="text-muted mb-0">Set the full payroll-period amount and its effective dates.</p></div><a href="{{ route('allowances.index') }}" class="btn btn-light">Back</a></div>
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ route('allowances.store') }}" class="card"><div class="card-body">@csrf
        <div class="form-group"><label>Employee</label><select name="user_id" class="form-control" required><option value="">Select employee</option>@foreach($users as $u)<option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>{{ $u->lastname }}, {{ $u->firstname }} ({{ $u->employee_no }})</option>@endforeach</select></div>
        <div class="form-group"><label>Allowance type</label><select name="allowance_type_id" id="allowance_type_id" class="form-control" required><option value="">Select allowance</option>@foreach($types as $type)<option value="{{ $type->id }}" data-amount="{{ $type->default_amount }}" {{ old('allowance_type_id') == $type->id ? 'selected' : '' }}>{{ $type->name }} — ₱{{ number_format($type->default_amount,2) }}</option>@endforeach</select></div>
        <div class="form-group"><label>Full amount per payroll period (₱)</label><input type="number" name="amount" id="amount" min="0.01" step="0.01" class="form-control" value="{{ old('amount') }}" required><small class="text-muted">This is the amount before attendance and effective-date proration.</small></div>
        <div class="form-row"><div class="form-group col-md-6"><label>Effective from</label><input type="date" name="effective_from" class="form-control" value="{{ old('effective_from', date('Y-m-d')) }}" required></div><div class="form-group col-md-6"><label>Effective to (optional)</label><input type="date" name="effective_to" class="form-control" value="{{ old('effective_to') }}"></div></div>
        <button class="btn btn-primary">Assign Allowance</button> <a href="{{ route('allowances.index') }}" class="btn btn-light">Cancel</a>
    </div></form>
</div>
<script>
document.addEventListener('DOMContentLoaded',function(){var t=document.getElementById('allowance_type_id'),a=document.getElementById('amount');t.addEventListener('change',function(){var o=t.options[t.selectedIndex];if(o&&o.dataset.amount){a.value=o.dataset.amount;}});});
</script>
@endsection
