@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')

    <div class="container-fluid">

        <div class="row mb-3">
            <div class="col-md-6">
                <h3>Companies</h3>
            </div>

            <div class="col-md-6 text-right">
                <a href="{{ route('clients.companies.create') }}" class="btn btn-primary">
                    <i class="fa fa-plus"></i> Create Company
                </a>
            </div>
        </div>

        @if(session('success'))
            <div class="alert alert-success alert-dismissible fade show">
                {{ session('success') }}

                <button type="button" class="close" data-dismiss="alert">
                    <span>&times;</span>
                </button>
            </div>
        @endif

        <div class="card shadow">
            <div class="card-header">
                Company List
            </div>

            <div class="card-body p-0">
                <table class="table table-bordered table-hover mb-0">
                    <thead class="thead-light">
                    <tr>
                        <th width="80">ID</th>
                        <th>Name</th>
                        <th>Category</th>
                        <th>Age Limit</th>
                        <th>Status</th>
                        <th width="180">Actions</th>
                    </tr>
                    </thead>
                    <tbody>
                    @forelse($companies as $company)
                        <tr>
                            <td>{{ $company->id }}</td>
                            <td>{{ $company->name }}</td>
                            <td>{{ $company->category }}</td>
                            <td>{{ $company->age_limit ?? 'N/A' }}</td>
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
                            <td>
                                <a href="{{ route('clients.companies.show', $company->id) }}"
                                   class="btn btn-sm btn-info">
                                    View
                                </a>

                                <a href="{{ route('clients.companies.edit', $company->id) }}"
                                   class="btn btn-sm btn-warning">
                                    Edit
                                </a>

                                <form action="{{ route('clients.companies.destroy', $company->id) }}"
                                      method="POST"
                                      style="display:inline-block;"
                                      onsubmit="return confirm('Delete this company?')">
                                    @csrf
                                    @method('DELETE')

                                    <button type="submit"
                                            class="btn btn-sm btn-danger">
                                        Delete
                                    </button>
                                </form>
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="6" class="text-center">
                                No companies found.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            @if(method_exists($companies, 'links'))
                <div class="card-footer">
                    {{ $companies->links() }}
                </div>
            @endif

        </div>

    </div>

@endsection

@push('plugin-scripts')
    {!! Html::script('/assets/plugins/chartjs/chart.min.js') !!}
    {!! Html::script('/assets/plugins/jquery-sparkline/jquery.sparkline.min.js') !!}
@endpush

@push('custom-scripts')
    {!! Html::script('/assets/js/dashboard.js') !!}
@endpush
