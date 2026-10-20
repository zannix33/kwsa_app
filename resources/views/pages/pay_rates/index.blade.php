@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')
    <div class="container-fluid">

        <div class="d-flex justify-content-between align-items-center mb-4">
            <div>
                <h3 class="mb-1">Regional Pay Rates</h3>
                <p class="text-muted mb-0">
                    Manage current and historical daily wage rates by region.
                </p>
            </div>

            <button type="button"
                    class="btn btn-primary"
                    data-toggle="collapse"
                    data-target="#rateForm"
                    aria-expanded="false"
                    aria-controls="rateForm">
                <i class="fas fa-plus"></i> Add Pay Rate
            </button>
        </div>

        {{-- Success message --}}
        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}
                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        {{-- Validation errors --}}
        @if($errors->any())
            <div class="alert alert-danger">
                <strong>Please correct the following:</strong>
                <ul class="mb-0 mt-2">
                    @foreach($errors->all() as $error)
                        <li>{{ $error }}</li>
                    @endforeach
                </ul>
            </div>
        @endif

        {{-- Add Pay Rate Form --}}
        <div class="collapse mb-4 {{ $errors->any() ? 'show' : '' }}"
             id="rateForm">
            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Encode Regional Pay Rate</h5>
                </div>

                <div class="card-body">
                    <form action="{{ route('pay-rates.store') }}"
                          method="POST">
                        @csrf

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="region_id">Region</label>
                                <select name="region_id"
                                        id="region_id"
                                        class="form-control @error('region_id') is-invalid @enderror"
                                        required>
                                    <option value="">Select Region</option>

                                    @foreach($regions as $region)
                                        <option value="{{ $region->id }}"
                                            {{ old('region_id') == $region->id ? 'selected' : '' }}>
                                            {{ $region->code }} - {{ $region->name }}
                                        </option>
                                    @endforeach
                                </select>

                                @error('region_id')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="daily_rate">Daily Rate (₱)</label>
                                <input type="number"
                                       name="daily_rate"
                                       id="daily_rate"
                                       class="form-control @error('daily_rate') is-invalid @enderror"
                                       value="{{ old('daily_rate') }}"
                                       min="0.01"
                                       step="0.01"
                                       required>

                                @error('daily_rate')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>
                        </div>

                        <div class="form-row">
                            <div class="form-group col-md-6">
                                <label for="effective_from">Effective From</label>
                                <input type="date"
                                       name="effective_from"
                                       id="effective_from"
                                       class="form-control @error('effective_from') is-invalid @enderror"
                                       value="{{ old('effective_from') }}"
                                       required>

                                @error('effective_from')
                                <div class="invalid-feedback">{{ $message }}</div>
                                @enderror
                            </div>

                            <div class="form-group col-md-6">
                                <label for="wage_order_reference">
                                    Wage Order Reference
                                </label>
                                <input type="text"
                                       name="wage_order_reference"
                                       id="wage_order_reference"
                                       class="form-control"
                                       value="{{ old('wage_order_reference') }}"
                                       placeholder="e.g. Wage Order No.">
                            </div>
                        </div>

                        <div class="form-group">
                            <label for="remarks">Remarks</label>
                            <textarea name="remarks"
                                      id="remarks"
                                      class="form-control"
                                      rows="3"
                                      placeholder="Optional notes about this rate">{{ old('remarks') }}</textarea>
                        </div>

                        <button type="submit" class="btn btn-primary">
                            Save Pay Rate
                        </button>

                        <button type="button"
                                class="btn btn-secondary"
                                data-toggle="collapse"
                                data-target="#rateForm">
                            Cancel
                        </button>
                    </form>
                </div>
            </div>
        </div>

        {{-- Regional Rate History --}}
        <div class="card">
            <div class="card-header">
                <h5 class="mb-0">Rate History by Region</h5>
            </div>

            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered table-striped">
                        <thead class="thead-light">
                        <tr>
                            <th>Region</th>
                            <th>Current Rate</th>
                            <th>Effective From</th>
                            <th>Previous Rates</th>
                        </tr>
                        </thead>

                        <tbody>
                        @forelse($regions as $region)
                            @php
                                $today = now()->toDateString();

                                $currentRate = $region->payRates
                                    ->first(function ($rate) use ($today) {
                                        return $rate->effective_from->toDateString() <= $today
                                            && (
                                                is_null($rate->effective_to)
                                                || $rate->effective_to->toDateString() >= $today
                                            );
                                    });

                                $previousRates = $region->payRates
                                    ->filter(function ($rate) use ($today) {
                                        return $rate->effective_from->toDateString() <= $today
                                            && !(
                                                is_null($rate->effective_to)
                                                || $rate->effective_to->toDateString() >= $today
                                            );
                                    });
                            @endphp

                            <tr>
                                <td>
                                    <strong>{{ $region->code }}</strong>
                                    <br>
                                    <small class="text-muted">
                                        {{ $region->name }}
                                    </small>
                                </td>

                                <td>
                                    @if($currentRate)
                                        <strong>
                                            ₱{{ number_format($currentRate->daily_rate, 2) }}
                                        </strong>
                                    @else
                                        <span class="text-muted">
                                            No current rate
                                        </span>
                                    @endif
                                </td>

                                <td>
                                    @if($currentRate)
                                        {{ $currentRate->effective_from->format('M d, Y') }}
                                    @else
                                        —
                                    @endif
                                </td>

                                <td>
                                    @if($previousRates->isNotEmpty())
                                        <button type="button"
                                                class="btn btn-sm btn-outline-primary"
                                                data-toggle="collapse"
                                                data-target="#history-{{ $region->id }}">
                                            View History ({{ $previousRates->count() }})
                                        </button>
                                    @else
                                        <span class="text-muted">No previous rates</span>
                                    @endif
                                </td>
                            </tr>

                            @if($previousRates->isNotEmpty())
                                <tr class="collapse bg-light"
                                    id="history-{{ $region->id }}">
                                    <td colspan="4">
                                        <h6>
                                            {{ $region->name }} — Previous Rates
                                        </h6>

                                        <div class="table-responsive">
                                            <table class="table table-sm table-bordered mb-0">
                                                <thead>
                                                <tr>
                                                    <th>Daily Rate</th>
                                                    <th>Effective From</th>
                                                    <th>Effective To</th>
                                                    <th>Wage Order</th>
                                                    <th>Remarks</th>
                                                </tr>
                                                </thead>

                                                <tbody>
                                                @foreach($previousRates as $rate)
                                                    <tr>
                                                        <td>
                                                            ₱{{ number_format($rate->daily_rate, 2) }}
                                                        </td>
                                                        <td>
                                                            {{ $rate->effective_from->format('M d, Y') }}
                                                        </td>
                                                        <td>
                                                            {{ $rate->effective_to
                                                                ? $rate->effective_to->format('M d, Y')
                                                                : 'No end date' }}
                                                        </td>
                                                        <td>
                                                            {{ $rate->wage_order_reference ?: '—' }}
                                                        </td>
                                                        <td>
                                                            {{ $rate->remarks ?: '—' }}
                                                        </td>
                                                    </tr>
                                                @endforeach
                                                </tbody>
                                            </table>
                                        </div>
                                    </td>
                                </tr>
                            @endif
                        @empty
                            <tr>
                                <td colspan="4" class="text-center">
                                    No regions found. Please seed the regions first.
                                </td>
                            </tr>
                        @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>

    </div>
@endsection

