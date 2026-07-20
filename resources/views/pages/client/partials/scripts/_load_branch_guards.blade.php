<script>

    /*
    |--------------------------------------------------------------------------
    | Load Branch Guards
    |--------------------------------------------------------------------------
    */

    function loadBranchGuards(branchId)
    {

        if (!branchId) {

            $('#guards-table').html(
                emptyRow(4, 'Select a Branch.')
            );

            return;

        }

        $('#guards-table').html(
            loadingRow(4, 'Loading guards...')
        );

        $.ajax({

            url: '/branches/' + branchId + '/guards',

            type: 'GET',

            dataType: 'json',

            success: function (guards) {

                renderGuardsTable(guards, 'branch');

            },

            error: function () {

                $('#guards-table').html(
                    errorRow(4, 'Unable to load guards.')
                );

            }

        });

    }

</script>
