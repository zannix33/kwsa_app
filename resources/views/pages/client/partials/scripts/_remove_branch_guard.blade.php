<script>

    /*
    |--------------------------------------------------------------------------
    | Remove Guard from Branch
    |--------------------------------------------------------------------------
    |
    | Removes the selected guard from the currently selected Branch.
    |
    */

    $(document).on('click', '.btn-remove-branch-guard', function () {

        if (!selectedBranchId) {

            alert('Please select a Branch.');

            return;

        }

        let userId = $(this).data('user');

        if (!confirmAction('Remove this guard from the selected Branch?')) {

            return;

        }

        $.ajax({

            url: "{{ route('branches.remove.guard') }}",

            type: "POST",

            data: {

                _token: "{{ csrf_token() }}",

                branch_id: selectedBranchId,

                user_id: userId

            },

            beforeSend:function(){

                $('#guards-table').html(
                    loadingRow(4, 'Removing guard...')
                );

            },

            success:function(response){

                loadBranchGuards(selectedBranchId);

            },

            error:function(xhr){

                if(xhr.status === 422){

                    alert('Validation failed.');

                }else{

                    alert('Unable to remove guard.');

                }

                loadBranchGuards(selectedBranchId);

            }

        });

    });

</script>
