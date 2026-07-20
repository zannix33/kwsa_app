<script>

    /*
    |--------------------------------------------------------------------------
    | Open Create Area Modal
    |--------------------------------------------------------------------------
    */

    $('#areaModal').on('shown.bs.modal', function () {

        clearErrors('#area-errors');

        $('#areaForm')[0].reset();

    });


    /*
    |--------------------------------------------------------------------------
    | Create Area
    |--------------------------------------------------------------------------
    */

    $('#areaForm').submit(function (e) {

        e.preventDefault();

        clearErrors('#area-errors');

        let form = $(this);

        $.ajax({

            url: "{{ route('areas.store') }}",

            type: "POST",

            data: form.serialize(),

            beforeSend: function () {

                form.find('button[type=submit]')
                    .prop('disabled', true)
                    .html('<i class="fa fa-spinner fa-spin"></i> Saving...');

            },

            success: function (response) {

                $('#areaModal').modal('hide');

                form[0].reset();

                /*
                |--------------------------------------------------------------------------
                | Reload Areas
                |--------------------------------------------------------------------------
                */

                selectedAreaId = response.id;

                selectedBranchId = null;

                loadAreas(selectedAreaId);

                /*
                |--------------------------------------------------------------------------
                | Optional Success Message
                |--------------------------------------------------------------------------
                */

                // alert(response.message);

            },

            error: function (xhr) {

                if (xhr.status === 422) {

                    showErrors(
                        '#area-errors',
                        xhr.responseJSON.errors
                    );

                } else {

                    alert('Unable to save Area.');

                }

            },

            complete: function () {

                form.find('button[type=submit]')
                    .prop('disabled', false)
                    .html('<i class="fa fa-save"></i> Save Area');

            }

        });

    });

</script>
