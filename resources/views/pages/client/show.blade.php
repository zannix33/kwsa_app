@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')

    <div class="container-fluid">

        <div class="row mb-3">
            <div class="col-md-6">
                <h3>Company Details</h3>
            </div>

            <div class="col-md-6 text-right">
                <a href="{{ route('clients.companies.index') }}" class="btn btn-secondary">
                    <i class="fa fa-arrow-left"></i> Back
                </a>

                <a href="{{ route('clients.companies.edit', $company->id) }}" class="btn btn-warning">
                    <i class="fa fa-edit"></i> Edit
                </a>
            </div>
        </div>

        <div class="card shadow">
            <div class="card-header">
                <strong>{{ $company->name }}</strong>
            </div>

            <div class="card-body">

                <table class="table table-bordered">
                    <tbody>
                    <tr>
                        <th width="200">ID</th>
                        <td>{{ $company->id }}</td>
                    </tr>

                    <tr>
                        <th>Company Name</th>
                        <td>{{ $company->name }}</td>
                    </tr>

                    <tr>
                        <th>Category</th>
                        <td>
                            <span class="badge badge-info">
                                {{ $company->category }}
                            </span>
                        </td>
                    </tr>

                    <tr>
                        <th>Age Limit</th>
                        <td>
                            {{ $company->age_limit ?? 'No Restriction' }}
                        </td>
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

                    <tr>
                        <th>Created At</th>
                        <td>
                            {{ $company->created_at->format('F d, Y h:i A') }}
                        </td>
                    </tr>

                    </tbody>
                </table>

            </div>

            <div class="card-footer text-right">
                <form action="{{ route('clients.companies.destroy', $company->id) }}"
                      method="POST"
                      style="display:inline-block;"
                      onsubmit="return confirm('Are you sure you want to delete this company?')">
                    @csrf
                    @method('DELETE')

                    <button type="submit" class="btn btn-danger">
                        <i class="fa fa-trash"></i> Delete
                    </button>
                </form>
            </div>
        </div>

        <br>

        <div class="row">

            <!-- LEFT: AREAS -->
            <div class="col-md-3">

                <div class="card">

                    <div class="card-header d-flex justify-content-between">

                        <div>
                            <strong>Areas</strong>
                        </div>

                        <div>

                            @if($company->name != 'KSA')
                                <button class="btn btn-success btn-sm"
                                        id="btnAssignAreaGuard">
                                    Assign Guard
                                </button>
                            @endif

                            <button class="btn btn-primary btn-sm"
                                    data-toggle="modal"
                                    data-target="#areaModal">
                                Create
                            </button>

                        </div>

                    </div>

                    <ul class="list-group list-group-flush" id="areas-list">
                        @forelse($company->areas as $area)
                            <li class="list-group-item area-item"
                                data-id="{{ $area->id }}"
                                style="cursor:pointer;">
                                {{ $area->name }}
                            </li>
                        @empty
                            <li class="list-group-item text-muted">
                                No areas found
                            </li>
                        @endforelse
                    </ul>

                </div>
            </div>

            <!-- RIGHT: BRANCHES -->
            <div class="col-md-4">

                <div class="card">
                    <div class="card-header d-flex justify-content-between align-items-center">
                        <span>Branches</span>

                        <button class="btn btn-sm btn-primary"
                                id="btnCreateBranch">
                            + Create
                        </button>
                    </div>

                    <div class="card-body" id="branches-container">
                        <p class="text-muted">Click an area to load branches</p>
                    </div>
                </div>

            </div>

            <!-- Guards -->
            <div class="col-md-5">

                <div class="card">
                    <div class="card-header d-flex justify-content-between">
                        <span>Guards</span>

                        <button class="btn btn-sm btn-primary"
                                id="btnAssignGuard">
                            Assign {{ $company->name == 'KSA' ? 'Staff' : 'Guard' }}
                        </button>
                    </div>

                    <div class="card-body" id="guards-container">
                        <p class="text-muted">
                            Select a branch to view guards
                        </p>
                    </div>
                </div>

            </div>

        </div>

    </div>

    <div class="modal fade" id="areaModal" tabindex="-1" role="dialog">
        <div class="modal-dialog" role="document">

            <form id="areaForm">
                @csrf
                <input type="hidden" name="client_id" value="{{ $company->id }}">

                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Create Area</h5>
                        <button type="button" class="close" data-dismiss="modal">
                            <span>&times;</span>
                        </button>
                    </div>

                    <div class="modal-body">

                        <div class="form-group">
                            <label>Area Name</label>
                            <input type="text" name="name" class="form-control" required>
                        </div>

                        <div class="form-group">
                            <label>Description</label>
                            <textarea name="description" class="form-control"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Rate Type</label>

                            <select name="rate"
                                    class="form-control @error('rate') is-invalid @enderror">

                                <option value="">Select Rate Type</option>

                                <option value="ncr"
                                    {{ old('rate', $area->rate ?? '') == 'NCR' ? 'selected' : '' }}>
                                    NCR
                                </option>

                                <option value="provincial"
                                    {{ old('rate', $area->rate ?? '') == 'Provincial' ? 'selected' : '' }}>
                                    Provincial
                                </option>

                            </select>

                            @error('rate')
                            <div class="invalid-feedback">{{ $message }}</div>
                            @enderror
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button type="submit" class="btn btn-primary">
                            Save
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </div>

    {{-- Area Guard AssignmentModal --}}

    <div class="modal fade" id="assignAreaGuardModal">

        <div class="modal-dialog">

            <form id="assignAreaGuardForm">

                @csrf

                <input type="hidden"
                       id="assign_area_id"
                       name="area_id">

                <div class="modal-content">

                    <div class="modal-header">
                        <h5>Assign Guard to Area</h5>
                    </div>

                    <div class="modal-body">

                        <div class="form-group">

                            <label>Guard</label>

                            <select class="form-control"
                                    name="user_id">

                                @foreach($guards as $guard)
                                    <option value="{{ $guard->id }}">
                                        {{ $guard->firstname }} {{ $guard->lastname }}
                                    </option>
                                @endforeach

                            </select>

                        </div>

                    </div>

                    <div class="modal-footer">

                        <button class="btn btn-primary">
                            Assign
                        </button>

                    </div>

                </div>

            </form>

        </div>

    </div>

    <div class="modal fade" id="branchModal">
        <div class="modal-dialog">

            <form id="branchForm">
                @csrf

                <input type="hidden" name="area_id" id="branch_area_id">

                <div class="modal-content">

                    <div class="modal-header">
                        <h5 class="modal-title">Create Branch</h5>
                    </div>

                    <div class="modal-body">

                        <div class="form-group">
                            <label>Branch Name</label>
                            <input type="text" name="name" class="form-control" maxlength="255">
                        </div>

                        <div class="form-group">
                            <label>Address</label>
                            <textarea name="address" class="form-control"></textarea>
                        </div>

                        <div class="form-group">
                            <label>Province</label>
                            <input type="text" name="province" class="form-control" maxlength="255">
                        </div>

                        <div class="form-group">
                            <label>Barangay</label>
                            <input type="text" name="baranggay" class="form-control" maxlength="255">
                        </div>

                        <div class="form-group">
                            <label>Operation Start</label>
                            <input type="date" name="operation_start" class="form-control">
                        </div>

                        <div class="form-group">
                            <label>Operation End</label>
                            <input type="date" name="operation_end" class="form-control">
                        </div>


                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary" type="submit">
                            Save
                        </button>
                    </div>

                </div>
            </form>

        </div>
    </div>

    <div class="modal fade" id="assignGuardModal">
        <div class="modal-dialog">
            <form id="assignGuardForm">

                @csrf

                <input type="hidden"
                       name="branch_id"
                       id="guard_branch_id">

                <div class="modal-content">

                    <div class="modal-header">
                        <h5>Assign Guard</h5>
                    </div>

                    <div class="modal-body">

                        <div class="form-group">
                            <label>Guard</label>

                            <select name="user_id"
                                    class="form-control">

                                @foreach($guards as $guard)
                                    <option value="{{ $guard->id }}">
                                        {{ $guard->firstname }} {{ $guard->lastname }}
                                    </option>
                                @endforeach

                            </select>
                        </div>

                    </div>

                    <div class="modal-footer">
                        <button class="btn btn-primary">
                            Assign
                        </button>
                    </div>

                </div>

            </form>
        </div>
    </div>
