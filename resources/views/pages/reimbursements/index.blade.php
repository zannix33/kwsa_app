@extends('layout.master')

@section('content')
<div class="content-wrapper">
    <div class="page-header"><h3 class="page-title">Employee Reimbursements</h3></div>
    @if(session('success')) <div class="alert alert-success">{{ session('success') }}</div> @endif
    @if($errors->any()) <div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div> @endif

    <div class="card mb-4"><div class="card-body">
        <h4 class="card-title">File Reimbursement</h4>
        <p class="text-muted">Filing records the reimbursement only. During payroll processing, the system matches it to the payroll period containing the filing date. No approval step is required.</p>
        <form method="POST" action="{{ route('reimbursements.store') }}" enctype="multipart/form-data">
            @csrf
            <div class="form-row">
                <div class="form-group col-md-4"><label>Employee</label><select name="user_id" class="form-control" required><option value="">Select employee</option>@foreach($users as $user)<option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->lastname }}, {{ $user->firstname }}</option>@endforeach</select></div>
                <div class="form-group col-md-3"><label>Date Filed</label><input type="date" name="filed_date" class="form-control" value="{{ old('filed_date', date('Y-m-d')) }}" required></div>
                <div class="form-group col-md-3"><label>Category</label><input name="category" class="form-control" value="{{ old('category') }}" placeholder="e.g. Transportation" required maxlength="120"></div>
                <div class="form-group col-md-2"><label>Amount (₱)</label><input type="number" name="amount" class="form-control" value="{{ old('amount') }}" min="0.01" step="0.01" required></div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-8"><label>Description</label><input name="description" class="form-control" value="{{ old('description') }}" maxlength="255" required></div>
                <div class="form-group col-md-4"><label>Receipt (optional)</label><input type="file" name="receipt" class="form-control-file" accept=".jpg,.jpeg,.png,.pdf"><small class="form-text text-muted">JPG, PNG or PDF; max 5 MB.</small></div>
            </div>
            <button class="btn btn-primary" type="submit">File Reimbursement</button>
        </form>
    </div></div>

    <div class="card"><div class="card-body"><h4 class="card-title">Reimbursement History</h4>
        <div class="table-responsive"><table class="table table-striped table-hover"><thead><tr><th>Date Filed</th><th>Employee</th><th>Category / Description</th><th class="text-right">Amount</th><th>Payroll Period</th><th>Status</th><th>Receipt</th><th></th></tr></thead><tbody>
        @forelse($reimbursements as $item)
            <tr><td>{{ optional($item->filed_date)->format('M d, Y') }}</td><td>{{ $item->user ? $item->user->lastname . ', ' . $item->user->firstname : '—' }}</td><td><strong>{{ $item->category }}</strong><br>{{ $item->description }}</td><td class="text-right">₱{{ number_format($item->amount, 2) }}</td><td>{{ $item->payrollPeriod ? $item->payrollPeriod->name : '—' }}</td><td><span class="badge badge-{{ $item->status === 'Included' ? 'success' : 'secondary' }}">{{ $item->status }}</span></td><td>@if($item->receipt_path)<a href="{{ asset('storage/'.$item->receipt_path) }}" target="_blank">View</a>@else—@endif</td><td>@if(!$item->payroll_id)<form method="POST" action="{{ route('reimbursements.destroy', $item) }}" onsubmit="return confirm('Delete this reimbursement?')">@csrf @method('DELETE')<button class="btn btn-sm btn-outline-danger">Delete</button></form>@endif</td></tr>
        @empty <tr><td colspan="8" class="text-center text-muted">No reimbursements filed yet.</td></tr> @endforelse
        </tbody></table></div>
        {{ $reimbursements->links() }}
    </div></div>
</div>
@endsection
