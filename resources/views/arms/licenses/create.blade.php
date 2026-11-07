@extends('layout.master')

@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h3>Register Firearm License</h3>
        <a href="{{ route('arms.licenses.index') }}" class="btn btn-secondary">Back</a>
    </div>
    @if ($errors->any())
        <div class="alert alert-danger"><ul class="mb-0">@foreach ($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>
    @endif
    <form action="{{ route('arms.licenses.store') }}" method="POST" enctype="multipart/form-data">
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
                    <label>License number *</label>
                    <input name="license_number" class="form-control" maxlength="255" value="{{ old('license_number') }}" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Registration number</label>
                    <input name="registration_number" class="form-control" value="{{ old('registration_number') }}">
                </div>
                <div class="form-group col-md-6">
                    <label>Permit number</label>
                    <input name="permit_number" class="form-control" value="{{ old('permit_number') }}">
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>License type *</label>
                    <select name="license_type" class="form-control" required>
                        @foreach(['Firearm Registration','Authority to Carry','Permit to Transport','Other'] as $type)
                            <option value="{{ $type }}" {{ old('license_type', 'Firearm Registration') === $type ? 'selected' : '' }}>{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-3">
                    <label>Issue date *</label>
                    <input type="date" name="issue_date" class="form-control" value="{{ old('issue_date') }}" required>
                </div>
                <div class="form-group col-md-3">
                    <label>Expiry date *</label>
                    <input type="date" name="expiry_date" class="form-control" value="{{ old('expiry_date') }}" required>
                </div>
            </div>
            <div class="form-row">
                <div class="form-group col-md-6">
                    <label>Next renewal date</label>
                    <input type="date" name="next_renewal" class="form-control" value="{{ old('next_renewal') }}">
                </div>
                <div class="form-group col-md-6">
                    <label>Issued by</label>
                    <input name="issued_by" class="form-control" value="{{ old('issued_by') }}">
                </div>
            </div>
            <div class="form-group">
                <label>License document (PDF/JPG/PNG, max 10 MB)</label>
                <input type="file" name="document" class="form-control-file" accept=".pdf,.jpg,.jpeg,.png">
            </div>
            <div class="form-group">
                <label>Remarks</label>
                <textarea name="remarks" class="form-control" rows="3">{{ old('remarks') }}</textarea>
            </div>
            <button type="submit" class="btn btn-primary">Save License</button>
            <a href="{{ route('arms.licenses.index') }}" class="btn btn-light">Cancel</a>
        </div></div>
    </form>
</div>
@endsection
