<script>

    $(document).ready(function(){

        // Optional if Select2 is installed
        if ($.fn.select2) {
            $('#employee_id').select2({
                width: '100%'
            });
        }

        function loadEmployeeInformation(id)
        {
            if(id == ''){
                $('#branch_name').val('');
                $('#branch_id').val('');

                $('#area_name').val('');
                $('#area_id').val('');

                return;
            }

            $.ajax({

                url: "/incidents/employee/" + id,

                type: "GET",

                success:function(response){

                    $('#branch_name').val(response.branch_name);
                    $('#branch_id').val(response.branch_id);

                    $('#area_name').val(response.area_name);
                    $('#area_id').val(response.area_id);

                }

            });

        }

        $('#employee_id').change(function(){

            loadEmployeeInformation($(this).val());

        });

        @if(old('employee_id'))
        loadEmployeeInformation("{{ old('employee_id') }}");
        @elseif(isset($incident))
        loadEmployeeInformation("{{ $incident->employee_id }}");
        @endif

    });

</script>
