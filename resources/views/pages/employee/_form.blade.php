
        <!-- PERSONAL INFORMATION -->
            <div class="card mb-4">
                <div class="card-header">
                    <strong>Personal Information</strong>
                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6">

                            <div class="form-group">
                                <label>Employee No.</label>
                                <input type="text"
                                       name="name"
                                       class="form-control"
                                       value="{{ old('name', $employee->name) }}">
                            </div>

                            <div class="form-group">
                                <label>First Name</label>
                                <input type="text"
                                       name="firstname"
                                       class="form-control"
                                       value="{{ old('firstname', $employee->firstname) }}">
                            </div>

                            <div class="form-group">
                                <label>Middle Name</label>
                                <input type="text"
                                       name="middlename"
                                       class="form-control"
                                       value="{{ old('middlename', $employee->middlename) }}">
                            </div>

                            <div class="form-group">
                                <label>Last Name</label>
                                <input type="text"
                                       name="lastname"
                                       class="form-control"
                                       value="{{ old('lastname', $employee->lastname) }}">
                            </div>

                            <div class="form-group">
                                <label>Birthdate</label>
                                <input type="date"
                                       name="birthdate"
                                       class="form-control"
                                       value="{{ old('birthdate', $employee->birthdate ? date('Y-m-d', strtotime($employee->birthdate)) : '') }}">
                            </div>

                            <div class="form-group">
                                <label>Civil Status</label>
                                <select name="civil_status" class="form-control">
                                    <option value="">Select</option>
                                    <option value="Single" {{ old('civil_status', $employee->civil_status) == 'Single' ? 'selected' : '' }}>Single</option>
                                    <option value="Married" {{ old('civil_status', $employee->civil_status) == 'Married' ? 'selected' : '' }}>Married</option>
                                    <option value="Widowed" {{ old('civil_status', $employee->civil_status) == 'Widowed' ? 'selected' : '' }}>Widowed</option>
                                    <option value="Separated" {{ old('civil_status', $employee->civil_status) == 'Separated' ? 'selected' : '' }}>Separated</option>
                                </select>
                            </div>

                            <div class="form-group">
                                <label>Religion</label>
                                <input type="text"
                                       name="religion"
                                       class="form-control"
                                       value="{{ old('religion', $employee->religion) }}">
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>Height</label>
                                    <input type="text"
                                           name="height"
                                           class="form-control"
                                           value="{{ old('height', $employee->height) }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Weight</label>
                                    <input type="text"
                                           name="weight"
                                           class="form-control"
                                           value="{{ old('weight', $employee->weight) }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Blood Type</label>
                                <select name="bloodtype" class="form-control">
                                    <option value="">Select</option>
                                    @foreach(['A+','A-','B+','B-','AB+','AB-','O+','O-'] as $type)
                                        <option value="{{ $type }}"
                                            {{ old('bloodtype', $employee->bloodtype) == $type ? 'selected' : '' }}>
                                            {{ $type }}
                                        </option>
                                    @endforeach
                                </select>
                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group">
                                <label>Email</label>
                                <input type="email"
                                       name="email"
                                       class="form-control"
                                       value="{{ old('email', $employee->email) }}">
                            </div>

                            <div class="form-group">
                                <label>Phone</label>
                                <input type="text"
                                       name="phone"
                                       class="form-control"
                                       value="{{ old('phone', $employee->phone) }}">
                            </div>

                            <div class="form-group">
                                <label>Address</label>
                                <textarea name="address"
                                          rows="3"
                                          class="form-control">{{ old('address', $employee->address) }}</textarea>
                            </div>

                            <div class="form-row">
                                <div class="form-group col-md-6">
                                    <label>City</label>
                                    <input type="text"
                                           name="city"
                                           class="form-control"
                                           value="{{ old('city', $employee->city) }}">
                                </div>

                                <div class="form-group col-md-6">
                                    <label>Province</label>
                                    <input type="text"
                                           name="province"
                                           class="form-control"
                                           value="{{ old('province', $employee->province) }}">
                                </div>
                            </div>

                            <div class="form-group">
                                <label>Spouse Name</label>
                                <input type="text"
                                       name="spouse_name"
                                       class="form-control"
                                       value="{{ old('spouse_name', $employee->spouse_name) }}">
                            </div>

                            <div class="form-group">
                                <label>Spouse Birthdate</label>
                                <input type="date"
                                       name="spouse_birthdate"
                                       class="form-control"
                                       value="{{ old('spouse_birthdate', $employee->spouse_birthdate ? date('Y-m-d', strtotime($employee->spouse_birthdate)) : '') }}">
                            </div>

                            <div class="form-group">
                                <label>Beneficiary Name</label>
                                <input type="text"
                                       name="beneficiary_name"
                                       class="form-control"
                                       value="{{ old('beneficiary_name', $employee->beneficiary_name) }}">
                            </div>

                            <div class="form-group">
                                <label>Beneficiary Contact</label>
                                <input type="text"
                                       name="beneficiary_contact"
                                       class="form-control"
                                       value="{{ old('beneficiary_contact', $employee->beneficiary_contact) }}">
                            </div>

                        </div>

                    </div>

                </div>
            </div>

            <!-- GOVERNMENT IDS -->
            <div class="card mb-4">
                <div class="card-header">
                    <strong>Government IDs & Employment</strong>
                </div>

                <div class="card-body">

                    <div class="row">

                        <div class="col-md-6">

                            <div class="form-group">
                                <label>SSS Number</label>
                                <input type="text"
                                       name="sss"
                                       class="form-control"
                                       value="{{ old('sss', $employee->sss) }}">
                            </div>

                            <div class="form-group">
                                <label>TIN Number</label>
                                <input type="text"
                                       name="tin"
                                       class="form-control"
                                       value="{{ old('tin', $employee->tin) }}">
                            </div>

                            <div class="form-group">
                                <label>Pag-IBIG Number</label>
                                <input type="text"
                                       name="pagibig"
                                       class="form-control"
                                       value="{{ old('pagibig', $employee->pagibig) }}">
                            </div>

                            <div class="form-group">
                                <label>PhilHealth Number</label>
                                <input type="text"
                                       name="philhealth"
                                       class="form-control"
                                       value="{{ old('philhealth', $employee->philhealth) }}">
                            </div>

                        </div>

                        <div class="col-md-6">

                            <div class="form-group">
                                <label>Position</label>
                                <input type="text"
                                       name="position"
                                       class="form-control"
                                       value="{{ old('position', $employee->position) }}">
                            </div>

                            <div class="form-group">
                                <label>Date Hired</label>
                                <input type="date"
                                       name="date_hired"
                                       class="form-control"
                                       value="{{ old('date_hired', $employee->date_hired ? date('Y-m-d', strtotime($employee->date_hired)) : '') }}">
                            </div>

                            <div class="form-group">
                                <label>LESP Number</label>
                                <input type="text"
                                       name="lesp_num"
                                       class="form-control"
                                       value="{{ old('lesp_num', $employee->lesp_num) }}">
                            </div>

                            <div class="form-group">
                                <label>LESP Issued</label>
                                <input type="text"
                                       name="lesp_issued"
                                       class="form-control"
                                       value="{{ old('lesp_issued', $employee->lesp_issued) }}">
                            </div>

                            <div class="form-group">
                                <label>LESP Expiry</label>
                                <input type="date"
                                       name="lesp_expiry"
                                       class="form-control"
                                       value="{{ old('lesp_expiry', $employee->lesp_expiry ? date('Y-m-d', strtotime($employee->lesp_expiry)) : '') }}">
                            </div>

                            <div class="form-group">
                                <label>DT Date</label>
                                <input type="date"
                                       name="dt_date"
                                       class="form-control"
                                       value="{{ old('dt_date', $employee->dt_date ? date('Y-m-d', strtotime($employee->dt_date)) : '') }}">
                            </div>

                        </div>

                    </div>

                </div>
            </div>

            <!-- ACCOUNT -->
            <div class="card mb-4">
                <div class="card-header">
                    <strong>Account Information</strong>
                </div>

                <div class="card-body">

                    <div class="form-group">
                        <label>New Password</label>
                        <input type="password"
                               name="password"
                               class="form-control">

                        <small class="form-text text-muted">
                            Leave blank if you do not want to change the password.
                        </small>
                    </div>

                </div>
            </div>



