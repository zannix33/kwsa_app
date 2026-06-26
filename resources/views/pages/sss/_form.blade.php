<div class="form-group">
    <label>Salary From</label>
    <input
        type="number"
        step="0.01"
        name="from_salary"
        class="form-control"
        value="{{ old('from_salary', $ss->from_salary ?? '') }}">
</div>

<div class="form-group">
    <label>Salary To</label>
    <input
        type="number"
        step="0.01"
        name="to_salary"
        class="form-control"
        value="{{ old('to_salary', $ss->to_salary ?? '') }}">
</div>

<div class="form-group">
    <label>Employee Share</label>
    <input
        type="number"
        step="0.01"
        name="employee_share"
        class="form-control"
        value="{{ old('employee_share', $ss->employee_share ?? '') }}">
</div>

<div class="form-group">
    <label>Employer Share</label>
    <input
        type="number"
        step="0.01"
        name="employer_share"
        class="form-control"
        value="{{ old('employer_share', $ss->employer_share ?? '') }}">
</div>

<div class="form-group">
    <label>EC</label>
    <input
        type="number"
        step="0.01"
        name="ec"
        class="form-control"
        value="{{ old('ec', $ss->ec ?? 0) }}">
</div>

<div class="form-group">
    <label>Status</label>
    <select
        name="active"
        class="form-control">

        <option value="1">Active</option>
        <option value="0">Inactive</option>

    </select>
</div>
