<script>

    $(function(){

        $('#licenseTable').DataTable({

            processing:true,

            serverSide:true,

            responsive:true,

            ajax:"{{ route('arms.licenses.datatable') }}",

            columns:[

                {data:'firearm'},

                {data:'license_number'},

                {data:'registration_number'},

                {data:'issue_date'},

                {data:'expiry_date'},

                {data:'status_badge'},

                {

                    data:'actions',

                    searchable:false,

                    orderable:false

                }

            ],

            dom:'Bfrtip',

            buttons:[

                'excel',

                'pdf',

                'print',

                'colvis'

            ]

        });

    });

</script>
