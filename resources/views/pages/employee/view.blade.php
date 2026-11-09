@extends('layout.master')

@push('plugin-styles')
    <style>
        .employee-index-page { max-width: 100%; }
        .employee-table-wrap { width: 100%; overflow-x: auto; -webkit-overflow-scrolling: touch; }
        #employees-table { min-width: 1050px; }
        .employee-page-header { gap: 10px; }
        @media (max-width: 1024px) {
            .employee-index-page { padding-left: 12px; padding-right: 12px; }
            .employee-page-header h3 { font-size: 1.35rem; }
            .employee-index-page .card-body { padding: 14px; }
        }
        @media (max-width: 767.98px) {
            .employee-page-header { align-items: flex-start !important; }
            .employee-page-header .btn { margin-left: auto; }
        }
    </style>
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')

    <div class="container-fluid employee-index-page">

        <div class="d-flex justify-content-between align-items-center flex-wrap mb-3 employee-page-header">
            <h3 class="mb-2 mb-md-0">Employees</h3>
            <a href="{{ route('hr.employee.create') }}" class="btn btn-success mb-2">Add Employee</a>
        </div>

        <div class="card mb-3">

            <div class="card-body">

                <div class="row">

                    <div class="col-12 col-sm-6 col-lg-3 mb-3">

                        <label>Position</label>

                        <select class="form-control" id="position">

                            <option value="">All</option>

                            <option>Security Guard</option>

                            <option>Security Officer</option>

                        </select>

                    </div>

                    <div class="col-12 col-sm-6 col-lg-3 mb-3">

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

                    <div class="col-12 col-sm-6 col-lg-2 mb-3">

                        <label>Age From</label>

                        <input
                            type="number"
                            id="age_from"
                            class="form-control">

                    </div>

                    <div class="col-12 col-sm-6 col-lg-2 mb-3">

                        <label>Age To</label>

                        <input
                            type="number"
                            id="age_to"
                            class="form-control">

                    </div>

                    <div class="col-12 col-sm-6 col-lg-2 d-flex align-items-end mb-3">

                        <button
                            class="btn btn-primary btn-block"
                            id="btnFilter">

                            Filter

                        </button>

                    </div>

                </div>

            </div>

        </div>

        <div class="table-responsive employee-table-wrap">
        <table class="table table-bordered table-striped w-100" id="employees-table">
            <thead>
            <tr>
                <th>#</th>
                <th>Photo</th>
                <th>Employee No.</th>
                <th>Full Name</th>
                <th>Email</th>
                <th>Position</th>
                <th>Employment Status</th>
                <th>Age</th>
                <th>Phone</th>
                <th>Action</th>
            </tr>
            </thead>
        </table>
        </div>

    </div>

@endsection

@push('custom-scripts')

    <script>

        $(function(){

            var table = $('#employees-table').DataTable({

                processing:true,
                serverSide:true,
                scrollX:true,
                autoWidth:false,

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

                    {data:'employee_no'},

                    {data:'fullname'},

                    {data:'email'},

                    {data:'position'},

                    {data:'employment_status'},

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
