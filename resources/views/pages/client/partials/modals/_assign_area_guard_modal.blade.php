<div class="modal fade"
     id="assignAreaGuardModal"
     tabindex="-1"
     role="dialog"
     aria-labelledby="assignAreaGuardModalLabel"
     aria-hidden="true">

    <div class="modal-dialog" role="document">

        <form id="assignAreaGuardForm">

            @csrf

            <input type="hidden"
                   name="area_id"
                   id="assign_area_id">

            <div class="modal-content">

                <div class="modal-header">

                    <h5 class="modal-title"
                        id="assignAreaGuardModalLabel">

                        Assign Guard to Area

                    </h5>

                    <button type="button"
                            class="close"
                            data-dismiss="modal">

                        <span>&times;</span>

                    </button>

                </div>

                <div class="modal-body">

                    <div class="alert alert-danger d-none"
                         id="assign-area-errors">
                    </div>

                    <div class="form-group">

                        <label>
                            Select Guard
                        </label>

                        <select name="user_id"
                                class="form-control"
                                required>

                            <option value="">
                                -- Select Guard --
                            </option>

                            @foreach($guards as $guard)

                                <option value="{{ $guard->id }}">

                                    {{ $guard->employee_no }}
                                    -
                                    {{ $guard->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

                <div class="modal-footer">

                    <button type="button"
                            class="btn btn-secondary"
                            data-dismiss="modal">

                        Cancel

                    </button>

                    <button type="submit"
                            class="btn btn-success">

                        <i class="fa fa-user-plus"></i>

                        Assign

                    </button>

                </div>

            </div>

        </form>

    </div>

</div>
