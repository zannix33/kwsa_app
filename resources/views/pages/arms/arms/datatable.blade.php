<script>
    $(function () {

        let table = $('#armsTable').DataTable({

            processing: true,
            serverSide: true,
            responsive: true,
            autoWidth: false,
            pageLength: 25,
            order: [[1, 'asc']],

            ajax: {
                url: "{{ route('arms.datatable') }}",
                data: function (d) {

                    d.branch = $('#branch').val();
                    d.status = $('#status').val();
                    d.caliber = $('#caliber').val();
                    d.make = $('#make').val();

                }
            },

            columns: [

                {
                    data: 'photo',
                    name: 'photo',
                    orderable: false,
                    searchable: false
                },

                {
                    data: 'property_no',
                    name: 'property_no'
                },

                {
                    data: 'serial_no',
                    name: 'serial_no'
                },

                {
                    data: 'type',
                    name: 'type'
                },

                {
                    data: 'caliber',
                    name: 'caliber'
                },

                {
                    data: 'branch',
                    name: 'branch.name'
                },

                {
                    data: 'status_badge',
                    name: 'status'
                },

                {
                    data: 'assigned_to',
                    name: 'assigned_to'
                },

                {
                    data: 'actions',
                    orderable: false,
                    searchable: false
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

                        table.ajax.reload();

                    }
                }

            ]

        });

        $('#search').click(function () {

            table.draw();

        });

        $('#reset').click(function () {

            $('#branch').val('');

            $('#status').val('');

            $('#caliber').val('');

            $('#make').val('');

            table.draw();

        });

        $('#branch,#status').change(function () {

            table.draw();

        });

        $('#caliber,#make').keyup(function (e) {

            if (e.keyCode === 13) {

                table.draw();

            }

        });

    });
</script>
