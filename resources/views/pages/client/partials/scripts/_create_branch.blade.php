<script>

    /*
    |--------------------------------------------------------------------------
    | Open Create Branch Modal
    |--------------------------------------------------------------------------
    */

    $('#btnCreateBranch').click(function () {

        if (!selectedAreaId) {

            alert('Please select an Area first.');

            return;

        }

        clearErrors('#branch-errors');

        $('#branchForm')[0].reset();

        $('#branch_area_id').val(selectedAreaId);

        $('#branchModal').modal('show');

    });


    /*
    |--------------------------------------------------------------------------
    | Create Branch
    |--------------------------------------------------------------------------
    */

    $('#branchForm').submit(function (e) {

        e.preventDefault();

        clearErrors('#branch-errors');

        let form = $(this);

        $.ajax({

            url: "{{ route('branches.store') }}",

            type: "POST",

            data: form.serialize(),

            beforeSend:function(){

                form.find('button[type=submit]')
                    .prop('disabled', true)
                    .html('<i class="fa fa-spinner fa-spin"></i> Saving...');

            },

            success:function(response){

                $('#branchModal').modal('hide');

                form[0].reset();

                selectedBranchId = response.id;

                loadBranches(selectedAreaId);

            },

            error:function(xhr){

                if(xhr.status == 422){

                    showErrors(
                        '#branch-errors',
                        xhr.responseJSON.errors
                    );

                }else{

                    alert('Unable to create Branch.');

                }

            },

            complete:function(){

                form.find('button[type=submit]')
                    .prop('disabled', false)
                    .html('<i class="fa fa-save"></i> Save Branch');

            }

        });

    });

</script>
