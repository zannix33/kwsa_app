<script>

    /*
    |--------------------------------------------------------------------------
    | Load Area Guards
    |--------------------------------------------------------------------------
    */

    function loadAreaGuards(areaId)
    {

        if (!areaId) {

            $('#guards-table').html(
                emptyRow(4, 'Select an Area.')
            );

            return;

        }

        $('#guards-table').html(
            loadingRow(4, 'Loading guards...')
        );

        $.ajax({

            url: '/areas/' + areaId + '/guards',

            type: 'GET',

            dataType: 'json',

            success: function (guards) {

                renderGuardsTable(guards, 'area');

            },

            error: function () {

                $('#guards-table').html(
                    errorRow(4, 'Unable to load guards.')
                );

            }

        });

    }

</script>
