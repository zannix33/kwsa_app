<script>

    /*
    |--------------------------------------------------------------------------
    | Open Assign Area Guard Modal
    |--------------------------------------------------------------------------
    */

    $('#btnAssignAreaGuard').click(function () {

        if (!selectedAreaId) {

            alert('Please select an Area first.');

            return;

        }

        clearErrors('#assign-area-errors');

        $('#assignAreaGuardForm')[0].reset();

        $('#assign_area_id').val(selectedAreaId);

        $('#assignAreaGuardModal').modal('show');

    });


    /*
    |--------------------------------------------------------------------------
    | Assign Guard to Area
    |--------------------------------------------------------------------------
    */

    $('#assignAreaGuardForm').submit(function (e) {

        e.preventDefault();

        clearErrors('#assign-area-errors');

        let form = $(this);

        $.ajax({

            url: "{{ route('areas.assign.guard') }}",

            type: "POST",

            data: form.serialize(),

            beforeSend:function(){

                form.find('button[type=submit]')
                    .prop('disabled', true)
                    .html('<i class="fa fa-spinner fa-spin"></i> Assigning...');

            },

            success:function(response){

                $('#assignAreaGuardModal').modal('hide');

                form[0].reset();

                loadAreaGuards(selectedAreaId);

            },

            error:function(xhr){

                if(xhr.status === 422){

                    showErrors(
                        '#assign-area-errors',
                        xhr.responseJSON.errors
                    );

                }else{

                    alert('Unable to assign guard.');

                }

            },

            complete:function(){

                form.find('button[type=submit]')
                    .prop('disabled', false)
                    .html('<i class="fa fa-user-plus"></i> Assign');

            }

        });

    });

</script>
