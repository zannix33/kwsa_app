<script>

    /*
    |--------------------------------------------------------------------------
    | Escape HTML
    |--------------------------------------------------------------------------
    | Safely displays text received from the server.
    */
        function escapeHtml(value) {
            return String(value ?? '').replace(/[&<>"']/g, function (char) {
                return {
                    '&': '&amp;',
                    '<': '&lt;',
                    '>': '&gt;',
                    '"': '&quot;',
                    "'": '&#39;'
                }[char];
            });
        }


    /*
    |--------------------------------------------------------------------------
    | Load Areas
    |--------------------------------------------------------------------------
    | Loads all Areas belonging to the current Company.
    */
    function loadAreas(selectedId = null)
    {
        $('#areas-table').html(
            loadingRow(3, 'Loading areas...')
        );

        $.ajax({
            url: "{{ route('companies.areas', $company->id) }}",
            type: "GET",
            dataType: "json",

            success: function(areas) {
                let html = '';

                if (areas.length === 0) {
                    $('#areas-table').html(
                        emptyRow(3, 'No Areas Found.')
                    );

                    return;
                }

                $.each(areas, function(index, area) {
                    let active = '';

                    if (selectedId == area.id) {
                        active = 'table-primary';
                        selectedAreaId = area.id;
                    }

                    let regionName = area.region_name
                        ? `${area.region_code} - ${area.region_name}`
                        : 'Not assigned';

                    html += `
        <tr class="area-item ${active}"
        data-id="${area.id}"
        style="cursor:pointer;">

            <td style="overflow-wrap:anywhere;">
            <strong>${escapeHtml(area.name ?? '')}</strong>
        <br>
            <small class="text-muted">
                ${escapeHtml(area.description ?? '')}
            </small>
        </td>

        <td style="overflow-wrap:anywhere;">${escapeHtml(regionName)}</td>
        <td class="text-center">
            <button type="button" class="btn btn-sm btn-outline-primary btn-edit-area" style="white-space:nowrap; padding-left:.4rem; padding-right:.4rem;"
                data-id="${area.id}"
                data-name="${escapeHtml(area.name ?? '')}"
                data-description="${escapeHtml(area.description ?? '')}"
                data-region-id="${area.region_id ?? ''}">
                <i class="fa fa-edit"></i> Edit
            </button>
        </td>
        </tr>
        `;
                });

                $('#areas-table').html(html);

                /*
                |--------------------------------------------------------------------------
                | Automatically load selected Area
                |--------------------------------------------------------------------------
                */
                if (selectedAreaId) {
                    loadBranches(selectedAreaId);
                    loadAreaGuards(selectedAreaId);
                }
            },

            error: function() {
                $('#areas-table').html(
                    errorRow(3, 'Unable to load Areas.')
                );
            }
        });
    }

    /*
    |--------------------------------------------------------------------------
    | Initial Load
    |--------------------------------------------------------------------------
    */
    $(function() {
        loadAreas();
    });


</script>
