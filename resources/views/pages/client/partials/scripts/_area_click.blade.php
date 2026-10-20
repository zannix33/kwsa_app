<script>

    /*
    |--------------------------------------------------------------------------
    | Area Selected
    |--------------------------------------------------------------------------
    |
    | When an area is clicked:
    |   - Highlight the selected area
    |   - Store the selected area ID
    |   - Clear any selected branch
    |   - Load branches for the area
    |   - Load guards assigned to the area
    |
    */

    $(document).on('click', '.area-item', function () {

        // Remove previous highlight
        $('.area-item').removeClass('table-primary');

        // Highlight selected area
        $(this).addClass('table-primary');

        // Save selected area
        selectedAreaId = $(this).data('id');

        // Reset branch selection
        selectedBranchId = null;

        // Remove branch highlight
        $('.branch-item').removeClass('table-primary');

        // Reset guards table while loading
        $('#guards-table').html(
            loadingRow(4, 'Loading area guards...')
        );

        // Reset branches table while loading
        $('#branches-table').html(
            loadingRow(2, 'Loading branches...')
        );

        // Load branches for the selected area
        loadBranches(selectedAreaId);

        // Load guards assigned directly to the area
        loadAreaGuards(selectedAreaId);

    });


    /*
    |--------------------------------------------------------------------------
    | Double Click (Optional)
    |--------------------------------------------------------------------------
    |
    | Reserved for future use (Area Edit)
    |
    */

    $(document).on('dblclick', '.area-item', function () {

        // Future:
        // editArea($(this).data('id'));

    });


$(document).ready(function () {

    $('#areaModal').on('show.bs.modal', function () {
        loadAreaRegions();
    });

    function loadAreaRegions(selectedRegionId = '') {
        var $region = $('#areaRegion');

        $region.html(
            '<option value="">Loading regions...</option>'
        );

        $.ajax({
            url: "{{ route('areas.regions') }}",
            type: 'GET',
            dataType: 'json',

            success: function (regions) {
                $region.empty();

                $region.append(
                    $('<option>', {
                        value: '',
                        text: 'Select Region (Optional)'
                    })
                );

                $.each(regions, function (index, region) {
                    $region.append(
                        $('<option>', {
                            value: region.id,
                            text: region.code + ' - ' + region.name
                        })
                    );
                });

                $region.val(selectedRegionId);
            },

            error: function () {
                $region.html(
                    '<option value="">Unable to load regions</option>'
                );
            }
        });
    }

});




</script>
