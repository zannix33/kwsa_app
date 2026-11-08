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

        // Reset branches table while loading
        $('#branches-table').html(
            loadingRow(2, 'Loading branches...')
        );

        // Load branches for the selected area
        loadBranches(selectedAreaId);

        // Guards are shown on demand in the guards popup.

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
        loadAreaRegions(window.areaEditRegionId || '');
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

<script>
$(document).on('click', '#btnViewAreaGuards', function () {
    if (!selectedAreaId) { alert('Please select an Area first.'); return; }
    $('#guardsModalLabel').text('Area Guards');
    $('#guards-modal-table').html(loadingRow(4, 'Loading area guards...'));
    $('#btnAssignAreaGuard').show(); $('#btnAssignBranchGuard').hide();
    $('#guardsModal').modal('show');
    loadAreaGuards(selectedAreaId);
});
</script>
