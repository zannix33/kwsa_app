<script>

    /*
    |--------------------------------------------------------------------------
    | Open Assign Branch Guard Modal
    |--------------------------------------------------------------------------
    */

    $('#btnAssignBranchGuard').click(function () {

        if (!selectedBranchId) {

            alert('Please select a Branch first.');

            return;

        }

        clearErrors('#assign-branch-errors');

        $('#assignBranchGuardForm')[0].reset();

        $('#assign_branch_id').val(selectedBranchId);

        $('#assignBranchGuardModal').modal('show');

    });


    /*
    |--------------------------------------------------------------------------
    | Assign Guard to Branch
    |--------------------------------------------------------------------------
    */

    $('#assignBranchGuardForm').submit(function (e) {

        e.preventDefault();

        clearErrors('#assign-branch-errors');

        let form = $(this);

        $.ajax({

            url: "{{ route('branches.assign.guard') }}",

            type: "POST",

            data: form.serialize(),

            beforeSend:function(){

                form.find('button[type=submit]')
                    .prop('disabled', true)
                    .html('<i class="fa fa-spinner fa-spin"></i> Assigning...');

            },

            success:function(response){

                $('#assignBranchGuardModal').modal('hide');

                form[0].reset();

                loadBranchGuards(selectedBranchId);

            },

            error:function(xhr){

                if(xhr.status === 422){

                    showErrors(
                        '#assign-branch-errors',
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
