@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')

    <div class="container-fluid">

        {{-- Company Header --}}
        <div class="row mb-3">

            <div class="col-md-6">
                <h3 class="mb-0">{{ $company->name }}</h3>
                <small class="text-muted">
                    {{ $company->category }}
                </small>
            </div>

            <div class="col-md-6 text-right">

                <a href="{{ route('clients.companies.edit',$company->id) }}"
                   class="btn btn-warning">
                    <i class="fa fa-edit"></i>
                    Edit Company
                </a>

                <a href="{{ route('clients.companies.index') }}"
                   class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i>
                    Back
                </a>

            </div>

        </div>

        {{-- Company Information --}}
        <div class="card mb-4">

            <div class="card-header">
                Company Information
            </div>

            <div class="card-body">

                <div class="row">

                    <div class="col-md-4">

                        <table class="table table-bordered table-sm">

                            <tr>
                                <th width="150">Name</th>
                                <td>{{ $company->name }}</td>
                            </tr>

                            <tr>
                                <th>Category</th>
                                <td>{{ $company->category }}</td>
                            </tr>

                            <tr>
                                <th>Age Limit</th>
                                <td>{{ $company->age_limit ?? '-' }}</td>
                            </tr>

                            <tr>
                                <th>Status</th>
                                <td>

                                    @if($company->active)
                                        <span class="badge badge-success">
                                        Active
                                    </span>
                                    @else
                                        <span class="badge badge-danger">
                                        Inactive
                                    </span>
                                    @endif

                                </td>
                            </tr>

                        </table>

                    </div>

                </div>

            </div>

        </div>

        {{-- Area / Branch / Guards --}}
        <div class="row">

            <div class="col-lg-3">
                @include('pages.client.partials.cards._areas')
            </div>

            <div class="col-lg-4">
                @include('pages.client.partials.cards._branches')
            </div>

            <div class="col-lg-5">
                @include('pages.client.partials.cards._guards')
            </div>

        </div>

    </div>

    @include('pages.client.partials.modals._area_modal')

    @include('pages.client.partials.modals._branch_modal')

    @include('pages.client.partials.modals._assign_area_guard_modal')

    @include('pages.client.partials.modals._assign_branch_guard_modal')
@endsection

@push('custom-scripts')
    @include('pages.client.partials.scripts._variables')

    @include('pages.client.partials.scripts._helpers')

    @include('pages.client.partials.scripts._area_click')

    @include('pages.client.partials.scripts._branch_click')

    @include('pages.client.partials.scripts._load_branches')

    @include('pages.client.partials.scripts._load_area_guards')

    @include('pages.client.partials.scripts._load_branch_guards')

    @include('pages.client.partials.scripts._render_guards')

    @include('pages.client.partials.scripts._load_areas')

    @include('pages.client.partials.scripts._create_area')

    @include('pages.client.partials.scripts._create_branch')

    @include('pages.client.partials.scripts._assign_area_guard')

    @include('pages.client.partials.scripts._assign_branch_guard')

    @include('pages.client.partials.scripts._remove_area_guard')

    @include('pages.client.partials.scripts._remove_branch_guard')
@endpush
