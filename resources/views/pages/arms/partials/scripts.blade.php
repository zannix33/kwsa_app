<script>

    $(function(){

        $.get(
            "{{ route('arms.dashboard.charts') }}",
            function(response){

                new Chart(

                    document.getElementById('maintenanceChart'),

                    {

                        type:'bar',

                        data:{

                            labels:response.maintenance.map(x=>x.month),

                            datasets:[{

                                label:'Maintenance',

                                data:response.maintenance.map(
                                    x=>x.total_cost
                                )

                            }]

                        }

                    }

                );

                new Chart(

                    document.getElementById('inspectionChart'),

                    {

                        type:'line',

                        data:{

                            labels:response.inspections.map(
                                x=>x.month
                            ),

                            datasets:[{

                                label:'Inspections',

                                data:response.inspections.map(
                                    x=>x.inspections
                                )

                            }]

                        }

                    }

                );

            }

        );

    });

</script>