@endsection

@push('custom-scripts')
    <script>
        $(document).ready(function () {


            // CLICK AREA → LOAD BRANCHES
            $('.area-item').on('click', function () {


                selectedAreaId = $(this).data('id');
                //let selectedAreaId = areaId;


                $('.area-item').removeClass('active');
                $(this).addClass('active');

                loadBranches(selectedAreaId);
                loadAreaGuards(selectedAreaId);
            });



            // CREATE AREA (AJAX)
            $('#areaForm').on('submit', function (e) {
                e.preventDefault();

                $.ajax({
                    url: "{{ route('areas.store') }}",
                    method: "POST",
                    data: $(this).serialize(),
                    success: function (area) {

                        // append new area to list
                        $('#areas-list').append(`
                    <li class="list-group-item area-item"
                        data-id="${area.id}"
                        style="cursor:pointer;">
                        ${area.name}
                    </li>
                `);

                        $('#areaModal').modal('hide');
                        $('#areaForm')[0].reset();
                    },
                    error: function () {
                        alert('Failed to create area');
                    }
                });

            });

            function loadAreaGuards(areaId)
            {
                $('#guards-container').html('<p>Loading guards...</p>');

                $.get('/areas/' + areaId + '/guards', function (guards) {

                    let html = '';

                    if (guards.length === 0) {

                        html = `
                <div class="alert alert-light">
                    No guards assigned to this area.
                </div>
            `;

                    } else {

                        html = `
                <table class="table table-sm table-hover table-bordered">
                    <thead>
                        <tr>
                            <th>Employee No.</th>
                            <th>Name</th>
                            <th>Position</th>
                        </tr>
                    </thead>
                    <tbody>
            `;

                        guards.forEach(function (guard) {

                            html += `
                    <tr>
                        <td>${guard.employee_no ?? ''}</td>
                        <td>${guard.name}</td>
                        <td>${guard.position ? guard.position.name : ''}</td>
                    </tr>
                `;

                        });

                        html += `
                    </tbody>
                </table>
            `;
                    }

                    $('#guards-container').html(html);

                });
            }

            function loadBranches(selectedAreaId) {

                $('#branches-container').html('<p>Loading...</p>');

                $.get('/areas/' + selectedAreaId + '/branches', function (res) {

                    if (res.length === 0) {
                        $('#branches-container').html('<p class="text-muted">No branches found</p>');
                        return;
                    }

                    let html = '<ul class="list-group">';

                    res.forEach(function(branch) {

                        html += `
        <li class="list-group-item branch-item"
            data-id="${branch.id}"
            style="cursor:pointer;">

            <div>
                <strong>${branch.name ?? 'Unnamed Branch'}</strong>
            </div>

            <small class="text-muted">
                ${branch.address ?? ''}
            </small>

        </li>
    `;
                    });

                    html += '</ul>';

                    $('#branches-container').html(html);
                });
            }

        });

        $('#btnCreateBranch').on('click', function () {

            if (!selectedAreaId) {
                alert('Please select an area first');
                return;
            }

            // inject selected area into hidden input
            $('#branch_area_id').val(selectedAreaId);

            $('#branchModal').modal('show');
        });

        $('#branchForm').on('submit', function (e) {
            e.preventDefault();

            $.ajax({
                url: "{{ route('branches.store') }}",
                type: "POST",
                data: $(this).serialize(),

                success: function (branch) {

                    // reload branches for selected area
                    loadBranches(selectedAreaId);

                    $('#branchModal').modal('hide');
                    $('#branchForm')[0].reset();
                },

                error: function () {
                    alert('Failed to create branch');
                }
            });
        });

        let selectedBranchId = null;

        $(document).on('click', '.branch-item', function () {

            selectedBranchId = $(this).data('id');

            $('.branch-item').removeClass('active');
            $(this).addClass('active');

            loadGuards(selectedBranchId);
        });

        function loadGuards(branchId)
        {
            $('#guards-container').html('Loading...');

            $.get('/branches/' + branchId + '/guards', function(response){

                let html = '';

                if(response.length === 0)
                {
                    html = '<p class="text-muted">No guards assigned.</p>';
                }
                else
                {
                    html += '<ul class="list-group">';

                    response.forEach(function(user){

                        html += `
                    <li class="list-group-item">
                        <strong>${user.firstname} ${user.lastname}</strong>
                        <br>
                        <small>${user.email ?? ''}</small>
                        <br>
                        <small>${user.phone ?? ''}</small>
                    </li>
                `;
                    });

                    html += '</ul>';
                }

                $('#guards-container').html(html);
            });
        }

        $('#btnAssignGuard').click(function(){

            if(!selectedBranchId)
            {
                alert('Please select a branch first.');
                return;
            }

            $('#guard_branch_id').val(selectedBranchId);

            $('#assignGuardModal').modal('show');
        });

        $('#assignGuardForm').on('submit', function(e) {

            e.preventDefault();

            $.ajax({
                url: '/branches/assign-guard',
                type: 'POST',
                data: $(this).serialize(),

                success: function(response) {

                    $('#assignGuardModal').modal('hide');

                    // reload guards for selected branch
                    loadGuards(selectedBranchId);

                    alert('Guard assigned successfully');
                },

                error: function(xhr) {

                    console.log(xhr.responseText);

                    alert('Failed to assign guard');
                }
            });

        });

        let selectedAreaId = null;

        $(document).on('click', '.area-item', function(){

            selectedAreaId = $(this).data('id');

            $('.area-item').removeClass('active');
            $(this).addClass('active');

            loadBranches(selectedAreaId);

        });

        $('#btnAssignAreaGuard').click(function(){

            if(selectedAreaId == null)
            {
                alert('Please select an area first.');
                return;
            }

            $('#assign_area_id').val(selectedAreaId);

            $('#assignAreaGuardModal').modal('show');

        });

        $(document).on('submit','#assignAreaGuardForm',function(e){

            e.preventDefault();

            $.post(
                "{{ route('areas.assign.guard') }}",
                $(this).serialize(),
                function(){

                    $('#assignAreaGuardModal').modal('hide');

                }
            );

        });




    </script>
@endpush
