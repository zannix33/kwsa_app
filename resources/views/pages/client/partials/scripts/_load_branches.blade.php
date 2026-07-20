<script>

    /*
    |--------------------------------------------------------------------------
    | Load Branches
    |--------------------------------------------------------------------------
    |
    | Loads all branches belonging to the selected Area.
    |
    */

    function loadBranches(areaId, selectedId = null)
    {

        if (!areaId) {

            $('#branches-table').html(
                emptyRow(2, 'Select an Area first.')
            );

            $('#guards-table').html(
                emptyRow(4, 'Select a Branch.')
            );

            selectedBranchId = null;

            return;

        }

        $('#branches-table').html(
            loadingRow(2, 'Loading branches...')
        );

        $.ajax({

            url: '/areas/' + areaId + '/branches',

            type: 'GET',

            dataType: 'json',

            success: function (branches) {

                let html = '';

                selectedBranchId = null;

                if (branches.length === 0) {

                    $('#branches-table').html(
                        emptyRow(2, 'No branches found.')
                    );

                    $('#guards-table').html(
                        emptyRow(4, 'No branches available.')
                    );

                    return;

                }

                $.each(branches, function (index, branch) {

                    let active = '';

                    if (selectedId && selectedId == branch.id) {

                        active = 'table-primary';

                        selectedBranchId = branch.id;

                    }

                    html += `

                    <tr class="branch-item ${active}"
                        data-id="${branch.id}"
                        style="cursor:pointer;">

                        <td>

                            <strong>${branch.name}</strong>

                            <br>

                            <small class="text-muted">

                                ${branch.address ?? ''}

                            </small>

                            <br>

                            <small class="text-muted">

                                ${branch.baranggay ?? ''}

                                ${branch.baranggay && branch.province ? ', ' : ''}

                                ${branch.province ?? ''}

                            </small>

                        </td>

                        <td class="text-center">

                            ${formatTime(branch.operation_start)}

                            -

                            ${formatTime(branch.operation_end)}

                        </td>

                    </tr>

                `;

                });

                $('#branches-table').html(html);

                /*
                |--------------------------------------------------------------------------
                | Auto-load selected Branch Guards
                |--------------------------------------------------------------------------
                */

                if (selectedBranchId) {

                    loadBranchGuards(selectedBranchId);

                } else {

                    $('#guards-table').html(
                        emptyRow(4, 'Select a Branch.')
                    );

                }

            },

            error: function () {

                $('#branches-table').html(
                    errorRow(2, 'Unable to load branches.')
                );

                $('#guards-table').html(
                    errorRow(4, 'Unable to load guards.')
                );

            }

        });

    }

</script>
