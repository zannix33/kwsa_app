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

        <table class="table table-bordered table-striped" id="employees-table">
            <thead>
            <tr>
                <th>#</th>
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
        $(function () {

            $('#employees-table').DataTable({
                processing: true,
                serverSide: false, // change to true if large data
                ajax: "{{ route('hr.employee.index') }}",
                columns: [
                    { data: 'DT_RowIndex', name: 'DT_RowIndex' },
                    { data: 'name', name: 'name' },
                    { data: 'fullname', name: 'fullname' },
                    { data: 'email', name: 'email' },
                    { data: 'position', name: 'position' },
                    { data: 'age', name: 'age' },
                    { data: 'phone', name: 'phone' },
                    { data: 'action', name: 'action', orderable: false, searchable: false },
                ]
            });

        });
    </script>
@endpush
