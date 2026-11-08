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

        // Guards are shown on demand in the guards popup.

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

        // Editing is handled by the explicit Edit button.

    });

</script>

<script>
$(document).on('click', '#btnViewBranchGuards', function () {
    if (!selectedBranchId) { alert('Please select a Branch first.'); return; }
    $('#guardsModalLabel').text('Branch Guards');
    $('#guards-modal-table').html(loadingRow(4, 'Loading branch guards...'));
    $('#btnAssignAreaGuard').hide(); $('#btnAssignBranchGuard').show();
    $('#guardsModal').modal('show');
    loadBranchGuards(selectedBranchId);
});
</script>
