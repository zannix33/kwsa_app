<script>
    $(document).on('click', '.btn-move-branch', function (e) {
        e.preventDefault();
        e.stopPropagation();

        const branchId = $(this).data('id');
        const branchName = $(this).data('name');
        const currentAreaId = String($(this).data('area-id'));
        const $select = $('#moveBranchAreaId').empty();

        (loadedClientAreas || []).forEach(function (area) {
            if (String(area.id) !== currentAreaId) {
                $select.append($('<option>', { value: area.id, text: area.name }));
            }
        });

        if (!$select.children().length) {
            alert('There are no other Areas available for this Client.');
            return;
        }

        $('#moveBranchId').val(branchId);
        $('#moveBranchName').text(branchName);
        $('#move-branch-error').text('');
        $('#moveBranchModal').modal('show');
    });

    $('#moveBranchForm').on('submit', function (e) {
        e.preventDefault();
        const branchId = $('#moveBranchId').val();
        const destinationAreaId = $('#moveBranchAreaId').val();

        $.ajax({
            url: '/branches/' + branchId + '/move-area',
            type: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content'), _method: 'PUT', area_id: destinationAreaId },
            success: function (response) {
                $('#moveBranchModal').modal('hide');
                if (String(selectedBranchId) === String(branchId)) {
                    selectedBranchId = null;
                    $('#guards-table').html(emptyRow(4, 'Select a Branch.'));
                }
                loadBranches(selectedAreaId);
                alert(response.message || 'Branch moved successfully.');
            },
            error: function (xhr) {
                const message = xhr.responseJSON?.message || 'Unable to move this Branch.';
                $('#move-branch-error').text(message);
            }
        });
    });

    $(document).on('click', '.btn-delete-branch', function (e) {
        e.preventDefault();
        e.stopPropagation();
        const branchId = $(this).data('id');
        const branchName = $(this).data('name');

        if (!confirm('Soft-delete branch "' + branchName + '"? It will disappear from active lists, but its guards and historical records will be retained.')) return;

        $.ajax({
            url: '/branches/' + branchId,
            type: 'POST',
            data: { _token: $('meta[name="csrf-token"]').attr('content'), _method: 'DELETE' },
            success: function (response) {
                if (String(selectedBranchId) === String(branchId)) {
                    selectedBranchId = null;
                    $('#guards-table').html(emptyRow(4, 'Select a Branch.'));
                }
                loadBranches(selectedAreaId);
                alert(response.message || 'Branch soft-deleted.');
            },
            error: function (xhr) {
                alert(xhr.responseJSON?.message || 'Unable to delete this Branch.');
            }
        });
    });
</script>
