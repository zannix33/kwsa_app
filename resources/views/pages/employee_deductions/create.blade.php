@extends('layout.master')
@section('content')
<div class="container-fluid"><h1 class="h3 mb-4">Add Fixed Employee Deduction</h1>
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ route('employee-deductions.store') }}" class="card"><div class="card-body">@csrf
<div class="form-group"><label>Employee</label><select name="user_id" class="form-control" required><option value="">Select employee</option>@foreach($users as $u)<option value="{{ $u->id }}" {{ old('user_id') == $u->id ? 'selected' : '' }}>{{ $u->lastname }}, {{ $u->firstname }} ({{ $u->employee_no }})</option>@endforeach</select></div>
<div class="form-group"><label>Deduction name / description</label><input name="description" class="form-control" required maxlength="255" value="{{ old('description') }}"></div>
<div class="form-group"><label>Fixed amount (₱)</label><input type="number" name="amount" min="0.01" step="0.01" class="form-control" required value="{{ old('amount') }}"></div>
<div class="form-group"><label>Frequency</label><select name="frequency" id="frequency" class="form-control" required><option value="one_time" {{ old('frequency') === 'one_time' ? 'selected' : '' }}>One Time</option><option value="every_payroll" {{ old('frequency') === 'every_payroll' ? 'selected' : '' }}>Every Payroll</option><option value="monthly" {{ old('frequency') === 'monthly' ? 'selected' : '' }}>Monthly</option></select></div>
<div class="form-row"><div class="form-group col-md-6"><label id="date-label">Deduction date</label><input type="date" name="start_date" class="form-control" required value="{{ old('start_date') }}"><small class="text-muted">The deduction is applied automatically to the first matching payroll period on or after this date.</small></div><div class="form-group col-md-6" id="end-date-group"><label>End date (optional)</label><input type="date" name="end_date" class="form-control" value="{{ old('end_date') }}"></div></div>
<div class="form-group" id="count-group"><label>Number of deductions</label><input type="number" name="installment_count" min="0" step="1" value="{{ old('installment_count', 0) }}" class="form-control" required><small class="text-muted">Set to 0 for unlimited deductions. It will continue until manually marked Fully Paid or made Inactive.</small></div>
<div class="form-group" id="cutoff-group"><label>Monthly payroll cutoff</label><select name="monthly_cutoff" class="form-control"><option value="first" {{ old('monthly_cutoff') === 'first' ? 'selected' : '' }}>1st Payroll (1–15)</option><option value="second" {{ old('monthly_cutoff') === 'second' ? 'selected' : '' }}>2nd Payroll (16–end of month)</option></select></div>
<button class="btn btn-primary">Save Deduction</button> <a href="{{ route('employee-deductions.index') }}" class="btn btn-light">Cancel</a>
</div></form></div>
<script>
document.addEventListener('DOMContentLoaded',function(){var f=document.getElementById('frequency'),cutoff=document.getElementById('cutoff-group'),count=document.getElementById('count-group'),end=document.getElementById('end-date-group'),label=document.getElementById('date-label');function sync(){cutoff.style.display=f.value==='monthly'?'':'none';count.style.display=f.value==='one_time'?'none':'';end.style.display=f.value==='one_time'?'none':'';label.textContent=f.value==='one_time'?'Deduction date':'Start date';}f.addEventListener('change',sync);sync();});
</script>
@endsection
