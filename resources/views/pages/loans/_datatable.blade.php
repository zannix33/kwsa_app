<div class="card shadow-sm">
    <div class="card-header d-flex justify-content-between align-items-center">
        <h5 class="mb-0">Loan Records</h5>

        <a href="{{ route('loans.create') }}" class="btn btn-primary btn-sm">
            <i class="fas fa-plus"></i> Add Loan
        </a>
    </div>

    <div class="card-body">
        {{-- Search and Filter --}}
        <form method="GET" action="{{ route('loans.index') }}" class="mb-3">
            <div class="form-row">

                <div class="col-md-4 mb-2">
                    <input
                        type="text"
                        name="search"
                        class="form-control"
                        placeholder="Search employee or reference..."
                        value="{{ request('search') }}"
                    >
                </div>

                <div class="col-md-3 mb-2">
                    <select name="loan_provider" class="form-control">
                        <option value="">All Providers</option>
                        <option value="SSS"
                            {{ request('loan_provider') == 'SSS' ? 'selected' : '' }}>
                            SSS
                        </option>
                        <option value="Pag-IBIG"
                            {{ request('loan_provider') == 'Pag-IBIG' ? 'selected' : '' }}>
                            Pag-IBIG
                        </option>
                        <option value="Company"
                            {{ request('loan_provider') == 'Company' ? 'selected' : '' }}>
                            Company
                        </option>
                    </select>
                </div>

                <div class="col-md-3 mb-2">
                    <select name="status" class="form-control">
                        <option value="">All Statuses</option>
                        <option value="Active"
                            {{ request('status') == 'Active' ? 'selected' : '' }}>
                            Active
                        </option>
                        <option value="Fully Paid"
                            {{ request('status') == 'Fully Paid' ? 'selected' : '' }}>
                            Fully Paid
                        </option>
                        <option value="Cancelled"
                            {{ request('status') == 'Cancelled' ? 'selected' : '' }}>
                            Cancelled
                        </option>
                    </select>
                </div>

                <div class="col-md-2 mb-2">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>

            </div>

            @if(request()->hasAny(['search', 'loan_provider', 'status']))
                <a href="{{ route('loans.index') }}" class="btn btn-sm btn-light">
                    Clear Filters
                </a>
            @endif
        </form>

        {{-- Loan Table --}}
        <div class="table-responsive">
            <table class="table table-bordered table-striped table-hover">
                <thead>
                <tr>
                    <th>Reference</th>
                    <th>Employee</th>
                    <th>Provider</th>
                    <th>Loan Type</th>
                    <th class="text-right">Loan Amount</th>
                    <th class="text-right">Monthly Deduction</th>
                    <th class="text-right">Outstanding</th>
                    <th>Status</th>
                    <th style="width: 130px;">Actions</th>
                </tr>
                </thead>

                <tbody>
                @forelse($loans as $loan)
                    <tr>
                        <td>{{ $loan->loan_reference }}</td>

                        <td>
                            @if($loan->user)
                                {{ $loan->user->employee_no }}
                                <br>
                                {{ $loan->user->firstname }}
                                {{ $loan->user->lastname }}
                            @else
                                <span class="text-muted">Employee unavailable</span>
                            @endif
                        </td>

                        <td>
                            @if($loan->loan_provider === 'SSS')
                                <span class="badge badge-primary">SSS</span>
                            @elseif($loan->loan_provider === 'Pag-IBIG')
                                <span class="badge badge-info">Pag-IBIG</span>
                            @else
                                <span class="badge badge-secondary">
                                        {{ $loan->loan_provider }}
                                    </span>
                            @endif
                        </td>

                        <td>{{ $loan->loan_type }}</td>

                        <td class="text-right">
                            ₱{{ number_format($loan->total_loan_amount, 2) }}
                        </td>

                        <td class="text-right">
                            ₱{{ number_format($loan->monthly_amortization, 2) }}
                        </td>

                        <td class="text-right">
                            ₱{{ number_format($loan->outstanding_balance, 2) }}
                        </td>

                        <td>
                            @if($loan->status === 'Active')
                                <span class="badge badge-success">Active</span>
                            @elseif($loan->status === 'Fully Paid')
                                <span class="badge badge-primary">Fully Paid</span>
                            @elseif($loan->status === 'Cancelled')
                                <span class="badge badge-danger">Cancelled</span>
                            @else
                                <span class="badge badge-secondary">
                                        {{ $loan->status }}
                                    </span>
                            @endif
                        </td>

                        <td>
                            <a href="{{ route('loans.show', $loan->id) }}"
                               class="btn btn-info btn-sm"
                               title="View Loan">
                                <i class="fas fa-eye"></i>
                            </a>

                            @if($loan->status !== 'Cancelled')
                                <a href="{{ route('loans.edit', $loan->id) }}"
                                   class="btn btn-warning btn-sm"
                                   title="Edit Loan">
                                    <i class="fas fa-edit"></i>
                                </a>
                            @endif
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="9" class="text-center text-muted">
                            No loan records found.
                        </td>
                    </tr>
                @endforelse
                </tbody>
            </table>
        </div>

        {{-- Pagination --}}
        <div class="d-flex justify-content-between align-items-center flex-wrap">
            <div class="text-muted small mb-2">
                Showing {{ $loans->firstItem() ?? 0 }}
                to {{ $loans->lastItem() ?? 0 }}
                of {{ $loans->total() }} records
            </div>

            <div class="mb-2">
                {{ $loans->appends(request()->query())->links() }}
            </div>
        </div>
    </div>
</div>
