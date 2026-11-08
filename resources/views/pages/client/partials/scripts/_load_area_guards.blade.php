<script>

    /*
    |--------------------------------------------------------------------------
    | Load Area Guards
    |--------------------------------------------------------------------------
    */

    function loadAreaGuards(areaId)
    {

        if (!areaId) {

            $('#guards-modal-table').html(
                emptyRow(4, 'Select an Area.')
            );

            return;

        }

        $('#guards-modal-table').html(
            loadingRow(4, 'Loading guards...')
        );

        $.ajax({

            url: '/areas/' + areaId + '/guards',

            type: 'GET',

            dataType: 'json',

            success: function (guards) {

                $('#guardsModalContext').text('Area guards');
                $('#btnAssignAreaGuard').show();
                $('#btnAssignBranchGuard').hide();
                renderGuardsTable(guards, 'area');

            },

            error: function () {

                $('#guards-modal-table').html(
                    errorRow(4, 'Unable to load guards.')
                );

            }

        });

    }

</script>
