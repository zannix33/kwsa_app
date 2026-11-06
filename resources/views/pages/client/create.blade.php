@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')

    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-8">

                <div class="card shadow">
                    <div class="card-header">
                        <h4>Create Client Group</h4>
                    </div>

                    <div class="card-body">

                        @if ($errors->any())
                            <div class="alert alert-danger">
                                <ul class="mb-0">
                                    @foreach ($errors->all() as $error)
                                        <li>{{ $error }}</li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        <form action="{{ route('clients.store') }}" method="POST">
                        @csrf

                        <!-- Company Name -->
                            <div class="form-group">
                                <label for="name">Client Name</label>
                                <input
                                    type="text"
                                    class="form-control"
                                    id="name"
                                    name="name"
                                    value="{{ old('name') }}"
                                    required>
                            </div>

                            <!-- Category -->
                            <div class="form-group">
                                <label for="category">Category</label>
                                <select class="form-control" id="category" name="category" required>
                                    <option value="">-- Select Category --</option>
                                    <option value="company" {{ old('category') == 'company' ? 'selected' : '' }}>
                                        Company
                                    </option>
                                    <option value="property" {{ old('category') == 'property' ? 'selected' : '' }}>
                                        Property
                                    </option>
                                    <option value="individual" {{ old('category') == 'individual' ? 'selected' : '' }}>
                                        Individual
                                    </option>
                                </select>
                            </div>

                            <!-- Age Limit -->
                            <div class="form-group">
                                <label for="age_limit">Age Limit</label>
                                <input
                                    type="number"
                                    class="form-control"
                                    id="age_limit"
                                    name="age_limit"
                                    value="{{ old('age_limit') }}"
                                    min="0">
                                <small class="form-text text-muted">
                                    Leave blank if there is no age restriction.
                                </small>
                            </div>

                            <!-- Active Status -->
                            <div class="form-group">
                                <label for="active">Status</label>
                                <select class="form-control" id="active" name="active" required>
                                    <option value="1" {{ old('active') == '1' ? 'selected' : '' }}>
                                        Active
                                    </option>
                                    <option value="0" {{ old('active') == '0' ? 'selected' : '' }}>
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
                            <option value="1" {{ old('apply_overtime_rate', '1') == '1' ? 'selected' : '' }}>Enabled</option>
                            <option value="0" {{ old('apply_overtime_rate', '1') == '0' ? 'selected' : '' }}>Disabled — base hourly rate</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="apply_night_diff_rate">Apply Night Differential Premium</label>
                        <select class="form-control" id="apply_night_diff_rate" name="apply_night_diff_rate" required>
                            <option value="1" {{ old('apply_night_diff_rate', '1') == '1' ? 'selected' : '' }}>Enabled</option>
                            <option value="0" {{ old('apply_night_diff_rate', '1') == '0' ? 'selected' : '' }}>Disabled — no additional ND premium</option>
                        </select>
                    </div>
                    <input type="hidden" name="premium_combination_method" value="billing_formula">
                            <div class="text-right">
                                <a href="{{ route('clients.index') }}" class="btn btn-secondary">
                                    Cancel
                                </a>

                                <button type="submit" class="btn btn-primary">
                                    Save Client
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection


