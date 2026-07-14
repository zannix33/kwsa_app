@csrf

@if(isset($license))
    @method('PUT')
@endif

<div class="row">

    <div class="col-lg-8">

        <div class="card shadow-sm">

            <div class="card-header bg-primary text-white">

                <i class="fa fa-id-card"></i>

                License Information

            </div>

            <div class="card-body">

                <div class="form-row">

                    <div class="form-group col-md-6">

                        <label>Firearm <span class="text-danger">*</span></label>

                        <select
                            name="arm_id"
                            class="form-control select2 @error('arm_id') is-invalid @enderror"
                            required>

                            <option value="">Select Firearm</option>

                            @foreach($arms as $arm)

                                <option
                                    value="{{ $arm->id }}"
                                    {{ old('arm_id', $license->arm_id ?? '') == $arm->id ? 'selected' : '' }}>

                                    {{ $arm->property_no }}
                                    -
                                    {{ $arm->manufacturer }}
                                    {{ $arm->model }}
                                    ({{ $arm->serial_number }})

                                </option>

                            @endforeach

                        </select>

                        @error('arm_id')

                        <div class="invalid-feedback">

                            {{ $message }}

                        </div>

                        @enderror

                    </div>

                    <div class="form-group col-md-6">

                        <label>License Number <span class="text-danger">*</span></label>

                        <input
                            type="text"
                            name="license_number"
                            class="form-control @error('license_number') is-invalid @enderror"
                            value="{{ old('license_number', $license->license_number ?? '') }}"
                            required>

                        @error('license_number')

                        <div class="invalid-feedback">

                            {{ $message }}

                        </div>

                        @enderror

                    </div>

                </div>

                <div class="form-row">

                    <div class="form-group col-md-6">

                        <label>Registration Number</label>

                        <input
                            type="text"
                            name="registration_number"
                            class="form-control"
                            value="{{ old('registration_number', $license->registration_number ?? '') }}">

                    </div>

                    <div class="form-group col-md-6">

                        <label>License Type</label>

                        <select
                            name="license_type"
                            class="form-control">

                            <option value="">Select Type</option>

                            <option value="Firearm Registration"
                                {{ old('license_type', $license->license_type ?? '') == 'Firearm Registration' ? 'selected' : '' }}>
                                Firearm Registration
                            </option>

                            <option value="Permit to Carry"
                                {{ old('license_type', $license->license_type ?? '') == 'Permit to Carry' ? 'selected' : '' }}>
                                Permit to Carry
                            </option>

                            <option value="Government Issued"
                                {{ old('license_type', $license->license_type ?? '') == 'Government Issued' ? 'selected' : '' }}>
                                Government Issued
                            </option>

                        </select>

                    </div>

                </div>

                <div class="form-row">

                    <div class="form-group col-md-4">

                        <label>Issue Date</label>

                        <input
                            type="date"
                            name="issue_date"
                            class="form-control"
                            value="{{ old('issue_date', isset($license->issue_date) ? $license->issue_date->format('Y-m-d') : '') }}">

                    </div>

                    <div class="form-group col-md-4">

                        <label>Expiry Date</label>

                        <input
                            type="date"
                            name="expiry_date"
                            class="form-control"
                            value="{{ old('expiry_date', isset($license->expiry_date) ? $license->expiry_date->format('Y-m-d') : '') }}">

                    </div>

                    <div class="form-group col-md-4">

                        <label>Renewal Date</label>

                        <input
                            type="date"
                            name="renewal_date"
                            class="form-control"
                            value="{{ old('renewal_date', isset($license->renewal_date) ? $license->renewal_date->format('Y-m-d') : '') }}">

                    </div>

                </div>

                <div class="form-row">

                    <div class="form-group col-md-6">

                        <label>Issued By</label>

                        <input
                            type="text"
                            name="issued_by"
                            class="form-control"
                            value="{{ old('issued_by', $license->issued_by ?? '') }}">

                    </div>

                    <div class="form-group col-md-6">

                        <label>Issuing Office</label>

                        <input
                            type="text"
                            name="issuing_office"
                            class="form-control"
                            value="{{ old('issuing_office', $license->issuing_office ?? '') }}">

                    </div>

                </div>

                <div class="form-row">

                    <div class="form-group col-md-6">

                        <label>Status</label>

                        <select
                            name="status"
                            class="form-control">

                            <option value="Active"
                                {{ old('status', $license->status ?? 'Active') == 'Active' ? 'selected' : '' }}>
                                Active
                            </option>

                            <option value="Pending Renewal"
                                {{ old('status', $license->status ?? '') == 'Pending Renewal' ? 'selected' : '' }}>
                                Pending Renewal
                            </option>

                            <option value="Expired"
                                {{ old('status', $license->status ?? '') == 'Expired' ? 'selected' : '' }}>
                                Expired
                            </option>

                            <option value="Suspended"
                                {{ old('status', $license->status ?? '') == 'Suspended' ? 'selected' : '' }}>
                                Suspended
                            </option>

                            <option value="Cancelled"
                                {{ old('status', $license->status ?? '') == 'Cancelled' ? 'selected' : '' }}>
                                Cancelled
                            </option>

                        </select>

                    </div>

                    <div class="form-group col-md-6">

                        <label>License Attachment</label>

                        <input
                            type="file"
                            name="attachment"
                            class="form-control-file">

                        @if(isset($license) && $license->attachment)

                            <small class="d-block mt-2">

                                <a href="{{ asset('storage/'.$license->attachment) }}"
                                   target="_blank">

                                    View Uploaded File

                                </a>

                            </small>

                        @endif

                    </div>

                </div>

                <div class="form-group">

                    <label>Remarks</label>

                    <textarea
                        name="remarks"
                        rows="4"
                        class="form-control">{{ old('remarks', $license->remarks ?? '') }}</textarea>

                </div>

            </div>

        </div>

    </div>

    <div class="col-lg-4">

        <div class="card shadow-sm">

            <div class="card-header bg-secondary text-white">

                Firearm Information

            </div>

            <div class="card-body" id="armPreview">

                <div class="text-center text-muted">

                    Select a firearm to view details.

                </div>

            </div>

        </div>

        <div class="card shadow-sm mt-3">

            <div class="card-body">

                <button
                    class="btn btn-success btn-block">

                    <i class="fa fa-save"></i>

                    {{ isset($license) ? 'Update License' : 'Save License' }}

                </button>

                <a
                    href="{{ route('arms.licenses.index') }}"
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

        $('select[name=arm_id]').change(function(){

            let id = $(this).val();

            if(!id){

                $('#armPreview').html('<div class="text-center text-muted">Select a firearm.</div>');

                return;

            }

            $.get('/arms/api/firearms/'+id,function(data){

                $('#armPreview').html(

                    '<div class="text-center mb-3">'+
                    '<img src="'+data.photo_url+'" class="img-fluid img-thumbnail" style="max-height:180px;">'+
                    '</div>'+

                    '<table class="table table-sm table-bordered">'+

                    '<tr><th>Property</th><td>'+data.property_no+'</td></tr>'+

                    '<tr><th>Serial</th><td>'+data.serial_number+'</td></tr>'+

                    '<tr><th>Manufacturer</th><td>'+data.manufacturer+'</td></tr>'+

                    '<tr><th>Model</th><td>'+data.model+'</td></tr>'+

                    '<tr><th>Caliber</th><td>'+data.caliber+'</td></tr>'+

                    '<tr><th>Status</th><td>'+data.status+'</td></tr>'+

                    '</table>'

                );

            });

        });

        @if(old('arm_id') || isset($license))

        $('select[name=arm_id]').trigger('change');

        @endif

    </script>

@endpush
