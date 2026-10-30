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

        if (!editingBranchId) {
            $('#branchForm')[0].reset();
            $('#branch_id').val('');
            $('#branchModalLabel').text('Create Branch');
            $('#branchForm button[type=submit]').html(editingBranchId ? '<i class="fa fa-save"></i> Update Branch' : '<i class="fa fa-save"></i> Save Branch');
        }

        $('#branch_area_id').val(selectedAreaId);
        $('#branch_company_id').val('');

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

            url: editingBranchId
                ? urls.updateBranchBase + '/' + editingBranchId
                : urls.createBranch,

            type: "POST",

            data: form.serialize() + (editingBranchId ? '&_method=PUT' : ''),

            beforeSend:function(){

                form.find('button[type=submit]')
                    .prop('disabled', true)
                    .html('<i class="fa fa-spinner fa-spin"></i> Saving...');

            },

            success:function(response){

                $('#branchModal').modal('hide');

                form[0].reset();

                if (response.id) selectedBranchId = response.id;
                else selectedBranchId = editingBranchId;
                editingBranchId = null;
                loadBranches(selectedAreaId, selectedBranchId);

            },

            error:function(xhr){

                if(xhr.status == 422){

                    showErrors(
                        '#branch-errors',
                        xhr.responseJSON.errors
                    );

                }else{

                    alert('Unable to save Branch.');

                }

            },

            complete:function(){

                form.find('button[type=submit]')
                    .prop('disabled', false)
                    .html(editingBranchId ? '<i class="fa fa-save"></i> Update Branch' : '<i class="fa fa-save"></i> Save Branch');

            }

        });

    });

</script>

<script>
    // Edit Branch: stop propagation so the row's selection handler is not triggered.
    $(document).on('click', '.btn-edit-branch', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const $button = $(this);
        editingBranchId = $button.data('id');
        clearErrors('#branch-errors');
        $('#branchForm')[0].reset();
        $('#branch_id').val(editingBranchId);
        $('#branch_area_id').val(selectedAreaId);
        $('#branchForm [name="name"]').val($button.attr('data-name'));
        $('#branch_company_id').val($button.attr('data-company-id') || '');
        $('#branchForm [name="address"]').val($button.attr('data-address'));
        $('#branchForm [name="baranggay"]').val($button.attr('data-baranggay'));
        $('#branchForm [name="province"]').val($button.attr('data-province'));
        $('#branchForm [name="operation_start"]').val(($button.attr('data-operation-start') || '').substring(0, 5));
        $('#branchForm [name="operation_end"]').val(($button.attr('data-operation-end') || '').substring(0, 5));
        $('#branchForm [name="operation_saturday_start"]').val(($button.attr('data-operation-saturday-start') || '').substring(0, 5));
        $('#branchForm [name="operation_saturday_end"]').val(($button.attr('data-operation-saturday-end') || '').substring(0, 5));
        $('#branchForm [name="operation_sunday_start"]').val(($button.attr('data-operation-sunday-start') || '').substring(0, 5));
        $('#branchForm [name="operation_sunday_end"]').val(($button.attr('data-operation-sunday-end') || '').substring(0, 5));
        $('#branchModalLabel').text('Edit Branch');
        $('#branchForm button[type=submit]').html('<i class="fa fa-save"></i> Update Branch');
        $('#branchModal').modal('show');
    });

    $('#branchModal').on('hidden.bs.modal', function () {
        editingBranchId = null;
        $('#branch_id').val('');
        $('#branchModalLabel').text('Create Branch');
        $('#branchForm button[type=submit]').html('<i class="fa fa-save"></i> Save Branch');
    });
</script>
