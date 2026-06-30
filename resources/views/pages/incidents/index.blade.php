@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')

    <div class="container-fluid">

        <div class="card">

            <div class="card-header d-flex justify-content-between">

                <h4>
                    Incident Reports
                </h4>

                <div class="mb-3">
                    {{--

                    <a
                        href="{{route('incidents.excel')}}"
                        class="btn btn-success">

                        Excel

                    </a>

                    <a
                        href="{{route('incidents.pdf')}}"
                        class="btn btn-danger">

                        PDF

                    </a>

                    <a
                        href="{{route('incidents.print')}}"
                        class="btn btn-secondary">

                        Print

                    </a>
                    --}}

                </div>

                <a href="{{ route('incidents.create') }}"
                   class="btn btn-primary">

                    New Incident

                </a>

            </div>

            <div class="card-body">

                <form method="GET">

                    <div class="row">

                        <div class="col-md-2">

                            <select
                                id="category"
                                name="category"
                                class="form-control filter">

                                <option value="">Category</option>

                                <option value="Major">
                                    Major
                                </option>

                                <option value="Minor">
                                    Minor
                                </option>

                            </select>

                        </div>

                        <div class="col-md-2">

                            <select
                                id="status"
                                name="status"
                                class="form-control filter">

                                <option value="">Status</option>

                                <option>Open</option>
                                <option>Under Investigation</option>
                                <option>Resolved</option>
                                <option>Closed</option>

                            </select>

                        </div>

                        <div class="col-md-2">

                            <input
                                id="from"
                                type="date"
                                class="form-control filter"
                                name="from">

                        </div>

                        <div class="col-md-2">

                            <input
                                id="from"
                                type="date"
                                class="form-control filter"
                                name="to">

                        </div>

                        <div class="col-md-2">

                            <button
                                type="button"
                                id="btnFilter"
                                class="btn btn-success btn-block">

                                Filter

                            </button>

                        </div>

                    </div>

                </form>

                <hr>

                <table
                    id="incidentTable"
                    class="table table-bordered table-striped">

                    <thead>

                    <tr>

                        <th>Date</th>
                        <th>Guard</th>
                        <th>Branch</th>
                        <th>Area</th>
                        <th>Category</th>
                        <th>Type</th>
                        <th>Status</th>
                        <th width="150">Action</th>

                    </tr>

                    </thead>

                </table>

            </div>

        </div>

    </div>

@endsection

@push('custom-scripts')
    <script>
        $('#incidentTable').DataTable({

            processing:true,

            serverSide:true,

            ajax:{

                url:"{{ route('incidents.datatable') }}",

                data:function(d){

                    d.employee=$("#employee").val();

                    d.branch=$("#branch").val();

                    d.area=$("#area").val();

                    d.category=$("#category").val();

                    d.status=$("#status").val();

                    d.from=$("#from").val();

                    d.to=$("#to").val();

                }

            },

            columns:[

                {data:'incident_date'},

                {data:'guard'},

                {data:'branch'},

                {data:'area'},

                {data:'category'},

                {data:'incident_type'},

                {data:'status'},

                {data:'action'}

            ]

        });

        $('#btnFilter').click(function(){

            $('#incidentTable').DataTable().ajax.reload();

        });

        $('.filter').change(function(){

            $('#incidentTable').DataTable().ajax.reload();

        });
    </script>

@endpush
