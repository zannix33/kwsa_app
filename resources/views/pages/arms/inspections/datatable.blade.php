<script>

    $(function(){

        $('#inspectionTable').DataTable({

            processing:true,

            serverSide:true,

            responsive:true,

            ajax:{
                url:"{{ route('arms.inspections.datatable') }}"
            },

            columns:[

                {data:'firearm'},

                {data:'property_no'},

                {data:'inspection_date'},

                {data:'inspector'},

                {data:'result_badge'},

                {data:'next_inspection'},

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
