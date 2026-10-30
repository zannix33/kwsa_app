<div class="modal fade" id="areaModal" tabindex="-1" role="dialog"
     aria-labelledby="areaModalLabel" aria-hidden="true">

    <div class="modal-dialog" role="document">
        <form id="areaForm">
            @csrf

            <input type="hidden" name="area_id" id="area_id">

            <input type="hidden"
                   name="client_id"
                   value="{{ $company->id }}">

            <div class="modal-content">

                <div class="modal-header">
                    <h5 class="modal-title" id="areaModalLabel">
                        Create Area
                    </h5>

                    <button type="button"
                            class="close"
                            data-dismiss="modal"
                            aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>

                <div class="modal-body">

                    <div class="alert alert-danger d-none"
                         id="area-errors">
                    </div>

                    <div class="form-group">
                        <label for="areaName">
                            Area Name
                            <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               id="areaName"
                               name="name"
                               class="form-control"
                               required>
                    </div>

                    <div class="form-group">
                        <label for="areaDescription">Description</label>

                        <textarea name="description"
                                  id="areaDescription"
                                  class="form-control"
                                  rows="3"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="areaRegion">
                            Region
                        </label>

                        <select name="region_id"
                                id="areaRegion"
                                class="form-control">
                            <option value="">Select Region (Optional)</option>
                        </select>

                        <small class="form-text text-muted">
                            Select the region where this area is located.
                            Its applicable pay rate will be based on the
                            region and the payroll work date.
                        </small>
                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">
                        Cancel
                    </button>

                    <button type="submit"
                            class="btn btn-primary">
                        <i class="fa fa-save"></i>
                        Save Area
                    </button>

                </div>

            </div>
        </form>
    </div>
</div>

