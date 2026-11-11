<div class="modal fade" id="moveBranchModal" tabindex="-1" role="dialog" aria-labelledby="moveBranchModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <form id="moveBranchForm" class="modal-content">
            @csrf
            <div class="modal-header">
                <h5 class="modal-title" id="moveBranchModalLabel">Move Branch to Another Area</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
            </div>
            <div class="modal-body">
                <p class="mb-2">Move <strong id="moveBranchName"></strong> to another Area. Assigned guards and branch-linked history will remain attached.</p>
                <input type="hidden" id="moveBranchId">
                <div class="form-group mb-0">
                    <label for="moveBranchAreaId">Destination Area</label>
                    <select class="form-control" id="moveBranchAreaId" name="area_id" required></select>
                    <small class="text-muted">Only Areas belonging to this same Client are available.</small>
                </div>
                <div id="move-branch-error" class="text-danger small mt-2"></div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                <button type="submit" class="btn btn-info"><i class="fa fa-exchange-alt"></i> Move Branch</button>
            </div>
        </form>
    </div>
</div>
