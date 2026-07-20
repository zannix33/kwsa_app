<script>

    /*
    |--------------------------------------------------------------------------
    | Branch Selected
    |--------------------------------------------------------------------------
    |
    | When a branch is clicked:
    |   - Highlight the selected branch
    |   - Store the selected branch ID
    |   - Load guards assigned to the branch
    |
    */

    $(document).on('click', '.branch-item', function () {

        // Remove previous highlight
        $('.branch-item').removeClass('table-primary');

        // Highlight selected branch
        $(this).addClass('table-primary');

        // Store selected branch
        selectedBranchId = $(this).data('id');

        // Show loading while retrieving guards
        $('#guards-table').html(
            loadingRow(4, 'Loading branch guards...')
        );

        // Load assigned guards
        loadBranchGuards(selectedBranchId);

    });


    /*
    |--------------------------------------------------------------------------
    | Create Branch
    |--------------------------------------------------------------------------
    |
    | A branch cannot exist without an Area.
    |
    */

    $('#btnCreateBranch').click(function () {

        if (selectedAreaId == null) {

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
    | Double Click (Reserved)
    |--------------------------------------------------------------------------
    |
    | Future:
    | Edit Branch
    |
    */

    $(document).on('dblclick', '.branch-item', function () {

        // editBranch($(this).data('id'));

    });

</script>
