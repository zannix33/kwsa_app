@csrf

@if(isset($assignment))
    @method('PUT')
@endif

<div class="row">

    <!-- Left Column -->
    <div class="col-lg-8">

        <div class="card shadow mb-3">

            <div class="card-header bg-primary text-white">

                Assignment Information

            </div>

            <div class="card-body">

                <div class="form-row">

                    <div class="form-group col-md-6">

                        <label>

                            Firearm

                        </label>

                        <select
                            name="arm_id"
                            id="arm_id"
                            class="form-control select2"
                            {{ isset($assignment) ? 'disabled' : '' }}
                            required>

                            <option value="">

                                Select Firearm

                            </option>

                            @foreach($arms as $arm)

                                <option
                                    value="{{ $arm->id }}"
                                    {{ old('arm_id',$assignment->arm_id ?? '')==$arm->id?'selected':'' }}>

                                    {{ $arm->property_no }}
                                    -
                                    {{ $arm->manufacturer }}
                                    {{ $arm->model }}
                                    ({{ $arm->serial_number }})

                                </option>

                            @endforeach

                        </select>

                        @if(isset($assignment))
                            <input type="hidden"
                                   name="arm_id"
                                   value="{{ $assignment->arm_id }}">
                        @endif

                    </div>

                    <div class="form-group col-md-6">

                        <label>

                            Employee

                        </label>

                        <select
                            name="user_id"
                            class="form-control select2"
                            {{ isset($assignment) ? 'disabled' : '' }}
                            required>

                            <option value="">

                                Select Employee

                            </option>

                            @foreach($users as $user)

                                <option
                                    value="{{ $user->id }}"
                                    {{ old('user_id',$assignment->user_id ?? '')==$user->id?'selected':'' }}>

                                    {{ $user->name }}

                                </option>

                            @endforeach

                        </select>

                        @if(isset($assignment))
                            <input type="hidden"
                                   name="user_id"
                                   value="{{ $assignment->user_id }}">
                        @endif

                    </div>

                </div>

                @if(!isset($assignment))

                    <div class="form-row">

                        <div class="form-group col-md-6">

                            <label>

                                Assigned Date

                            </label>

                            <input
                                type="date"
                                name="assigned_at"
                                class="form-control"
                                value="{{ old('assigned_at',date('Y-m-d')) }}">

                        </div>

                        <div class="form-group col-md-6">

                            <label>

                                Expected Return

                            </label>

                            <input
                                type="date"
                                name="expected_return"
                                class="form-control"
                                value="{{ old('expected_return') }}">

                        </div>

                    </div>

                @endif

                <div class="form-group">

                    <label>

                        Purpose

                    </label>

                    <textarea
                        class="form-control"
                        rows="3"
                        name="purpose"
                        {{ isset($assignment) ? 'readonly' : '' }}>{{ old('purpose',$assignment->purpose ?? '') }}</textarea>

                </div>

                @if(isset($assignment))

                    <hr>

                    <h5>

                        Return Information

                    </h5>

                    <div class="form-row">

                        <div class="form-group col-md-6">

                            <label>

                                Return Date

                            </label>

                            <input
                                type="date"
                                name="returned_at"
                                class="form-control"
                                value="{{ date('Y-m-d') }}">

                        </div>

                        <div class="form-group col-md-6">

                            <label>

                                Firearm Condition

                            </label>

                            <select
                                name="condition"
                                class="form-control">

                                <option value="Excellent">Excellent</option>
                                <option value="Good">Good</option>
                                <option value="Needs Cleaning">Needs Cleaning</option>
                                <option value="Needs Repair">Needs Repair</option>
                                <option value="Damaged">Damaged</option>

                            </select>

                        </div>

                    </div>

                    <div class="form-group">

                        <label>

                            Return Remarks

                        </label>

                        <textarea
                            class="form-control"
                            rows="3"
                            name="return_remarks"></textarea>

                    </div>

                @endif

            </div>

        </div>

    </div>

    <!-- Right Column -->
    <div class="col-lg-4">

        <div class="card shadow mb-3">

            <div class="card-header bg-dark text-white">

                Firearm Details

            </div>

            <div class="card-body">

                <div id="firearmInfo">

                    <div class="text-center text-muted">

                        Select a firearm to view details.

                    </div>

                </div>

            </div>

        </div>

        <div class="card shadow">

            <div class="card-body">

                <button
                    class="btn btn-success btn-block">

                    <i class="fa fa-save"></i>

                    {{ isset($assignment) ? 'Process Return' : 'Assign Firearm' }}

                </button>

                <a
                    href="{{ route('arms.assignments.index') }}"
                    class="btn btn-secondary btn-block">

                    Cancel

                </a>

            </div>

        </div>

    </div>

</div>

@push('custom-scripts')

    <script>

        $('.select2').select2({
            width:'100%'
        });

        $('#arm_id').change(function(){

            let id = $(this).val();

            if(!id){

                $('#firearmInfo').html(
                    '<div class="text-muted text-center">Select a firearm.</div>'
                );

                return;

            }

            $.get(

                "{{ url('/arms/api/firearms') }}/"+id,

                function(arm){

                    let html = `

                <table class="table table-sm table-bordered">

                    <tr>

                        <th>Property No.</th>

                        <td>${arm.property_no}</td>

                    </tr>

                    <tr>

                        <th>Serial</th>

                        <td>${arm.serial_number}</td>

                    </tr>

                    <tr>

                        <th>Make</th>

                        <td>${arm.manufacturer}</td>

                    </tr>

                    <tr>

                        <th>Model</th>

                        <td>${arm.model}</td>

                    </tr>

                    <tr>

                        <th>Caliber</th>

                        <td>${arm.caliber}</td>

                    </tr>

                    <tr>

                        <th>Status</th>

                        <td>${arm.status}</td>

                    </tr>

                </table>

            `;

                    if(arm.photo_url){

                        html =
                            '<div class="text-center mb-3">'+
                            '<img src="'+arm.photo_url+'" class="img-fluid img-thumbnail" style="max-height:180px;">'+
                            '</div>' + html;

                    }

                    $('#firearmInfo').html(html);

                });

        });

        @if(old('arm_id') || isset($assignment))
        $('#arm_id').trigger('change');
        @endif

    </script>

@endpush
