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
                        <h4>Create Client</h4>
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

                        <form action="{{ route('clients.companies.store') }}" method="POST">
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

                            <div class="text-right">
                                <a href="{{ route('clients.companies.index') }}" class="btn btn-secondary">
                                    Cancel
                                </a>

                                <button type="submit" class="btn btn-primary">
                                    Save Company
                                </button>
                            </div>

                        </form>

                    </div>
                </div>

            </div>
        </div>
    </div>
@endsection


