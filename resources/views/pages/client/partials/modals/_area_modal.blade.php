<div class="modal fade" id="areaModal" tabindex="-1" role="dialog" aria-labelledby="areaModalLabel" aria-hidden="true">

    <div class="modal-dialog" role="document">

        <form id="areaForm">

            @csrf

            <input type="hidden"
                   name="company_id"
                   value="{{ $company->id }}">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title" id="areaModalLabel">
                        Create Area
                    </h5>

                    <button type="button"
                            class="close"
                            data-dismiss="modal">

                        <span>&times;</span>

                    </button>

                </div>

                <div class="modal-body">

                    <div class="alert alert-danger d-none"
                         id="area-errors">
                    </div>

                    <div class="form-group">

                        <label>
                            Area Name
                            <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               name="name"
                               class="form-control"
                               required>

                    </div>

                    <div class="form-group">

                        <label>Description</label>

                        <textarea name="description"
                                  class="form-control"
                                  rows="3"></textarea>

                    </div>

                    <div class="form-group">

                        <label>Rate</label>

                        <input type="number"
                               name="rate"
                               class="form-control"
                               step="0.01"
                               min="0">

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
