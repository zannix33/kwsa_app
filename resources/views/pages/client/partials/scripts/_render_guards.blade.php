<script>

    /*
    |--------------------------------------------------------------------------
    | Render Guards Table
    |--------------------------------------------------------------------------
    |
    | mode:
    |   area   = show Remove Area button
    |   branch = show Remove Branch button
    |
    */

    function renderGuardsTable(guards, mode)
    {
        let html = '';

        if (!guards || guards.length === 0) {

            $('#guards-modal-table').html(
                emptyRow(5, 'No guards assigned.')
            );

            return;
        }

        $.each(guards, function (index, guard) {

            let removeButton = '';

            if (mode === 'area') {

                removeButton = `
                <button type="button"
                        class="btn btn-sm btn-danger btn-remove-area-guard"
                        data-user="${guard.id}"
                        title="Remove Guard">

                    <i class="fa fa-trash"></i>

                </button>
            `;

            } else {

                removeButton = `
                <button type="button"
                        class="btn btn-sm btn-danger btn-remove-branch-guard"
                        data-user="${guard.id}"
                        title="Remove Guard">

                    <i class="fa fa-trash"></i>

                </button>
            `;

            }

            let companies = mode === 'area' && Array.isArray(guard.assigned_companies) && guard.assigned_companies.length
                ? guard.assigned_companies.map(escapeHtml).join(', ')
                : (mode === 'area' ? 'Not assigned' : '—');

            html += `
            <tr>

                <td>

                    ${escapeHtml(guard.employee_no ?? '')}

                </td>

                <td>

                    <strong>

                        ${escapeHtml(guard.name ?? '')}

                    </strong>

                </td>

                <td>

                    ${escapeHtml(guard.position ? guard.position.name : '-')}

                </td>

                <td>
                    ${companies}
                </td>

                <td class="text-center">

                    ${removeButton}

                </td>

            </tr>
        `;

        });

        $('#guards-modal-table').html(html);

    }

</script>
