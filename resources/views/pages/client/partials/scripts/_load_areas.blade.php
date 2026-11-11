<script>

    // Delegated search continues to work after AJAX replaces the area rows.
    function applyAreaSearch() {
        var query = String($('#areaSearch').val() || '').trim().toLocaleLowerCase();
        var $rows = $('#areas-table tr.area-item');
        var visible = 0;
        $('#area-search-no-results').remove();
        $rows.each(function () {
            var haystack = String($(this).attr('data-search') || $(this).text()).toLocaleLowerCase();
            var match = !query || haystack.indexOf(query) !== -1;
            $(this).toggle(match);
            if (match) visible++;
        });
        if (query && $rows.length && visible === 0) {
            $('#areas-table').append('<tr id="area-search-no-results"><td colspan="3" class="text-center text-muted py-3">No areas match your search.</td></tr>');
        }
        return visible;
    }

    $(document).off('input.areaSearch', '#areaSearch')
        .on('input.areaSearch', '#areaSearch', applyAreaSearch);
    $(document).off('click.areaSearch', '#clearAreaSearch')
        .on('click.areaSearch', '#clearAreaSearch', function () {
            $('#areaSearch').val('');
            applyAreaSearch();
            $('#areaSearch').trigger('focus');
        });

    function setAreaSearchHint(text) {
        $('#areaSearchHint').text(text);
    }


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
    var loadedClientAreas = [];

    function loadAreas(selectedId = null)
    {
        setAreaSearchHint('Loading areas...');
        $('#areas-table').html(
            loadingRow(3, 'Loading areas...')
        );

        $.ajax({
            url: "{{ route('companies.areas', $company->id) }}",
            type: "GET",
            dataType: "json",

            success: function(areas) {
                loadedClientAreas = areas || [];
                let html = '';

                if (areas.length === 0) {
                    $('#areas-table').html(
                        emptyRow(3, 'No Areas Found.')
                    );
                    setAreaSearchHint('0 areas.');
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
        data-search="${escapeHtml([area.name, area.description, area.region_code, area.region_name].filter(Boolean).join(' '))}"
        style="cursor:pointer;">

            <td class="client-area-cell">
                <strong class="client-area-name">${escapeHtml(area.name ?? '')}</strong>
                ${area.description ? `<div class="text-muted client-area-description">${escapeHtml(area.description)}</div>` : ''}
            </td>

            <td class="client-region-cell">${escapeHtml(regionName)}</td>
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
                $('#areaSearch').prop('disabled', false);
                setAreaSearchHint(areas.length + ' area(s).');
                applyAreaSearch();

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
                setAreaSearchHint('Unable to search areas until they load.');
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
