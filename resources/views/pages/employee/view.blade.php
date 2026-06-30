@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')

    <div class="container">

        <div class="d-flex justify-content-between mb-3">
            <h3>Employees</h3>
            <a href="{{ route('hr.employee.create') }}" class="btn btn-success">Add Employee</a>
        </div>

        <div class="card mb-3">

            <div class="card-body">

                <div class="row">

                    <div class="col-md-3">

                        <label>Position</label>

                        <select class="form-control" id="position">

                            <option value="">All</option>

                            <option>Security Guard</option>

                            <option>Security Officer</option>

                        </select>

                    </div>

                    <div class="col-md-3">

                        <label>Branch</label>

                        <select class="form-control" id="branch">

                            <option value="">All Branches</option>

                            @foreach($branches as $branch)

                                <option value="{{ $branch->id }}">

                                    {{ $branch->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="col-md-2">

                        <label>Age From</label>

                        <input
                            type="number"
                            id="age_from"
                            class="form-control">

                    </div>

                    <div class="col-md-2">

                        <label>Age To</label>

                        <input
                            type="number"
                            id="age_to"
                            class="form-control">

                    </div>

                    <div class="col-md-2 d-flex align-items-end">

                        <button
                            class="btn btn-primary btn-block"
                            id="btnFilter">

                            Filter

                        </button>

                    </div>

                </div>

            </div>

        </div>

        <table class="table table-bordered table-striped" id="employees-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Photo</th>
                <th>Employee No.</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Position</th>
                <th>Age</th>
                <th>Phone</th>
                <th>Action</th>
            </tr>
            </thead>
        </table>

    </div>

@endsection

@push('custom-scripts')

    <script>

        $(function(){

            var table = $('#employees-table').DataTable({

                processing:true,
                serverSide:true,

                ajax:{
                    url:"{{ route('hr.employee.index') }}",

                    data:function(d){

                        d.position=$("#position").val();

                        d.branch_id=$("#branch").val();

                        d.age_from=$("#age_from").val();

                        d.age_to=$("#age_to").val();

                    }

                },

                columns:[

                    {data:'DT_RowIndex',orderable:false,searchable:false},

                    {
                        data:'photo',
                        name:'photo',
                        orderable:false,
                        searchable:false
                    },

                    {data:'name'},

                    {data:'fullname'},

                    {data:'email'},

                    {data:'position'},

                    {data:'age'},

                    {data:'phone'},

                    {data:'action',orderable:false,searchable:false},

                ]

            });

            $("#btnFilter").click(function(){

                table.draw();

            });

        });

    </script>
@endpush
