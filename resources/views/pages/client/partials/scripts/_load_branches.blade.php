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
                emptyRow(3, 'Select an Area first.')
            );

            $('#guards-table').html(
                emptyRow(4, 'Select a Branch.')
            );

            selectedBranchId = null;

            return;

        }

        $('#branches-table').html(
            loadingRow(3, 'Loading branches...')
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
                        emptyRow(3, 'No branches found.')
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

                            <strong>${escapeHtml(branch.name ?? '')} ${branch.company_name ? '<span class=\"badge badge-info ml-1\">' + escapeHtml(branch.company_name) + '</span>' : '<span class=\"badge badge-light ml-1\">Unassigned</span>'}</strong>

                            <br>

                            <small class="text-muted branch-address" style="display:block; max-width:100%; white-space:normal; overflow-wrap:anywhere; word-break:break-word;">

                                ${escapeHtml(branch.address ?? '')}

                            </small>

                            <small class="text-muted branch-location" style="display:block; max-width:100%; white-space:normal; overflow-wrap:anywhere; word-break:break-word;">

                                ${escapeHtml(branch.baranggay ?? '')}

                                ${branch.baranggay && branch.province ? ', ' : ''}

                                ${escapeHtml(branch.province ?? '')}

                            </small>

                        </td>

                        <td class="text-center">

                            <small class="d-block">Weekdays: ${formatTime(branch.operation_start)} - ${formatTime(branch.operation_end)}</small>
                            <small class="d-block">Sat: ${formatTime(branch.operation_saturday_start)} - ${formatTime(branch.operation_saturday_end)}</small>
                            <small class="d-block">Sun: ${formatTime(branch.operation_sunday_start)} - ${formatTime(branch.operation_sunday_end)}</small>

                        </td>
                        <td class="text-center">
                            <button type="button" class="btn btn-sm btn-outline-primary btn-edit-branch"
                                data-id="${branch.id}"
                                data-company-id="${branch.company_id ?? ''}"
                                data-name="${escapeHtml(branch.name ?? '')}"
                                data-address="${escapeHtml(branch.address ?? '')}"
                                data-baranggay="${escapeHtml(branch.baranggay ?? '')}"
                                data-province="${escapeHtml(branch.province ?? '')}"
                                data-operation-start="${branch.operation_start ?? ''}"
                                data-operation-end="${branch.operation_end ?? ''}"
                                data-operation-saturday-start="${branch.operation_saturday_start ?? ''}"
                                data-operation-saturday-end="${branch.operation_saturday_end ?? ''}"
                                data-operation-sunday-start="${branch.operation_sunday_start ?? ''}"
                                data-operation-sunday-end="${branch.operation_sunday_end ?? ''}">
                                <i class="fa fa-edit"></i> Edit
                            </button>
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
                    errorRow(3, 'Unable to load branches.')
                );

                $('#guards-table').html(
                    errorRow(4, 'Unable to load guards.')
                );

            }

        });

    }

</script>
