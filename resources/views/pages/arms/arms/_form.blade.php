@csrf

@if(isset($arm))
    @method('PUT')
@endif

<div class="row">

    <!-- LEFT COLUMN -->
    <div class="col-lg-8">

        <!-- Firearm Information -->
        <div class="card shadow mb-3">

            <div class="card-header bg-primary text-white">
                Firearm Information
            </div>

            <div class="card-body">

                @if ($errors->any())
                    <div class="alert alert-danger">
                        <strong>Please correct the following errors:</strong>
                        <ul class="mb-0 mt-2">
                            @foreach ($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <div class="row">

                    <div class="form-group col-md-4">
                        <label>Property No.</label>
                        <input type="text"
                               name="property_no"
                               class="form-control @error('property_no') is-invalid @enderror"
                               value="{{ old('property_no', $arm->property_no ?? '') }}"
                               required>

                        @error('property_no')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-md-4">
                        <label>Serial Number</label>
                        <input type="text"
                               name="serial_no"
                               class="form-control"
                               value="{{ old('serial_number', $arm->serial_number ?? '') }}">
                    </div>

                    <div class="form-group col-md-4">
                        <label>Firearm Type</label>

                        <select name="type"
                                class="form-control select2">

                            <option value="">Select Type</option>

                            <option value="Pistol"
                                {{ old('type',$arm->type ?? '')=='Pistol'?'selected':'' }}>
                                Pistol
                            </option>

                            <option value="Revolver"
                                {{ old('type',$arm->type ?? '')=='Revolver'?'selected':'' }}>
                                Revolver
                            </option>

                            <option value="Shotgun"
                                {{ old('type',$arm->type ?? '')=='Shotgun'?'selected':'' }}>
                                Shotgun
                            </option>

                            <option value="Rifle"
                                {{ old('type',$arm->type ?? '')=='Rifle'?'selected':'' }}>
                                Rifle
                            </option>

                        </select>

                    </div>

                </div>

                <div class="row">

                    <div class="form-group col-md-4">

                        <label>Manufacturer</label>

                        <input
                            type="text"
                            name="manufacturer"
                            class="form-control"
                            value="{{ old('manufacturer',$arm->manufacturer ?? '') }}">

                    </div>

                    <div class="form-group col-md-4">

                        <label>Model</label>

                        <input
                            type="text"
                            name="model"
                            class="form-control"
                            value="{{ old('model',$arm->model ?? '') }}">

                    </div>

                    <div class="form-group col-md-4">

                        <label>Caliber</label>

                        <input
                            type="text"
                            name="caliber"
                            class="form-control"
                            value="{{ old('caliber',$arm->caliber ?? '') }}">

                    </div>

                </div>

            </div>

        </div>

        <!-- Purchase Information -->
        <div class="card shadow mb-3">

            <div class="card-header bg-success text-white">
                Purchase Information
            </div>

            <div class="card-body">

                <div class="row">

                    <div class="form-group col-md-4">

                        <label>Purchase Date</label>

                        <input
                            type="date"
                            name="purchase_date"
                            class="form-control"
                            value="{{ old('purchase_date',$arm->purchase_date ?? '') }}">

                    </div>

                    <div class="form-group col-md-4">

                        <label>Purchase Cost</label>

                        <input
                            type="number"
                            step="0.01"
                            name="purchase_cost"
                            class="form-control"
                            value="{{ old('purchase_cost',$arm->purchase_cost ?? '') }}">

                    </div>

                    <div class="form-group col-md-4">

                        <label>Branch</label>

                        <select
                            name="branch_id"
                            class="form-control select2">

                            <option value="">
                                Select Branch
                            </option>

                            @foreach($branches as $branch)

                                <option
                                    value="{{ $branch->id }}"
                                    {{ old('branch_id',$arm->branch_id ?? '')==$branch->id?'selected':'' }}>

                                    {{ $branch->name }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                </div>

            </div>

        </div>

        <!-- Status -->
        <div class="card shadow">

            <div class="card-header bg-warning">
                Status
            </div>

            <div class="card-body">

                <div class="row">

                    <div class="form-group col-md-4">

                        <label>Status</label>

                        <select
                            name="status"
                            class="form-control">

                            @foreach([
                                'Available',
                                'Issued',
                                'Maintenance',
                                'Lost',
                                'Retired'
                            ] as $status)

                                <option
                                    value="{{ $status }}"
                                    {{ old('status',$arm->status ?? '')==$status?'selected':'' }}>

                                    {{ $status }}

                                </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="form-group col-md-8">

                        <label>Remarks</label>

                        <textarea
                            class="form-control"
                            rows="3"
                            name="remarks">{{ old('remarks',$arm->remarks ?? '') }}</textarea>

                    </div>

                </div>

            </div>

        </div>

    </div>

    <!-- RIGHT COLUMN -->
    <div class="col-lg-4">

        <!-- Photo -->
        <div class="card shadow mb-3">

            <div class="card-header bg-dark text-white">
                Firearm Photo
            </div>

            <div class="card-body text-center">

                @php
                    $photo = isset($arm) && $arm->photo
                        ? asset('storage/'.$arm->photo)
                        : 'https://via.placeholder.com/250x250?text=No+Image';
                @endphp

                <img
                    id="preview"
                    src="{{ $photo }}"
                    class="img-fluid img-thumbnail mb-3">

                <input
                    type="file"
                    name="photo"
                    id="photo"
                    class="form-control-file">

            </div>

        </div>

        <!-- QR / Barcode -->
        <div class="card shadow mb-3">

            <div class="card-header bg-info text-white">
                Asset Identification
            </div>

            <div class="card-body text-center">

                @if(isset($arm))

                    {!! $arm->qr_code !!}

                    <hr>

                    {!! $arm->barcode !!}

                @else

                    <div class="text-muted">
                        QR Code and barcode will be generated after saving.
                    </div>

                @endif

            </div>

        </div>

        <!-- Save -->
        <div class="card shadow">

            <div class="card-body">

                <button
                    class="btn btn-success btn-block">

                    <i class="fa fa-save"></i>

                    Save Firearm

                </button>

                <a
                    href="{{ route('arms.index') }}"
                    class="btn btn-secondary btn-block">

                    Cancel

                </a>

            </div>

        </div>

    </div>

</div>

@push('custom-scripts')
    <script>
        document.getElementById('photo').addEventListener('change', function(e){

            if (!e.target.files.length) return;

            const reader = new FileReader();

            reader.onload = function(evt){

                document.getElementById('preview').src = evt.target.result;

            };

            reader.readAsDataURL(e.target.files[0]);

        });

        $('.select2').select2({
            width: '100%'
        });
    </script>
@endpush
