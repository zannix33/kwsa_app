@php
    $employee = $employee ?? null;
@endphp

@if ($errors->any())
    <div class="alert alert-danger">
        <h5>
            <i class="fa fa-exclamation-triangle"></i>
            Please correct the following errors:
        </h5>

        <ul class="mb-0">
            @foreach ($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

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
                        <input
                            type="text"
                            name="employee_no"
                            class="form-control"
                            value="{{ old('employee_no',$employee->employee_no ?? '') }}">
                    </div>

                    <div class="form-group">
                        <label>Full Name</label>
                        <input
                            type="text"
                            id="fullname"
                            class="form-control"
                            readonly
                            value="{{ old('name',$employee->name ?? '') }}">
                    </div>

                    <div class="form-group col-md-3">
                        <label>First Name</label>
                        <input type="text"
                               id="firstname"
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
                               id="lastname"
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
                        <label for="department_type">Department Type</label>
                        <select name="department_type" id="department_type" class="form-control @error('department_type') is-invalid @enderror">
                            <option value="">-- Select Department --</option>
                            <option value="Admin"
                                {{ old('department_type', $employee->department_type ?? 'Operations') == 'Admin' ? 'selected' : '' }}>
                                Admin
                            </option>
                            <option value="Operations"
                                {{ old('department_type', $employee->department_type ?? 'Operations') == 'Operations' ? 'selected' : '' }}>
                                Operations
                            </option>
                        </select>

                        @error('department_type')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-md-4">
                        <label>Position</label>

                        <select name="position_id"
                                class="form-control @error('position_id') is-invalid @enderror"
                                required>

                            <option value="">-- Select Position --</option>

                            @foreach($positions as $position)
                                <option value="{{ $position->id }}"
                                    {{ old('position_id', $employee->position_id ?? '') == $position->id ? 'selected' : '' }}>
                                    {{ $position->name }}
                                </option>
                            @endforeach

                        </select>

                        @error('position_id')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-md-4">
                        <label>Date Hired</label>
                        <input type="date"
                               name="date_hired"
                               class="form-control"
                               value="{{ old('date_hired', optional(optional($employee)->date_hired)->format('Y-m-d')) }}">
                    </div>

                </div>

                <div class="form-row" id="lesp-section">

                    <div class="form-group col-md-3">
                        <label>LESP Number</label>
                        <input type="text"
                               name="lesp_num"
                               class="form-control"
                               value="{{ old('lesp_num', $employee->lesp_num ?? '') }}">
                    </div>

                    <div class="form-group col-md-3">
                        <label>LESP Category</label>

                        <select name="lesp_category" class="form-control">

                            <option value="">Select Category</option>

                            @foreach(['SO','SG','SM','BAG'] as $category)

                                <option value="{{ $category }}"
                                    {{ old('lesp_category', $employee->lesp_category ?? '') == $category ? 'selected' : '' }}>
                                    {{ $category }}
                                </option>

                            @endforeach

                        </select>

                    </div>

                    <div class="form-group col-md-3">
                        <label>LESP Issued</label>
                        <input type="date"
                               name="lesp_issued"
                               class="form-control"
                               value="{{ old('lesp_issued', optional($employee)->lesp_issued) }}">
                    </div>

                    <div class="form-group col-md-3">
                        <label>LESP Expiry</label>

                        <input type="date"
                               name="lesp_expiry"
                               class="form-control"
                               value="{{ old('lesp_expiry', optional($employee)->lesp_expiry) }}">
                    </div>

                </div>

            </div>

        </div>

        <!-- Family Information -->
        <div class="card mb-3">
            <div class="card-header bg-warning text-dark">
                <strong>Family Information</strong>
            </div>

            <div class="card-body">

                <div class="form-row">

                    <div class="form-group col-md-6">
                        <label>Spouse Name</label>
                        <input
                            type="text"
                            name="spouse_name"
                            class="form-control @error('spouse_name') is-invalid @enderror"
                            value="{{ old('spouse_name', $employee->spouse_name ?? '') }}"
                            placeholder="Enter spouse's full name">

                        @error('spouse_name')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                    <div class="form-group col-md-6">
                        <label>Spouse Birthdate</label>
                        <input
                            type="date"
                            name="spouse_birthdate"
                            class="form-control @error('spouse_birthdate') is-invalid @enderror"
                            value="{{ old('spouse_birthdate', optional($employee?->spouse_birthdate)->format('Y-m-d')) }}">

                        @error('spouse_birthdate')
                        <div class="invalid-feedback">{{ $message }}</div>
                        @enderror
                    </div>

                </div>

            </div>
        </div>

        <!-- Beneficiary Information -->
        <div class="card mb-3">

            <div class="card-header bg-info text-white">

                <strong>Beneficiary Information</strong>

            </div>

            <div class="card-body">

                <div class="form-row">

                    <!-- Beneficiary Name -->
                    <div class="form-group col-md-4">

                        <label>Beneficiary Name</label>

                        <input
                            type="text"
                            name="beneficiary_name"
                            class="form-control @error('beneficiary_name') is-invalid @enderror"
                            value="{{ old('beneficiary_name',$employee?->beneficiary_name ?? '') }}">

                        @error('beneficiary_name')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>

                    <!-- Relationship -->
                    <div class="form-group col-md-4">

                        <label>Relationship</label>

                        <select
                            name="beneficiary_relationship"
                            class="form-control @error('beneficiary_relationship') is-invalid @enderror">

                            <option value="">Select Relationship</option>

                            @foreach([
                                'Spouse',
                                'Mother',
                                'Father',
                                'Son',
                                'Daughter',
                                'Brother',
                                'Sister',
                                'Grandparent',
                                'Grandchild',
                                'Uncle',
                                'Aunt',
                                'Nephew',
                                'Niece',
                                'Cousin',
                                'Guardian',
                                'Friend',
                                'Other'
                            ] as $relationship)

                                <option
                                    value="{{ $relationship }}"
                                    {{ old('beneficiary_relationship',$employee->beneficiary_relationship ?? '') == $relationship ? 'selected' : '' }}>

                                    {{ $relationship }}

                                </option>

                            @endforeach

                        </select>

                        @error('beneficiary_relationship')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>

                    <!-- Contact -->
                    <div class="form-group col-md-4">

                        <label>Contact Number</label>

                        <input
                            type="text"
                            name="beneficiary_contact"
                            class="form-control @error('beneficiary_contact') is-invalid @enderror"
                            value="{{ old('beneficiary_contact',$employee->beneficiary_contact ?? '') }}"
                            placeholder="09XXXXXXXXX">

                        @error('beneficiary_contact')
                        <div class="invalid-feedback">
                            {{ $message }}
                        </div>
                        @enderror

                    </div>

                </div>

            </div>

        </div>

        {{-- GOVERNMENT IDS --}}
        <div class="card">

            <div class="card-header bg-secondary text-white">
                Government IDs / Account Information
            </div>

            <div class="card-body">

                <div class="form-row">

                    <div class="form-group col-md-3">
                        <label>SSS</label>
                        <input type="text"
                               name="sss"
                               class="form-control"
                               value="{{ old('sss', $employee->sss ?? '') }}">
                    </div>

                    <div class="form-group col-md-3">
                        <label>TIN</label>
                        <input type="text"
                               name="tin"
                               class="form-control"
                               value="{{ old('tin', $employee->tin ?? '') }}">
                    </div>

                    <div class="form-group col-md-3">
                        <label>Pag-IBIG</label>
                        <input type="text"
                               name="pagibig"
                               class="form-control"
                               value="{{ old('pagibig', $employee->pagibig ?? '') }}">
                    </div>

                    <div class="form-group col-md-3">
                        <label>PhilHealth</label>
                        <input type="text"
                               name="philhealth"
                               class="form-control"
                               value="{{ old('philhealth', $employee->philhealth ?? '') }}">
                    </div>

                </div>

                <div class="form-row">

                    <div class="form-group col-md-6">
                        <label>Micro Savings Account Number</label>

                        <input type="text"
                               name="micro_savings_account_no"
                               class="form-control"
                               value="{{ old('micro_savings_account_no', $employee->micro_savings_account_no ?? '') }}">
                    </div>

                </div>

            </div>

        </div>

    </div>

</div>

@push('custom-scripts')
    <script>

        function updateName(){

            let first = document.getElementById('firstname').value;

            let last = document.getElementById('lastname').value;

            document.getElementById('fullname').value =
                (first + ' ' + last).trim();

        }

        document.getElementById('firstname').addEventListener('keyup',updateName);

        document.getElementById('lastname').addEventListener('keyup',updateName);

        updateName();


        function toggleLesp() {

            let type = document.getElementById('department_type').value;

            let lesp = document.getElementById('lesp-section');

            if (type === 'Admin') {

            lesp.style.display = 'none';

        } else {

            lesp.style.display = '';

        }
        }

            document.getElementById('department_type')
            .addEventListener('change', toggleLesp);

            toggleLesp();


    </script>
@endpush
