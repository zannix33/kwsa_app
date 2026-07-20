<div class="card">

    <div class="card-header d-flex justify-content-between align-items-center">

        <strong>Guards</strong>

        <div>

            {{-- Assign to Area --}}
            <button type="button"
                    class="btn btn-success btn-sm"
                    id="btnAssignAreaGuard">
                <i class="fa fa-map-marker"></i>
                Area
            </button>

            {{-- Assign to Branch --}}
            <button type="button"
                    class="btn btn-primary btn-sm"
                    id="btnAssignBranchGuard">
                <i class="fa fa-building"></i>
                Branch
            </button>

        </div>

    </div>

    <div class="card-body p-0">

        <div class="table-responsive">

            <table class="table table-hover table-bordered mb-0">

                <thead class="thead-light">

                <tr>
                    <th width="120">Employee No.</th>
                    <th>Name</th>
                    <th width="180">Position</th>
                    <th width="90" class="text-center">
                        Action
                    </th>
                </tr>

                </thead>

                <tbody id="guards-table">

                <tr>
                    <td colspan="4"
                        class="text-center text-muted">

                        Select an Area or Branch to view guards.

                    </td>
                </tr>

                </tbody>

            </table>

        </div>

    </div>

</div>
