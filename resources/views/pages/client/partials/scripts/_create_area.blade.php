<script>

    /*
    |--------------------------------------------------------------------------
    | Open Create Area Modal
    |--------------------------------------------------------------------------
    */

    $('#areaModal').on('shown.bs.modal', function () {

        clearErrors('#area-errors');

        if (!editingAreaId) {
            $('#areaForm')[0].reset();
            $('#area_id').val('');
            $('#areaModalLabel').text('Create Area');
            $('#areaForm button[type=submit]').html(editingAreaId ? '<i class="fa fa-save"></i> Update Area' : '<i class="fa fa-save"></i> Save Area');
        }

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

            url: editingAreaId
                ? urls.updateAreaBase + '/' + editingAreaId
                : urls.createArea,

            type: "POST",

            data: form.serialize() + (editingAreaId ? '&_method=PUT' : ''),

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

                if (response.id) selectedAreaId = response.id;
                selectedBranchId = null;
                editingAreaId = null;
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
                    .html(editingAreaId ? '<i class="fa fa-save"></i> Update Area' : '<i class="fa fa-save"></i> Save Area');

            }

        });

    });

</script>

<script>
    // Edit Area: keep the row-selection click behavior untouched.
    $(document).on('click', '.btn-edit-area', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const $button = $(this);
        editingAreaId = $button.data('id');
        clearErrors('#area-errors');
        $('#areaForm')[0].reset();
        $('#area_id').val(editingAreaId);
        $('#areaName').val($button.attr('data-name'));
        $('#areaDescription').val($button.attr('data-description'));
        $('#areaModalLabel').text('Edit Area');
        $('#areaForm button[type=submit]').html('<i class="fa fa-save"></i> Update Area');
        // Set this BEFORE showing the modal because show.bs.modal loads the region list.
        // Otherwise the AJAX loader receives an empty ID and the selected region is lost.
        window.areaEditRegionId = String($button.attr('data-region-id') || '');
        $('#areaModal').modal('show');
    });

    $('#areaModal').on('hidden.bs.modal', function () {
        editingAreaId = null;
        window.areaEditRegionId = '';
        $('#area_id').val('');
        $('#areaModalLabel').text('Create Area');
        $('#areaForm button[type=submit]').html('<i class="fa fa-save"></i> Save Area');
    });
</script>
