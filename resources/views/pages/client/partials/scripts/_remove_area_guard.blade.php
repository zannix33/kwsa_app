<script>

    /*
    |--------------------------------------------------------------------------
    | Remove Guard from Area
    |--------------------------------------------------------------------------
    |
    | Removes the selected guard from the currently selected Area.
    |
    */

    $(document).on('click', '.btn-remove-area-guard', function () {

        if (!selectedAreaId) {

            alert('Please select an Area.');

            return;

        }

        let userId = $(this).data('user');

        if (!confirmAction('Remove this guard from the selected Area?')) {

            return;

        }

        $.ajax({

            url: "{{ route('areas.remove.guard') }}",

            type: "POST",

            data: {

                _token: "{{ csrf_token() }}",

                area_id: selectedAreaId,

                user_id: userId

            },

            beforeSend:function(){

                $('#guards-table').html(
                    loadingRow(4, 'Removing guard...')
                );

            },

            success:function(response){

                loadAreaGuards(selectedAreaId);

            },

            error:function(){

                alert('Unable to remove guard.');

                loadAreaGuards(selectedAreaId);

            }

        });

    });

</script>
