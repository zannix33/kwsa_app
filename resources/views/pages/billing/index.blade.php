@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')
<div class="container">
    <h1>Billing</h1>

    @if(session('unassigned_branches'))<div class="alert alert-warning"><strong>Skipped branches with no assigned company:</strong><ul>@foreach(session('unassigned_branches') as $branch)<li>{{ $branch['area_name'] }} — {{ $branch['branch_name'] }}</li>@endforeach</ul></div>@endif
@if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif

    @if($errors->any())
        <div class="alert alert-danger">
            <ul class="mb-0">
                @foreach($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    @endif

    <section class="card mb-4">
        <div class="card-header">Process billing by client, company, area and branch</div>
        <div class="card-body">
            @if($closedPeriods->isEmpty())
                <p>No closed payroll periods are available for billing.</p>
            @elseif($clients->isEmpty())
                <p>No clients are available.</p>
            @else
                <p>Select a client and a closed payroll period to reprocess billing. Missing branch bills will be created, existing draft bills will be recalculated and updated, and approved, issued or paid bills will be left unchanged.</p>
                <form method="POST" action="{{ route('billing.generate') }}">
                    @csrf
                    <div class="form-group">
                        <label for="client_id">Client</label>
                        <select class="form-control" name="client_id" id="client_id" required>
                            <option value="">Select a client</option>
                            @foreach($clients as $client)
                                <option value="{{ $client->id }}" {{ old('client_id') == $client->id ? 'selected' : '' }}>{{ $client->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="payroll_period_id">Closed payroll period</label>
                        <select class="form-control" name="payroll_period_id" id="payroll_period_id" required>
                            <option value="">Select a closed period</option>
                            @foreach($closedPeriods as $period)
                                <option value="{{ $period->id }}" {{ old('payroll_period_id') == $period->id ? 'selected' : '' }}>
                                    {{ $period->name ?? ('Period #' . $period->id) }} ({{ $period->date_from }} — {{ $period->date_to }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary" onclick="return confirm('Reprocess billing for this client and period? Existing draft bills will be updated. Approved, issued and paid bills will not be changed.')">Reprocess Billing</button>
                </form>
            @endif
        </div>
    </section>

    <section class="card">
        <div class="card-header">Previously generated bills</div>
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr><th>Bill</th><th>Client</th><th>Company</th><th>Area</th><th>Branch</th><th>Payroll period</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                @forelse($billings as $billing)
                    <tr>
                        <td>#{{ $billing->id }}</td>
                        <td>{{ optional($billing->client)->name ?? '—' }}</td>
                        <td>{{ optional($billing->company)->name ?? '—' }}</td>
                        <td>{{ optional($billing->area)->name ?? '—' }}</td>
                        <td>{{ optional($billing->branch)->name ?? '—' }}</td>
                        <td>{{ optional($billing->payrollPeriod)->name ?? ('Period #' . $billing->payroll_period_id) }}</td>
                        <td>{{ $billing->status ?? '—' }}</td>
                        <td>
                            <a href="{{ route('billing.show', $billing) }}">View</a> |
                            <a href="{{ route('billing.print', $billing) }}">Print</a> |
                            <a href="{{ route('billing.excel-preview', ['period' => $billing->payroll_period_id, 'company_id' => $billing->company_id, 'area_id' => $billing->area_id, 'branch_id' => $billing->branch_id]) }}">Excel preview</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="8">No bills have been generated yet.</td></tr>
                @endforelse
                </tbody>
            </table>
            {{ $billings->links() }}
        </div>
    </section>
</div>
@endsection
