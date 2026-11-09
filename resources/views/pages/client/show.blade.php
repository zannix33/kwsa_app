@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')

    <div class="container-fluid">

        {{-- Company Header --}}
        <div class="row mb-3">

            <div class="col-md-6">
                <h3 class="mb-0">{{ $client->name }}</h3>
                <small class="text-muted">
                    {{ ucfirst($client->category) }}
                </small>
            </div>

            <div class="col-md-6 text-right">

                <a href="{{ route('clients.edit',$client->id) }}"
                   class="btn btn-warning">
                    <i class="fa fa-edit"></i>
                    Edit Client
                </a>

                <a href="{{ route('clients.index') }}"
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
                                <td>{{ $client->name }}</td>
                            </tr>

                            <tr>
                                <th>Category</th>
                                <td>{{ ucfirst($client->category) }}</td>
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



        <div class="card mb-4">
                <div class="card-header"><strong>Branch Operating Calendar</strong></div>
                <div class="card-body">
                    <p class="text-muted mb-3">Manage this client's normal weekly operating days and special closure dates. These settings do not change billing, payroll, or attendance.</p>
                    <a href="{{ route('clients.branch-calendar.closures', $client) }}" class="btn btn-outline-secondary mb-2"><i class="fa fa-calendar-times"></i> Closures &amp; Holidays</a>
                </div>
        </div>

        {{-- Areas appear on the left, Companies on the right; Branches use the full row below. --}}
        <div class="row align-items-stretch">
            <div class="col-lg-6 mb-3">
                @include('pages.client.partials.cards._areas')
            </div>

            <div class="col-lg-6 mb-3">
                <div class="h-100">
        <div class="card mb-4">
            <div class="card-header d-flex justify-content-between align-items-center"><strong>Companies under this Client</strong><a class="btn btn-sm btn-primary" href="{{ route('clients.companies.create', $client) }}"><i class="fa fa-plus"></i> Add Company</a></div>
            <div class="card-body p-0"><table class="table table-bordered table-hover mb-0"><thead class="thead-light"><tr><th>Company</th><th>Status</th><th>Assigned Branches</th><th>Actions</th></tr></thead><tbody>
            @forelse($client->companies as $listedCompany)
                @php
                    $branchCountsByArea = $listedCompany->branches
                        ->groupBy(function ($branch) {
                            return optional($branch->area)->name ?: 'Area not assigned';
                        })
                        ->map(function ($branches) { return $branches->count(); });
                    $companyBranchCount = $listedCompany->branches->count();
                @endphp
                <tr>
                    <td>{{ $listedCompany->name }}</td>
                    <td>@if($listedCompany->active)<span class="badge badge-success">Active</span>@else<span class="badge badge-secondary">Inactive</span>@endif</td>
                    <td>
                        <span class="font-weight-bold">{{ $companyBranchCount }}</span>
                        <button type="button" class="btn btn-sm btn-link p-0 ml-2" data-toggle="modal" data-target="#companyAreaBranches{{ $listedCompany->id }}" {{ $companyBranchCount ? '' : 'disabled' }}>
                            View by Area
                        </button>
                    </td>
                    <td><a class="btn btn-sm btn-warning" href="{{ route('clients.companies.edit', [$client,$listedCompany]) }}">Edit</a><form class="d-inline" method="POST" action="{{ route('clients.companies.destroy', [$client,$listedCompany]) }}" onsubmit="return confirm('Delete this Company?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Delete</button></form></td>
                </tr>
            @empty<tr><td colspan="4" class="text-center text-muted">No companies have been added to this Client.</td></tr>@endforelse
            </tbody></table></div>
        </div>

        @foreach($client->companies as $listedCompany)
            @php
                $branchCountsByArea = $listedCompany->branches
                    ->groupBy(function ($branch) {
                        return optional($branch->area)->name ?: 'Area not assigned';
                    })
                    ->map(function ($branches) { return $branches->count(); });
            @endphp
            <div class="modal fade" id="companyAreaBranches{{ $listedCompany->id }}" tabindex="-1" role="dialog" aria-labelledby="companyAreaBranchesLabel{{ $listedCompany->id }}" aria-hidden="true">
                <div class="modal-dialog modal-dialog-centered" role="document">
                    <div class="modal-content">
                        <div class="modal-header">
                            <h5 class="modal-title" id="companyAreaBranchesLabel{{ $listedCompany->id }}">{{ $listedCompany->name }} — Branches by Area</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                        </div>
                        <div class="modal-body">
                            @if($branchCountsByArea->isEmpty())
                                <p class="text-muted mb-0">No branches are currently assigned to this company.</p>
                            @else
                                <div class="table-responsive">
                                    <table class="table table-sm table-bordered mb-0">
                                        <thead class="thead-light"><tr><th>Area</th><th class="text-center" style="width: 110px;">Branches</th></tr></thead>
                                        <tbody>
                                            @foreach($branchCountsByArea as $areaName => $branchCount)
                                                <tr><td>{{ $areaName }}</td><td class="text-center font-weight-bold">{{ $branchCount }}</td></tr>
                                            @endforeach
                                        </tbody>
                                        <tfoot><tr><th>Total</th><th class="text-center">{{ $listedCompany->branches->count() }}</th></tr></tfoot>
                                    </table>
                                </div>
                            @endif
                        </div>
                        <div class="modal-footer"><button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button></div>
                    </div>
                </div>
            </div>
        @endforeach

                </div>
            </div>

            <div class="col-12 mb-3">
                @include('pages.client.partials.cards._branches')
            </div>
        </div>

    </div>

    @include('pages.client.partials.modals._guards_modal')

    @include('pages.client.partials.modals._area_modal')

    @include('pages.client.partials.modals._branch_modal')

    @include('pages.client.partials.modals._assign_area_guard_modal')

    @include('pages.client.partials.modals._assign_branch_guard_modal')

    {{-- Shared, authoritative toolbar layout. Loaded after both card partials. --}}
    <style id="client-area-branch-search-toolbar">
        .client-search-controls {
            display: flex !important;
            flex-direction: row !important;
            flex-wrap: nowrap !important;
            align-items: center !important;
            justify-content: flex-start !important;
            gap: 8px !important;
            width: 100% !important;
            min-width: 0 !important;
        }
        .client-search-controls > .client-search-input-wrap {
            display: block !important;
            position: relative !important;
            flex: 1 1 0% !important;
            width: auto !important;
            min-width: 0 !important;
            max-width: none !important;
        }
        .client-search-controls > .client-search-input-wrap > .form-control {
            display: block !important;
            box-sizing: border-box !important;
            width: 100% !important;
            max-width: 100% !important;
            min-width: 0 !important;
            height: 36px !important;
        }
        .client-search-controls > .client-search-clear {
            position: static !important;
            display: inline-flex !important;
            flex: 0 0 64px !important;
            width: 64px !important;
            min-width: 64px !important;
            max-width: 64px !important;
            height: 36px !important;
            margin: 0 !important;
            align-items: center !important;
            justify-content: center !important;
            white-space: nowrap !important;
        }
    </style>
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
