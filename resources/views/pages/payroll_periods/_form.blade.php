<div class="form-group">
    <label>Date From</label>

    <input
        type="date"
        name="date_from"
        class="form-control"
        value="{{ old(
            'date_from',
            isset($payrollPeriod)
            ? $payrollPeriod->date_from->format('Y-m-d')
            : ''
        ) }}"
        required>
</div>

<div class="form-group">
    <label>Date To</label>

    <input
        type="date"
        name="date_to"
        class="form-control"
        value="{{ old(
            'date_to',
            isset($payrollPeriod)
            ? $payrollPeriod->date_to->format('Y-m-d')
            : ''
        ) }}"
        required>
</div>

@if(isset($payrollPeriod))

    <div class="form-group">
        <label>Status</label>

        <select
            name="status"
            class="form-control">

            <option value="Open"
                {{ $payrollPeriod->status == 'Open' ? 'selected' : '' }}>
                Open
            </option>

            <option value="Closed"
                {{ $payrollPeriod->status == 'Closed' ? 'selected' : '' }}>
                Closed
            </option>

        </select>

    </div>

@endif
