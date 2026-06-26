@csrf

<div class="form-group">
    <label>Name</label>
    <input type="text" name="name"
           class="form-control"
           value="{{ old('name', $rate->name ?? '') }}"
           required>
</div>

<div class="form-group">
    <label>Rate</label>
    <input type="number" step="0.01" name="rate"
           class="form-control"
           value="{{ old('rate', $rate->rate ?? '') }}"
           required>
</div>

<div class="form-group form-check">
    <input type="checkbox"
           name="active"
           value="1"
           class="form-check-input"
           {{ old('active', $rate->active ?? 1) ? 'checked' : '' }}>
    <label class="form-check-label">Active</label>
</div>
