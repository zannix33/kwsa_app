<div class="card">
    <div class="card-header branch-card-header">
        <div class="branch-toolbar-top" style="display:flex !important; flex-direction:row !important; align-items:center !important; justify-content:space-between !important; width:100% !important; gap:12px !important;">
            <strong class="branch-card-title">Branches</strong>
            <div class="branch-card-actions" style="display:flex !important; flex-direction:row !important; align-items:center !important; justify-content:flex-end !important; gap:8px !important; margin-left:auto !important; flex:0 0 auto !important; white-space:nowrap !important;">
                <button type="button" class="btn btn-info btn-sm" id="btnViewBranchGuards">
                    <i class="fa fa-users"></i> View Guards
                </button>
                <button type="button" class="btn btn-primary btn-sm" id="btnCreateBranch">
                    <i class="fa fa-plus"></i> Create
                </button>
            </div>
        </div>

        <div class="branch-search-row" style="margin-top:10px !important;">
            <div class="branch-search-controls client-search-controls" style="display:flex !important; flex-flow:row nowrap !important; align-items:center !important; gap:8px !important; width:100% !important; min-width:0 !important;">
                <div class="branch-search-input-wrap client-search-input-wrap" style="position:relative !important; display:block !important; flex:1 1 0% !important; width:auto !important; min-width:0 !important;">
                    <i class="fa fa-search branch-search-icon" aria-hidden="true"></i>
                    <input type="search" class="form-control form-control-sm" id="branchSearch"
                           placeholder="Search branches by name, address, barangay, province, or company..."
                           aria-label="Search branches" autocomplete="off" disabled>
                </div>
                <button type="button" class="btn btn-outline-secondary btn-sm branch-search-clear client-search-clear" style="position:static !important; display:inline-flex !important; flex:0 0 64px !important; width:64px !important; min-width:64px !important; max-width:64px !important; height:36px !important; margin:0 !important; align-items:center !important; justify-content:center !important; white-space:nowrap !important;"
                        id="clearBranchSearch" disabled>Clear</button>
            </div>
            <small class="text-muted branch-search-hint" id="branchSearchHint">Select an Area first.</small>
        </div>
    </div>

    <div class="card-body p-0">
        <div class="table-responsive">
            <table class="table table-hover table-bordered mb-0">
                <thead class="thead-light">
                    <tr>
                        <th>Branch</th>
                        <th width="180">Operation Hours</th>
                        <th width="90" class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody id="branches-table">
                    <tr><td colspan="3" class="text-center text-muted">Select an Area to load branches</td></tr>
                </tbody>
            </table>
        </div>
    </div>
</div>

<style>
#branches-table td, #branches-table th {
    padding: .7rem;
    vertical-align: middle;
}
#branches-table td strong { font-size: 1rem; }

@media (max-width: 575.98px) {
    .branch-card-header { padding: 10px 12px !important; }
    .branch-toolbar-top { align-items: flex-start; }
    .branch-card-actions { gap: 4px; }
    .branch-card-actions .btn { padding: .25rem .45rem; font-size: 12px; }
    .branch-search-row { align-items: stretch; }
    .branch-search-controls { display: grid !important; grid-template-columns: minmax(0, 1fr) 64px !important; width: 100% !important; }
    .branch-search-input-wrap { width: 100% !important; min-width: 0 !important; }
    .branch-search-hint { white-space: normal; }
}
</style>
