@extends('layout.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Issue Firearm</h3>
        <a href="{{ route('arms.assignments.index') }}" class="btn btn-secondary">Back</a>
    </div>
    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <form action="{{ route('arms.assignments.store') }}" method="POST">
        @csrf
        <div class="card"><div class="card-body">
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Firearm *</label>
                    <select name="arm_id" class="form-control" required>
                        <option value="">Select firearm</option>
                        @foreach($arms as $arm)
                            <option value="{{ $arm->id }}" {{ old('arm_id') == $arm->id ? 'selected' : '' }}>{{ $arm->property_no }} — {{ $arm->manufacturer }} {{ $arm->model }} ({{ $arm->serial_no }})</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label>Employee *</label>
                    <select name="user_id" class="form-control" required>
                        <option value="">Select employee</option>
                        @foreach($users as $user)
                            <option value="{{ $user->id }}" {{ old('user_id') == $user->id ? 'selected' : '' }}>{{ $user->name }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-4">
                    <label>Branch</label>
                    <select name="branch_id" class="form-control">
                        <option value="">No branch selected</option>
                        @foreach($branches as $branch)
                            <option value="{{ $branch->id }}" {{ old('branch_id') == $branch->id ? 'selected' : '' }}>{{ $branch->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-4">
                    <label>Issue date *</label>
                    <input type="date" name="issued_at" class="form-control" value="{{ old('issued_at', now()->toDateString()) }}" required>
                </div>
                <div class="form-group col-md-4">
                    <label>Reference number</label>
                    <input type="text" name="reference_no" class="form-control" value="{{ old('reference_no') }}" maxlength="100">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Condition before issue *</label>
                    <select name="condition_before" class="form-control" required>
                        @foreach(['Excellent','Good','Fair','Needs Repair'] as $condition)
                            <option value="{{ $condition }}" {{ old('condition_before', 'Good') === $condition ? 'selected' : '' }}>{{ $condition }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-6">
                    <label>Ammunition issued (rounds)</label>
                    <input type="number" min="0" name="ammo_issued" class="form-control" value="{{ old('ammo_issued', 0) }}">
                </div>
            </div>
            <div class="form-group">
                <label>Remarks</label>
                <textarea name="remarks" class="form-control" rows="3">{{ old('remarks') }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save Assignment</button>
            <a href="{{ route('arms.assignments.index') }}" class="btn btn-light">Cancel</a>
        </div></div>
    </form>
</div>
@endsection
