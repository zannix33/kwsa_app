@csrf

<div class="card">

    <div class="card-header">
        Incident Information
    </div>

    <div class="card-body">

        <div class="row">

            {{-- Employee --}}
            <div class="col-md-6">

                <div class="form-group">

                    <label>
                        Employee <span class="text-danger">*</span>
                    </label>

                    <select
                        name="user_id"
                        id="employee_id"
                        class="form-control @error('employee_id') is-invalid @enderror"
                        required>

                        <option value="">Select Employee</option>

                        @foreach($employees as $employee)

                            <option
                                value="{{ $employee->id }}"
                                {{ old('user_id', $incident->user_id ?? '') == $employee->id ? 'selected' : '' }}>

                                {{ $employee->lastname }},
                                {{ $employee->firstname }}

                            </option>

                        @endforeach

                    </select>

                    @error('employee_id')
                    <span class="invalid-feedback">
                            {{ $message }}
                        </span>
                    @enderror

                </div>

            </div>

            {{-- Category --}}
            <div class="col-md-3">

                <div class="form-group">

                    <label>
                        Category
                    </label>

                    <select
                        name="category"
                        id="category"
                        class="form-control"
                        required>

                        <option value="">Select</option>

                        <option
                            value="Major"
                            {{ old('category',$incident->category ?? '')=='Major' ? 'selected':'' }}>
                            Major
                        </option>

                        <option
                            value="Minor"
                            {{ old('category',$incident->category ?? '')=='Minor' ? 'selected':'' }}>
                            Minor
                        </option>

                    </select>

                </div>

            </div>

            {{-- Incident Type --}}
            <div class="col-md-3">

                <div class="form-group">

                    <label>
                        Incident Type
                    </label>

                    <select
                        name="incident_type"
                        id="incident_type"
                        class="form-control"
                        required>

                    </select>

                </div>

            </div>

        </div>


        <div class="row">

            {{-- Branch --}}
            <div class="col-md-6">

                <div class="form-group">

                    <label>Branch</label>

                    <input
                        type="text"
                        class="form-control"
                        id="branch_name"
                        readonly>

                    <input
                        type="hidden"
                        name="branch_id"
                        id="branch_id"
                        value="{{ old('branch_id',$incident->branch_id ?? '') }}">

                </div>

            </div>

            {{-- Area --}}
            <div class="col-md-6">

                <div class="form-group">

                    <label>Area</label>

                    <input
                        type="text"
                        class="form-control"
                        id="area_name"
                        readonly>

                    <input
                        type="hidden"
                        name="area_id"
                        id="area_id"
                        value="{{ old('area_id',$incident->area_id ?? '') }}">

                </div>

            </div>

        </div>


        <div class="row">

            <div class="col-md-4">

                <div class="form-group">

                    <label>Date</label>

                    <input
                        type="date"
                        name="incident_date"
                        class="form-control"
                        value="{{ old('incident_date',isset($incident) ? $incident->incident_date->format('Y-m-d') : date('Y-m-d')) }}"
                        required>

                </div>

            </div>

            <div class="col-md-4">

                <div class="form-group">

                    <label>Time</label>

                    <input
                        type="time"
                        name="incident_time"
                        class="form-control"
                        value="{{ old('incident_time',$incident->incident_time ?? '') }}">

                </div>

            </div>

            <div class="col-md-4">

                <div class="form-group">

                    <label>Location</label>

                    <input
                        type="text"
                        name="location"
                        class="form-control"
                        value="{{ old('location',$incident->location ?? '') }}"
                        required>

                </div>

            </div>

        </div>


        <div class="form-group">

            <label>Description</label>

            <textarea
                class="form-control"
                rows="5"
                name="description"
                required>{{ old('description',$incident->description ?? '') }}</textarea>

        </div>


        <div class="form-group">

            <label>Immediate Action Taken</label>

            <textarea
                class="form-control"
                rows="4"
                name="action_taken">{{ old('action_taken',$incident->action_taken ?? '') }}</textarea>

        </div>
        <div class="form-group">
            <label>
                Evidence Attachments
            </label>

            <input
                type="file"
                name="attachments[]"
                class="form-control"
                multiple>

            <small class="text-muted">
                JPG, PNG, PDF, Word, Excel, MP4
            </small>



        </div>



        <div class="form-group">

            <label>Recommendation</label>

            <textarea
                class="form-control"
                rows="4"
                name="recommendation">{{ old('recommendation',$incident->recommendation ?? '') }}</textarea>

        </div>


        <div class="form-group">

            <label>Status</label>

            <select
                class="form-control"
                name="status">

                <option
                    value="Open"
                    {{ old('status',$incident->status ?? 'Open')=='Open'?'selected':'' }}>
                    Open
                </option>

                <option
                    value="Under Investigation"
                    {{ old('status',$incident->status ?? '')=='Under Investigation'?'selected':'' }}>
                    Under Investigation
                </option>

                <option
                    value="Resolved"
                    {{ old('status',$incident->status ?? '')=='Resolved'?'selected':'' }}>
                    Resolved
                </option>

                <option
                    value="Closed"
                    {{ old('status',$incident->status ?? '')=='Closed'?'selected':'' }}>
                    Closed
                </option>

            </select>

        </div>

    </div>

    <div class="card-footer">

        <button class="btn btn-primary">

            Save Incident

        </button>

        <a
            href="{{ route('incidents.index') }}"
            class="btn btn-secondary">

            Cancel

        </a>

    </div>

</div>
