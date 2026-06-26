@csrf

<div class="row">

    <div class="col-md-4">
        <div class="form-group">
            <label>Employee</label>

            <select name="user_id" class="form-control" required>
                <option value="">Select Employee</option>

                @foreach($employees as $employee)
                    <option value="{{ $employee->id }}"
                        {{ old('user_id', $dtr->user_id ?? '') == $employee->id ? 'selected' : '' }}>
                        {{ $employee->full_name }}
                    </option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>Work Date</label>

            <input type="date"
                   name="work_date"
                   class="form-control"
                   value="{{ old('work_date', isset($dtr) ? $dtr->work_date : '') }}"
                   required>
        </div>
    </div>

    <div class="col-md-4">
        <div class="form-group">
            <label>Break Minutes</label>

            <input type="number"
                   name="break_minutes"
                   class="form-control"
                   value="{{ old('break_minutes', $dtr->break_minutes ?? 60) }}">
        </div>
    </div>

</div>

<div class="row">

    <div class="col-md-6">
        <div class="form-group">
            <label>Time In</label>

            <input type="datetime-local"
                   name="time_in"
                   class="form-control"
                   value="{{ old('time_in', isset($dtr) && $dtr->time_in ? \Carbon\Carbon::parse($dtr->time_in)->format('Y-m-d\TH:i') : '') }}"
                   required>
        </div>
    </div>

    <div class="col-md-6">
        <div class="form-group">
            <label>Time Out</label>

            <input type="datetime-local"
                   name="time_out"
                   class="form-control"
                   value="{{ old('time_out', isset($dtr) && $dtr->time_out ? \Carbon\Carbon::parse($dtr->time_out)->format('Y-m-d\TH:i') : '') }}"
                   required>
        </div>
    </div>

</div>

<div class="row">

    <div class="col-md-3">
        <div class="form-group">
            <label>Rest Day</label>

            <select name="is_rest_day" class="form-control">
                <option value="0"
                    {{ old('is_rest_day', $dtr->is_rest_day ?? 0) == 0 ? 'selected' : '' }}>
                    No
                </option>

                <option value="1"
                    {{ old('is_rest_day', $dtr->is_rest_day ?? 0) == 1 ? 'selected' : '' }}>
                    Yes
                </option>
            </select>
        </div>
    </div>

    <div class="col-md-3">
        <div class="form-group">
            <label>Holiday</label>

            <select name="is_holiday" class="form-control">
                <option value="0"
                    {{ old('is_holiday', $dtr->is_holiday ?? 0) == 0 ? 'selected' : '' }}>
                    No
                </option>

                <option value="1"
                    {{ old('is_holiday', $dtr->is_holiday ?? 0) == 1 ? 'selected' : '' }}>
                    Yes
                </option>
            </select>
        </div>
    </div>

</div>

<div class="form-group">
    <label>Remarks</label>

    <textarea name="remarks"
              rows="3"
              class="form-control">{{ old('remarks', $dtr->remarks ?? '') }}</textarea>
</div>
