<script>

    $(function(){

        let table = $('#maintenanceTable').DataTable({

            processing:true,

            serverSide:true,

            responsive:true,

            ajax:{

                url:"{{ route('arms.maintenances.datatable') }}"

            },

            columns:[

                {data:'firearm'},

                {data:'property_no'},

                {data:'maintenance_type'},

                {data:'maintenance_date'},

                {data:'performed_by'},

                {data:'cost'},

                {data:'status_badge'},

                {data:'actions',orderable:false,searchable:false}

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
