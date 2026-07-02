@php
    $employee = $employee ?? null;
@endphp

<div class="row">

    {{-- PHOTO --}}
    <div class="col-md-3 text-center">

        @if($employee && $employee->photo)
            <img
                id="photo-preview"
                src="{{ asset('storage/'.$employee->photo) }}"
                class="img-thumbnail rounded mb-3"
                style="width:200px;height:200px;object-fit:cover;">
        @else
            <div id="photo-preview"
                 class="border rounded d-flex align-items-center justify-content-center bg-light mb-3"
                 style="width:200px;height:200px;margin:auto;">
                <span class="text-muted">No Photo</span>
            </div>
        @endif

        <div class="form-group">
            <label><strong>Employee Photo</strong></label>
            <input
                type="file"
                name="photo"
                id="photo"
                class="form-control-file"
                accept="image/*">
        </div>

    </div>

    {{-- DETAILS --}}
    <div class="col-md-9">

        <div class="card mb-3">
            <div class="card-header bg-primary text-white">
                Personal Information
            </div>

            <div class="card-body">

                <div class="form-row">

                    <div class="form-group col-md-3">
                        <label>Employee No.</label>
                        <input type="text"
                               name="name"
                               class="form-control"
                               value="{{ old('name', optional($employee)->name) }}">
                    </div>

                    <div class="form-group col-md-3">
                        <label>First Name</label>
                        <input type="text"
                               name="firstname"
                               class="form-control"
                               value="{{ old('firstname', optional($employee)->firstname) }}">
                    </div>

                    <div class="form-group col-md-3">
                        <label>Middle Name</label>
                        <input type="text"
                               name="middlename"
                               class="form-control"
                               value="{{ old('middlename', optional($employee)->middlename) }}">
                    </div>

                    <div class="form-group col-md-3">
                        <label>Last Name</label>
                        <input type="text"
                               name="lastname"
                               class="form-control"
                               value="{{ old('lastname', optional($employee)->lastname) }}">
                    </div>

                </div>

                <div class="form-row">

                    <div class="form-group col-md-6">
                        <label>Email</label>
                        <input type="email"
                               name="email"
                               class="form-control"
                               value="{{ old('email', optional($employee)->email) }}">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Phone</label>
                        <input type="text"
                               name="phone"
                               class="form-control"
                               value="{{ old('phone', optional($employee)->phone) }}">
                    </div>

                </div>

                <div class="form-row">

                    <div class="form-group col-md-4">
                        <label>Birthdate</label>
                        <input type="date"
                               name="birthdate"
                               class="form-control"
                               value="{{ old('birthdate', optional(optional($employee)->birthdate)->format('Y-m-d')) }}">
                    </div>

                    <div class="form-group col-md-4">
                        <label>Civil Status</label>
                        <select name="civil_status" class="form-control">
                            <option value="">Select</option>

                            @foreach(['Single','Married','Widowed','Separated'] as $status)
                                <option value="{{ $status }}"
                                    {{ old('civil_status', optional($employee)->civil_status)==$status?'selected':'' }}>
                                    {{ $status }}
                                </option>
                            @endforeach

                        </select>
                    </div>

                    <div class="form-group col-md-4">
                        <label>Religion</label>
                        <input type="text"
                               name="religion"
                               class="form-control"
                               value="{{ old('religion', optional($employee)->religion) }}">
                    </div>

                </div>

            </div>
        </div>

        {{-- ADDRESS --}}
        <div class="card mb-3">

            <div class="card-header bg-info text-white">
                Address
            </div>

            <div class="card-body">

                <div class="form-group">
                    <label>Address</label>
                    <input type="text"
                           name="address"
                           class="form-control"
                           value="{{ old('address', optional($employee)->address) }}">
                </div>

                <div class="form-row">

                    <div class="form-group col-md-6">
                        <label>City</label>
                        <input type="text"
                               name="city"
                               class="form-control"
                               value="{{ old('city', optional($employee)->city) }}">
                    </div>

                    <div class="form-group col-md-6">
                        <label>Province</label>
                        <input type="text"
                               name="province"
                               class="form-control"
                               value="{{ old('province', optional($employee)->province) }}">
                    </div>

                </div>

            </div>

        </div>

        {{-- EMPLOYMENT --}}
        <div class="card mb-3">

            <div class="card-header bg-success text-white">
                Employment
            </div>

            <div class="card-body">

                <div class="form-row">

                    <div class="form-group col-md-4">
                        <label>Position</label>
                        <input type="text"
                               name="position"
                               class="form-control"
                               value="{{ old('position', optional($employee)->position) }}">
                    </div>

                    <div class="form-group col-md-4">
                        <label>Date Hired</label>
                        <input type="date"
                               name="date_hired"
                               class="form-control"
                               value="{{ old('date_hired', optional(optional($employee)->date_hired)->format('Y-m-d')) }}">
                    </div>

                    <div class="form-group col-md-4">
                        <label>LESP Number</label>
                        <input type="text"
                               name="lesp_num"
                               class="form-control"
                               value="{{ old('lesp_num', optional($employee)->lesp_num) }}">
                    </div>

                </div>

                <div class="form-row">

                    <div class="form-group col-md-6">
                        <label>LESP Issued</label>
                        <input type="text"
                               name="lesp_issued"
                               class="form-control"
                               value="{{ old('lesp_issued', optional($employee)->lesp_issued) }}">
                    </div>

                    <div class="form-group col-md-6">
                        <label>LESP Expiry</label>
                        <input type="date"
                               name="lesp_expiry"
                               class="form-control"
                               value="{{ old('lesp_expiry', optional(optional($employee)->lesp_expiry)->format('Y-m-d')) }}">
                    </div>

                </div>

            </div>

        </div>

        {{-- GOVERNMENT IDS --}}
        <div class="card">

            <div class="card-header bg-secondary text-white">
                Government IDs
            </div>

            <div class="card-body">

                <div class="form-row">

                    <div class="form-group col-md-3">
                        <label>SSS</label>
                        <input type="text"
                               name="sss"
                               class="form-control"
                               value="{{ old('sss', optional($employee)->sss) }}">
                    </div>

                    <div class="form-group col-md-3">
                        <label>TIN</label>
                        <input type="text"
                               name="tin"
                               class="form-control"
                               value="{{ old('tin', optional($employee)->tin) }}">
                    </div>

                    <div class="form-group col-md-3">
                        <label>Pag-IBIG</label>
                        <input type="text"
                               name="pagibig"
                               class="form-control"
                               value="{{ old('pagibig', optional($employee)->pagibig) }}">
                    </div>

                    <div class="form-group col-md-3">
                        <label>PhilHealth</label>
                        <input type="text"
                               name="philhealth"
                               class="form-control"
                               value="{{ old('philhealth', optional($employee)->philhealth) }}">
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>
