@extends('layout.master')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3"><div><h3 class="mb-0">{{ $closure ? 'Edit Closure' : 'Add Closure' }}</h3><small class="text-muted">{{ $client->name }}</small></div><a href="{{ route('clients.branch-calendar.closures', $client) }}" class="btn btn-secondary">Back to Closures</a></div>
    @if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ $closure ? route('clients.branch-calendar.closures.update', [$client, $closure->id]) : route('clients.branch-calendar.closures.store', $client) }}">
        @csrf @if($closure) @method('PUT') @endif
        <div class="card mb-3"><div class="card-header"><strong>Closure Details</strong></div><div class="card-body">
            <div class="form-row">
                <div class="form-group col-md-6"><label>Closure name *</label><input name="name" class="form-control" required maxlength="255" value="{{ old('name', $closure->name ?? '') }}" placeholder="e.g. Christmas Day"></div>
                <div class="form-group col-md-6"><label>Closure type *</label><select name="closure_type" class="form-control" required>
                    @foreach(['Regular Holiday','Special Non-Working Holiday','Local Holiday','Company Closure','Branch Closure','Maintenance','Emergency Closure','Other'] as $type)<option value="{{ $type }}" {{ old('closure_type', $closure->closure_type ?? '') === $type ? 'selected' : '' }}>{{ $type }}</option>@endforeach
                </select></div>
                <div class="form-group col-md-6"><label>Start date *</label><input type="date" name="start_date" class="form-control" required value="{{ old('start_date', $closure->start_date ?? '') }}"></div>
                <div class="form-group col-md-6"><label>End date *</label><input type="date" name="end_date" class="form-control" required value="{{ old('end_date', $closure->end_date ?? '') }}"></div>
            </div>
            <div class="form-group mb-0"><label>Description / Remarks</label><textarea name="description" rows="3" class="form-control" maxlength="2000">{{ old('description', $closure->description ?? '') }}</textarea></div>
        </div></div>
        <div class="card mb-3"><div class="card-header"><strong>Branch Assignment</strong></div><div class="card-body">
            <div class="form-group"><label>Apply closure to *</label><select class="form-control" name="assignment_mode" id="assignmentMode" required>
                <option value="all" {{ old('assignment_mode', $closure->assignment_mode ?? 'all') === 'all' ? 'selected' : '' }}>All branches under this client</option>
                <option value="areas" {{ old('assignment_mode', $closure->assignment_mode ?? '') === 'areas' ? 'selected' : '' }}>All branches in selected Areas</option>
                <option value="branches" {{ old('assignment_mode', $closure->assignment_mode ?? '') === 'branches' ? 'selected' : '' }}>Selected branches only</option>
                <option value="all_except" {{ old('assignment_mode', $closure->assignment_mode ?? '') === 'all_except' ? 'selected' : '' }}>All branches except selected branches</option>
            </select><small class="form-text text-muted">All branches mode also applies to branches added to this client in the future. Area assignment applies to branches currently belonging to the selected Areas and follows those Areas as branches are added.</small></div>
            <div id="areaChoices" class="form-group"><label>Select Areas</label>
                @forelse($areas as $area)<div class="custom-control custom-checkbox mb-1"><input class="custom-control-input" type="checkbox" id="area{{ $area->id }}" name="area_ids[]" value="{{ $area->id }}" {{ in_array($area->id, old('area_ids', $selectedAreas)) ? 'checked' : '' }}><label class="custom-control-label" for="area{{ $area->id }}">{{ $area->name }} <small class="text-muted">({{ $area->branches->count() }} branches)</small></label></div>@empty<p class="text-muted">No Areas available.</p>@endforelse
            </div>
            <div id="branchChoices" class="form-group"><label id="branchChoicesLabel">Select branches</label>
                @forelse($areas as $area)<div class="border rounded p-2 mb-2"><strong>{{ $area->name }}</strong>
                    @forelse($area->branches as $branch)<div class="custom-control custom-checkbox ml-2"><input class="custom-control-input" type="checkbox" id="branch{{ $branch->id }}" name="branch_ids[]" value="{{ $branch->id }}" {{ in_array($branch->id, old('branch_ids', $selectedBranches)) ? 'checked' : '' }}><label class="custom-control-label" for="branch{{ $branch->id }}">{{ $branch->name }}</label></div>@empty<small class="text-muted d-block ml-2">No branches</small>@endforelse
                </div>@empty<p class="text-muted">No branches available.</p>@endforelse
            </div>
        </div></div>
        <button type="submit" class="btn btn-primary"><i class="fa fa-save"></i> Save Closure</button>
        <a href="{{ route('clients.branch-calendar.closures', $client) }}" class="btn btn-light">Cancel</a>
    </form>
</div>
@endsection
@push('custom-scripts')
<script>
(function () {
    var mode = document.getElementById('assignmentMode');
    var areas = document.getElementById('areaChoices');
    var branches = document.getElementById('branchChoices');
    var label = document.getElementById('branchChoicesLabel');
    function refresh() {
        areas.style.display = mode.value === 'areas' ? '' : 'none';
        branches.style.display = (mode.value === 'branches' || mode.value === 'all_except') ? '' : 'none';
        label.textContent = mode.value === 'all_except' ? 'Branches to exclude' : 'Select branches';
    }
    mode.addEventListener('change', refresh);
    refresh();
})();
</script>
@endpush
