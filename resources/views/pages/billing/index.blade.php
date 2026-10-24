@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')
<div class="container">
    <h1>Billing</h1>

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
        <div class="card-header">Start a billing worksheet</div>
        <div class="card-body">
            @if($closedPeriods->isEmpty())
                <p>No closed payroll periods are available for billing.</p>
            @else
                <form method="POST" action="{{ route('billing.preview.start') }}"  target="_blank">
                    @csrf
                    <div class="form-group">
                        <label for="payroll_period_id">Closed payroll period</label>
                        <select class="form-control" name="payroll_period_id" id="payroll_period_id" required>
                            <option value="">Select a closed period</option>
                            @foreach($closedPeriods as $period)
                                <option value="{{ $period->id }}" {{ old('payroll_period_id') == $period->id ? 'selected' : '' }}>
                                    {{ $period->name ?? ('Period #' . $period->id) }}
                                    ({{ $period->date_from }} — {{ $period->date_to }})
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="area_id">Area</label>
                        <select class="form-control" name="area_id" id="area_id" required>
                            <option value="">Select an area</option>
                            @foreach(\App\Models\Area::orderBy('name')->get() as $area)
                                <option value="{{ $area->id }}" {{ old('area_id') == $area->id ? 'selected' : '' }}>
                                    {{ $area->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Open Excel Preview</button>
                </form>
            @endif
        </div>
    </section>

    <section class="card">
        <div class="card-header">Previously generated bills</div>
        <div class="card-body table-responsive">
            <table class="table table-striped">
                <thead>
                    <tr><th>Bill</th><th>Client</th><th>Area</th><th>Payroll period</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                @forelse($billings as $billing)
                    <tr>
                        <td>#{{ $billing->id }}</td>
                        <td>{{ optional($billing->client)->name ?? '—' }}</td>
                        <td>{{ optional($billing->area)->name ?? '—' }}</td>
                        <td>{{ optional($billing->payrollPeriod)->name ?? ('Period #' . $billing->payroll_period_id) }}</td>
                        <td>{{ $billing->status ?? '—' }}</td>
                        <td>
                            <a href="{{ route('billing.show', $billing) }}">View</a> |
                            <a href="{{ route('billing.print', $billing) }}">Print</a>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6">No bills have been generated yet.</td></tr>
                @endforelse
                </tbody>
            </table>
            {{ $billings->links() }}
        </div>
    </section>
</div>
@endsection
