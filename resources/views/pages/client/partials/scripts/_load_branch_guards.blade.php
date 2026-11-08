<script>

    /*
    |--------------------------------------------------------------------------
    | Load Branch Guards
    |--------------------------------------------------------------------------
    */

    function loadBranchGuards(branchId)
    {

        if (!branchId) {

            $('#guards-modal-table').html(
                emptyRow(4, 'Select a Branch.')
            );

            return;

        }

        $('#guards-modal-table').html(
            loadingRow(4, 'Loading guards...')
        );

        $.ajax({

            url: '/branches/' + branchId + '/guards',

            type: 'GET',

            dataType: 'json',

            success: function (guards) {

                $('#guardsModalContext').text('Branch guards');
                $('#btnAssignAreaGuard').hide();
                $('#btnAssignBranchGuard').show();
                renderGuardsTable(guards, 'branch');

            },

            error: function () {

                $('#guards-modal-table').html(
                    errorRow(4, 'Unable to load guards.')
                );

            }

        });

    }

</script>
