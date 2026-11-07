<script>

    $(function(){

        $('#ammunitionTable').DataTable({

            processing:true,

            serverSide:true,

            responsive:true,

            ajax:"{{ route('arms.ammunition.datatable') }}",

            columns:[

                {data:'caliber'},

                {data:'brand'},

                {data:'lot_number'},

                {data:'quantity_on_hand'},

                {data:'unit_cost'},

                {data:'inventory_value'},

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
