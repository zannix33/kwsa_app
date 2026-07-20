<div class="modal fade" id="branchModal" tabindex="-1" role="dialog" aria-labelledby="branchModalLabel" aria-hidden="true">

    <div class="modal-dialog modal-lg" role="document">

        <form id="branchForm">

            @csrf

            <input type="hidden"
                   name="area_id"
                   id="branch_area_id">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title" id="branchModalLabel">
                        Create Branch
                    </h5>

                    <button type="button"
                            class="close"
                            data-dismiss="modal">

                        <span>&times;</span>

                    </button>

                </div>

                <div class="modal-body">

                    <div class="alert alert-danger d-none"
                         id="branch-errors">
                    </div>

                    <div class="form-group">

                        <label>
                            Branch Name
                            <span class="text-danger">*</span>
                        </label>

                        <input type="text"
                               name="name"
                               class="form-control"
                               required>

                    </div>

                    <div class="form-group">

                        <label>Address</label>

                        <textarea name="address"
                                  class="form-control"
                                  rows="2"></textarea>

                    </div>

                    <div class="form-row">

                        <div class="form-group col-md-6">

                            <label>Barangay</label>

                            <input type="text"
                                   name="baranggay"
                                   class="form-control">

                        </div>

                        <div class="form-group col-md-6">

                            <label>Province</label>

                            <input type="text"
                                   name="province"
                                   class="form-control">

                        </div>

                    </div>

                    <div class="form-row">

                        <div class="form-group col-md-6">

                            <label>Operation Start</label>

                            <input type="time"
                                   name="operation_start"
                                   class="form-control">

                        </div>

                        <div class="form-group col-md-6">

                            <label>Operation End</label>

                            <input type="time"
                                   name="operation_end"
                                   class="form-control">

                        </div>

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
                        Save Branch

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>
