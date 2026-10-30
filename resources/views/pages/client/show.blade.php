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
            <div class="card-header d-flex justify-content-between align-items-center"><strong>Companies under this Client</strong><a class="btn btn-sm btn-primary" href="{{ route('clients.companies.create', $client) }}"><i class="fa fa-plus"></i> Add Company</a></div>
            <div class="card-body p-0"><table class="table table-bordered table-hover mb-0"><thead class="thead-light"><tr><th>Company</th><th>Status</th><th>Assigned Branches</th><th>Actions</th></tr></thead><tbody>
            @forelse($client->companies as $listedCompany)
                <tr><td>{{ $listedCompany->name }}</td><td>@if($listedCompany->active)<span class="badge badge-success">Active</span>@else<span class="badge badge-secondary">Inactive</span>@endif</td><td>{{ $listedCompany->branches()->count() }}</td><td><a class="btn btn-sm btn-warning" href="{{ route('clients.companies.edit', [$client,$listedCompany]) }}">Edit</a><form class="d-inline" method="POST" action="{{ route('clients.companies.destroy', [$client,$listedCompany]) }}" onsubmit="return confirm('Delete this Company?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Delete</button></form></td></tr>
            @empty<tr><td colspan="4" class="text-center text-muted">No companies have been added to this Client.</td></tr>@endforelse
            </tbody></table></div>
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
