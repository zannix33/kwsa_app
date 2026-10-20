@php
    $isEdit = isset($loan);
@endphp

{{-- Loan Information --}}
<div class="card shadow-sm mb-4">
    <div class="card-header">
        <h5 class="mb-0 text-primary">Loan Information</h5>
    </div>

    <div class="card-body">
        <div class="row">

            {{-- Employee --}}
            <div class="col-md-6">
                <div class="form-group">
                    <label for="user_id">
                        Employee <span class="text-danger">*</span>
                    </label>

                    <select
                        name="user_id"
                        id="user_id"
                        class="form-control @error('user_id') is-invalid @enderror"
                        required
                    >
                        <option value="">Select Employee</option>

                        @foreach($users as $user)
                            <option
                                value="{{ $user->id }}"
                                {{ old('user_id', $isEdit ? $loan->user_id : '') == $user->id ? 'selected' : '' }}
                            >
                                {{ $user->employee_no }}
                                - {{ $user->firstname }} {{ $user->lastname }}
                            </option>
                        @endforeach
                    </select>

                    @error('user_id')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Loan Provider --}}
            <div class="col-md-6">
                <div class="form-group">
                    <label for="loan_provider">
                        Loan Provider <span class="text-danger">*</span>
                    </label>

                    <select
                        name="loan_provider"
                        id="loan_provider"
                        class="form-control @error('loan_provider') is-invalid @enderror"
                        required
                    >
                        <option value="">Select Provider</option>

                        @foreach(['SSS', 'Pag-IBIG', 'Company'] as $provider)
                            <option
                                value="{{ $provider }}"
                                {{ old('loan_provider', $isEdit ? $loan->loan_provider : '') == $provider ? 'selected' : '' }}
                            >
                                {{ $provider }}
                            </option>
                        @endforeach
                    </select>

                    @error('loan_provider')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Loan Type --}}
            <div class="col-md-6">
                <div class="form-group">
                    <label for="loan_type">
                        Loan Type <span class="text-danger">*</span>
                    </label>

                    <select
                        name="loan_type"
                        id="loan_type"
                        class="form-control @error('loan_type') is-invalid @enderror"
                        data-current-type="{{ old('loan_type', $isEdit ? $loan->loan_type : '') }}"
                        required
                    >
                        <option value="">Select Loan Type</option>
                    </select>

                    @error('loan_type')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Loan Reference --}}
            <div class="col-md-6">
                <div class="form-group">
                    <label for="loan_reference">
                        Loan Reference <span class="text-danger">*</span>
                    </label>

                    <input
                        type="text"
                        name="loan_reference"
                        id="loan_reference"
                        class="form-control @error('loan_reference') is-invalid @enderror"
                        value="{{ old('loan_reference', $isEdit ? $loan->loan_reference : '') }}"
                        maxlength="100"
                        required
                    >

                    @error('loan_reference')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Loan Date --}}
            <div class="col-md-4">
                <div class="form-group">
                    <label for="loan_date">
                        Loan Date <span class="text-danger">*</span>
                    </label>

                    <input
                        type="date"
                        name="loan_date"
                        id="loan_date"
                        class="form-control @error('loan_date') is-invalid @enderror"
                        value="{{ old('loan_date', $isEdit && $loan->loan_date ? $loan->loan_date->format('Y-m-d') : '') }}"
                        required
                    >

                    @error('loan_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Principal --}}
            <div class="col-md-4">
                <div class="form-group">
                    <label for="principal_amount">
                        Principal Amount (₱) <span class="text-danger">*</span>
                    </label>

                    <input
                        type="number"
                        name="principal_amount"
                        id="principal_amount"
                        class="form-control @error('principal_amount') is-invalid @enderror"
                        value="{{ old('principal_amount', $isEdit ? $loan->principal_amount : '') }}"
                        min="0.01"
                        step="0.01"
                        required
                    >

                    @error('principal_amount')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Interest --}}
            <div class="col-md-4">
                <div class="form-group">
                    <label for="interest_amount">Interest Amount (₱)</label>

                    <input
                        type="number"
                        name="interest_amount"
                        id="interest_amount"
                        class="form-control @error('interest_amount') is-invalid @enderror"
                        value="{{ old('interest_amount', $isEdit ? $loan->interest_amount : '0.00') }}"
                        min="0"
                        step="0.01"
                    >

                    @error('interest_amount')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Total Loan Amount --}}
            <div class="col-md-4">
                <div class="form-group">
                    <label for="total_loan_amount">
                        Total Loan Amount (₱) <span class="text-danger">*</span>
                    </label>

                    <input
                        type="number"
                        name="total_loan_amount"
                        id="total_loan_amount"
                        class="form-control @error('total_loan_amount') is-invalid @enderror"
                        value="{{ old('total_loan_amount', $isEdit ? $loan->total_loan_amount : '') }}"
                        min="0.01"
                        step="0.01"
                        readonly
                        required
                    >

                    @error('total_loan_amount')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <small class="form-text text-muted">
                        Automatically calculated from principal plus interest.
                    </small>
                </div>
            </div>

            {{-- Monthly Amortization --}}
            <div class="col-md-4">
                <div class="form-group">
                    <label for="monthly_amortization">
                        Monthly Amortization (₱) <span class="text-danger">*</span>
                    </label>

                    <input
                        type="number"
                        name="monthly_amortization"
                        id="monthly_amortization"
                        class="form-control @error('monthly_amortization') is-invalid @enderror"
                        value="{{ old('monthly_amortization', $isEdit ? $loan->monthly_amortization : '') }}"
                        min="0.01"
                        step="0.01"
                        required
                    >

                    @error('monthly_amortization')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Deduction Start Date --}}
            <div class="col-md-4">
                <div class="form-group">
                    <label for="deduction_start_date">
                        Deduction Start Date <span class="text-danger">*</span>
                    </label>

                    <input
                        type="date"
                        name="deduction_start_date"
                        id="deduction_start_date"
                        class="form-control @error('deduction_start_date') is-invalid @enderror"
                        value="{{ old('deduction_start_date', $isEdit && $loan->deduction_start_date ? $loan->deduction_start_date->format('Y-m-d') : '') }}"
                        required
                    >

                    @error('deduction_start_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Deduction End Date --}}
            <div class="col-md-4">
                <div class="form-group">
                    <label for="deduction_end_date">Deduction End Date</label>

                    <input
                        type="date"
                        name="deduction_end_date"
                        id="deduction_end_date"
                        class="form-control @error('deduction_end_date') is-invalid @enderror"
                        value="{{ old('deduction_end_date', $isEdit && $loan->deduction_end_date ? $loan->deduction_end_date->format('Y-m-d') : '') }}"
                    >

                    @error('deduction_end_date')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror

                    <small class="form-text text-muted">
                        Optional.
                    </small>
                </div>
            </div>

            {{-- Remarks --}}
            <div class="col-md-12">
                <div class="form-group">
                    <label for="remarks">Remarks</label>

                    <textarea
                        name="remarks"
                        id="remarks"
                        rows="3"
                        class="form-control @error('remarks') is-invalid @enderror"
                    >{{ old('remarks', $isEdit ? $loan->remarks : '') }}</textarea>

                    @error('remarks')
                    <div class="invalid-feedback">{{ $message }}</div>
                    @enderror
                </div>
            </div>

            {{-- Status: Edit Only --}}
            @if($isEdit)
                <div class="col-md-4">
                    <div class="form-group">
                        <label for="status">
                            Loan Status <span class="text-danger">*</span>
                        </label>

                        <select
                            name="status"
                            id="status"
                            class="form-control @error('status') is-invalid @enderror"
                            required
                        >
                            @foreach(['Active', 'Fully Paid', 'Cancelled'] as $status)
                                <option
                                    value="{{ $status }}"
                                    {{ old('status', $loan->status) == $status ? 'selected' : '' }}
                                >
                                    {{ $status }}
                                </option>
                            @endforeach
                        </select>

                        @error('status')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>
                </div>
            @endif

        </div>
    </div>
</div>

{{-- Form Scripts --}}
@push('custom-scripts')
    <script>
        $(document).ready(function () {

            /*
             * Available loan types by provider.
             */
            var loanTypes = {
                'SSS': [
                    'Salary Loan',
                    'Calamity Loan',
                    'Other SSS Loan'
                ],
                'Pag-IBIG': [
                    'Multi-Purpose Loan',
                    'Calamity Loan',
                    'Housing Loan',
                    'Other Pag-IBIG Loan'
                ],
                'Company': [
                    'Cash Loan',
                    'Salary Advance',
                    'Emergency Loan',
                    'Other Company Loan'
                ]
            };

            /*
             * Populate loan types when the provider changes.
             */
            function loadLoanTypes(selectedType) {
                var provider = $('#loan_provider').val();
                var $type = $('#loan_type');

                $type.empty();
                $type.append(
                    $('<option>', {
                        value: '',
                        text: 'Select Loan Type'
                    })
                );

                if (provider && loanTypes[provider]) {
                    $.each(loanTypes[provider], function (index, type) {
                        $type.append(
                            $('<option>', {
                                value: type,
                                text: type,
                                selected: type === selectedType
                            })
                        );
                    });
                }

                // Preserve a previously saved custom type when editing.
                if (
                    selectedType &&
                    provider &&
                    loanTypes[provider] &&
                    !loanTypes[provider].includes(selectedType)
                ) {
                    $type.append(
                        $('<option>', {
                            value: selectedType,
                            text: selectedType,
                            selected: true
                        })
                    );
                }
            }

            /*
             * Calculate principal + interest.
             */
            function calculateTotalLoan() {
                var principal = parseFloat($('#principal_amount').val()) || 0;
                var interest = parseFloat($('#interest_amount').val()) || 0;

                var total = principal + interest;

                $('#total_loan_amount').val(total.toFixed(2));
            }

            /*
             * Initial setup.
             */
            var initialLoanType = $('#loan_type').data('current-type');

            loadLoanTypes(initialLoanType);
            calculateTotalLoan();

            /*
             * Event listeners.
             */
            $('#loan_provider').on('change', function () {
                loadLoanTypes('');
            });

            $('#principal_amount, #interest_amount').on('input change', function () {
                calculateTotalLoan();
            });

        });
    </script>
@endpush
