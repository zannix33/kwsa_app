<div class="modal fade" id="guardsModal" tabindex="-1" role="dialog" aria-labelledby="guardsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <div>
                    <h5 class="modal-title mb-1" id="guardsModalLabel">Assigned Guards</h5>
                    <small class="text-muted" id="guardsModalContext">Select an area or branch.</small>
                </div>
                <div class="ml-auto d-flex align-items-center">
                    <button type="button" class="btn btn-success btn-sm mr-2" id="btnAssignAreaGuard"><i class="fa fa-user-plus"></i> Assign to Area</button>
                    <button type="button" class="btn btn-primary btn-sm mr-2" id="btnAssignBranchGuard"><i class="fa fa-user-plus"></i> Assign to Branch</button>
                    <button type="button" class="close ml-2" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
            </div>
            <div class="modal-body">
                <div class="table-responsive">
                    <table class="table table-hover table-bordered mb-0">
                        <thead class="thead-light"><tr><th style="width:140px;">Employee No.</th><th>Name</th><th style="width:180px;">Position</th><th class="text-center" style="width:90px;">Action</th></tr></thead>
                        <tbody id="guards-modal-table"><tr><td colspan="4" class="text-center text-muted">Select an Area or Branch, then click View Guards.</td></tr></tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>
<style>
#guardsModal .modal-dialog { max-width: 900px; }
#guardsModal .modal-title { font-size: 1.2rem; font-weight: 600; }
#guards-modal-table td { vertical-align: middle; padding: .75rem; }
@media (max-width: 575.98px) { #guardsModal .modal-header { flex-wrap: wrap; } #guardsModal .modal-header > div:last-child { width:100%; margin-top:.75rem; } #guardsModal .modal-header .btn { margin-bottom:.25rem; } }
</style>
