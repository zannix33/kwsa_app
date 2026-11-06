@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')
    <div class="container-fluid">

        <div class="row mb-3">
            <div class="col-md-6">
                <h3>Edit Client Group</h3>
            </div>

            <div class="col-md-6 text-right">
                <a href="{{ route('clients.index') }}" class="btn btn-secondary">
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
                Edit Client Group Information
            </div>

            <div class="card-body">

                <form action="{{ route('clients.update', $client->id) }}" method="POST">
                    @csrf
                    @method('PUT')

                    <div class="form-group">
                        <label for="name">Client Name</label>
                        <input
                            type="text"
                            class="form-control"
                            id="name"
                            name="name"
                            value="{{ old('name', $client->name) }}"
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
                                {{ old('category', $client->category) == 'company' ? 'selected' : '' }}>
                                Company
                            </option>

                            <option value="property"
                                {{ old('category', $client->category) == 'property' ? 'selected' : '' }}>
                                Property
                            </option>

                            <option value="individual"
                                {{ old('category', $client->category) == 'individual' ? 'selected' : '' }}>
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
                            value="{{ old('age_limit', $client->age_limit) }}">
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
                                {{ old('active', $client->active) == 1 ? 'selected' : '' }}>
                                Active
                            </option>

                            <option value="0"
                                {{ old('active', $client->active) == 0 ? 'selected' : '' }}>
                                Inactive
                            </option>

                        </select>
                    </div>


                    <hr>
                    <h5>Overtime & Night Differential Premiums</h5>
                    <p class="text-muted">Disabling a premium does not remove recorded DTR hours. Hours remain payable at the base rate.</p>
                    <div class="form-group">
                        <label for="apply_overtime_rate">Apply Overtime Premium</label>
                        <select class="form-control" id="apply_overtime_rate" name="apply_overtime_rate" required>
                            <option value="1" {{ old('apply_overtime_rate', $client->apply_overtime_rate ?? true) ? 'selected' : '' }}>Enabled</option>
                            <option value="0" {{ !old('apply_overtime_rate', $client->apply_overtime_rate ?? true) ? 'selected' : '' }}>Disabled — base hourly rate</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="apply_night_diff_rate">Apply Night Differential Premium</label>
                        <select class="form-control" id="apply_night_diff_rate" name="apply_night_diff_rate" required>
                            <option value="1" {{ old('apply_night_diff_rate', $client->apply_night_diff_rate ?? true) ? 'selected' : '' }}>Enabled</option>
                            <option value="0" {{ !old('apply_night_diff_rate', $client->apply_night_diff_rate ?? true) ? 'selected' : '' }}>Disabled — no additional ND premium</option>
                        </select>
                    </div>
                    <input type="hidden" name="premium_combination_method" value="billing_formula">
                    <div class="text-right">
                        <button type="submit" class="btn btn-primary">
                            <i class="fa fa-save"></i> Update Client
                        </button>
                    </div>

                </form>

            </div>
        </div>

    </div>


@endsection
