@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')
    <div class="container-fluid">

        <div class="row mb-3">
            <div class="col-md-6">
                <h3>Edit Company</h3>
            </div>

            <div class="col-md-6 text-right">
                <a href="{{ route('clients.companies.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Back
                </a>
            </div>
        </div>

        @if ($errors->any())
            <div class="alert alert-danger">
                <ul class="mb-0">
                    @foreach ($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        <div class="card shadow">
            <div class="card-header">
                Edit Company Information
            </div>

            <div class="card-body">

                <form action="{{ route('clients.companies.update', $company->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name">Company Name</label>
                        <input
                            type="text"
                            class="form-control"
                            id="name"
                            name="name"
                            value="{{ old('name', $company->name) }}"
                            required>
                    </div>

                    <div class="form-group">
                        <label for="category">Category</label>
                        <select
                            class="form-control"
                            id="category"
                            name="category"
                            required>
                            <option value="">-- Select Category --</option>

                            <option value="company"
                                {{ old('category', $company->category) == 'company' ? 'selected' : '' }}>
                                Company
                            </option>

                            <option value="property"
                                {{ old('category', $company->category) == 'property' ? 'selected' : '' }}>
                                Property
                            </option>

                            <option value="individual"
                                {{ old('category', $company->category) == 'individual' ? 'selected' : '' }}>
                                Individual
                            </option>
                        </select>
                    </div>

                    <div class="form-group" id="age-limit-group">
                        <label for="age_limit">Age Limit</label>
                        <input
                            type="number"
                            class="form-control"
                            id="age_limit"
                            name="age_limit"
                            min="0"
                            value="{{ old('age_limit', $company->age_limit) }}">
                        <small class="text-muted">
                            Leave blank if there is no age restriction.
                        </small>
                    </div>

                    <div class="form-group">
                        <label for="active">Status</label>
                        <select
                            class="form-control"
                            id="active"
                            name="active"
                            required>

                            <option value="1"
                                {{ old('active', $company->active) == 1 ? 'selected' : '' }}>
                                Active
                            </option>

                            <option value="0"
                                {{ old('active', $company->active) == 0 ? 'selected' : '' }}>
                                Inactive
                            </option>

                        </select>
                    </div>

                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Update Company
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>


@endsection
