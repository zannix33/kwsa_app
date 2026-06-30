<script>
    $(function () {

        let table = $('#assignmentTable').DataTable({

            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            pageLength: 25,
            order: [[4, 'desc']],

            ajax: {

                url: "{{ route('arms.assignments.datatable') }}",

                data: function (d) {

                    d.user = $('#employee').val();
                    d.branch = $('#branch').val();
                    d.status = $('#status').val();
                    d.assigned_date = $('#assigned_date').val();

                }

            },

            columns: [

                {
                    data: 'firearm',
                    name: 'arm.full_name'
                },

                {
                    data: 'property_no',
                    name: 'arm.property_no'
                },

                {
                    data: 'employee',
                    name: 'user.name'
                },

                {
                    data: 'branch',
                    name: 'branch.name'
                },

                {
                    data: 'assigned_at',
                    name: 'assigned_at'
                },

                {
                    data: 'expected_return',
                    name: 'expected_return'
                },

                {
                    data: 'status_badge',
                    name: 'status',
                    searchable: false
                },

                {
                    data: 'actions',
                    searchable: false,
                    orderable: false
                }

            ],

            dom:

                "<'row mb-2'<'col-md-6'B><'col-md-6'f>>" +

                "<'row'<'col-sm-12'tr>>" +

                "<'row mt-2'<'col-md-5'i><'col-md-7'p>>",

            buttons: [

                {
                    extend: 'excel',
                    className: 'btn btn-success btn-sm',
                    text: '<i class="fa fa-file-excel"></i> Excel'
                },

                {
                    extend: 'pdf',
                    className: 'btn btn-danger btn-sm',
                    text: '<i class="fa fa-file-pdf"></i> PDF'
                },

                {
                    extend: 'print',
                    className: 'btn btn-info btn-sm',
                    text: '<i class="fa fa-print"></i> Print'
                },

                {
                    extend: 'colvis',
                    className: 'btn btn-secondary btn-sm',
                    text: 'Columns'
                },

                {
                    text: '<i class="fa fa-sync"></i> Refresh',
                    className: 'btn btn-primary btn-sm',
                    action: function () {
                        table.ajax.reload(null, false);
                    }
                }

            ],

            createdRow: function (row, data) {

                if (data.is_overdue) {

                    $(row).addClass('table-danger');

                }

            }

        });

        $('#search').click(function () {

            table.draw();

        });

        $('#reset').click(function () {

            $('#employee').val('').trigger('change');
            $('#branch').val('').trigger('change');
            $('#status').val('');
            $('#assigned_date').val('');

            table.draw();

        });

        $('#employee,#branch,#status').change(function () {

            table.draw();

        });

        $('#assigned_date').change(function () {

            table.draw();

        });

    });
</script>
