@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')
    <div class="container-fluid">

        <div class="row">

            <!-- LEFT PROFILE -->
            <div class="col-lg-4">

                <div class="card shadow-sm">

                    <div class="card-body text-center">

                        @if($employee->photo)
                            <img src="{{ asset('storage/'.$employee->photo) }}"
                                 class="rounded-circle img-thumbnail"
                                 style="width:220px;height:220px;object-fit:cover;">
                        @else
                            <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center mx-auto"
                                 style="width:220px;height:220px;font-size:72px;font-weight:bold;">
                                {{ strtoupper(substr($employee->firstname,0,1).substr($employee->lastname,0,1)) }}
                            </div>
                        @endif

                        <h3 class="mb-0">
                            {{ $employee->firstname }}
                            {{ $employee->lastname }}
                        </h3>



                        <p class="text-muted">
                            {{ @$employee->position->name }}
                        </p>

                        <hr>

                        <table class="table table-borderless table-sm">

                            <tr>
                                <th width="45%">Employee No.</th>
                                <td>{{ $employee->name }}</td>
                            </tr>
                            <tr>
                                <td>Department</td>
                                <td>{{ $employee->department_type }}</td>
                            </tr>

                            <tr>
                                <th>Age</th>
                                <td>{{ $employee->birthdate?->age }} years old</td>
                            </tr>

                            <tr>
                                <th>Civil Status</th>
                                <td>{{ $employee->civil_status }}</td>
                            </tr>

                            <tr>
                                <th>Religion</th>
                                <td>{{ $employee->religion }}</td>
                            </tr>

                            <tr>
                                <th>Blood Type</th>
                                <td>{{ $employee->bloodtype }}</td>
                            </tr>

                            <tr>
                                <th>Date Hired</th>
                                <td>{{ $employee->date_hired?->format('F d, Y') }}</td>
                            </tr>


                        </table>

                        <a href="{{ route('hr.employee.edit',$employee) }}"
                           class="btn btn-primary btn-block">

                            <i class="fa fa-edit"></i>
                            Edit Employee

                        </a>

                        <a href="{{ route('hr.employee.index') }}"
                           class="btn btn-secondary btn-block">

                            Back

                        </a>

                    </div>

                </div>

            </div>

            <!-- RIGHT DETAILS -->
            <div class="col-lg-8">

                <!-- PERSONAL -->
                <div class="card shadow-sm mb-4">

                    <div class="card-header bg-primary text-white">

                        <strong>Personal Information</strong>

                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-6">

                                <table class="table table-bordered">

                                    <tr>
                                        <th>First Name</th>
                                        <td>{{ $employee->firstname }}</td>
                                    </tr>

                                    <tr>
                                        <th>Middle Name</th>
                                        <td>{{ $employee->middlename }}</td>
                                    </tr>

                                    <tr>
                                        <th>Last Name</th>
                                        <td>{{ $employee->lastname }}</td>
                                    </tr>

                                    <tr>
                                        <th>Birthdate</th>
                                        <td>{{ $employee->birthdate?->format('F d, Y') }}</td>
                                    </tr>

                                    <tr>
                                        <th>Height</th>
                                        <td>{{ $employee->height }}</td>
                                    </tr>

                                    <tr>
                                        <th>Weight</th>
                                        <td>{{ $employee->weight }}</td>
                                    </tr>

                                </table>

                            </div>

                            <div class="col-md-6">

                                <table class="table table-bordered">

                                    <tr>
                                        <th>Email</th>
                                        <td>{{ $employee->email }}</td>
                                    </tr>

                                    <tr>
                                        <th>Phone</th>
                                        <td>{{ $employee->phone }}</td>
                                    </tr>

                                    <tr>
                                        <th>Address</th>
                                        <td>{{ $employee->address }}</td>
                                    </tr>

                                    <tr>
                                        <th>City</th>
                                        <td>{{ $employee->city }}</td>
                                    </tr>

                                    <tr>
                                        <th>Province</th>
                                        <td>{{ $employee->province }}</td>
                                    </tr>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- FAMILY -->
                <div class="card shadow-sm mb-4">

                    <div class="card-header bg-success text-white">

                        <strong>Family Information</strong>

                    </div>

                    <div class="card-body">

                        <table class="table table-bordered">

                            <tr>
                                <th width="25%">Spouse</th>
                                <td>{{ $employee->spouse_name }}</td>
                            </tr>

                            <tr>
                                <th>Spouse Birthdate</th>
                                <td>{{ optional($employee->spouse_birthdate)->format('F d, Y') }}</td>
                            </tr>

                            <tr>
                                <th>Beneficiary</th>
                                <td>{{ $employee->beneficiary_name }}</td>
                            </tr>

                            <tr>
                                <th>Beneficiary Contact</th>
                                <td>{{ $employee->beneficiary_contact }}</td>
                            </tr>

                        </table>

                    </div>

                </div>

                <!-- GOVERNMENT IDS -->
                <div class="card shadow-sm mb-4">

                    <div class="card-header bg-info text-white">

                        <strong>Government IDs</strong>

                    </div>

                    <div class="card-body">

                        <div class="row">

                            <div class="col-md-6">

                                <table class="table table-bordered">

                                    <tr>
                                        <th>SSS</th>
                                        <td>{{ $employee->sss }}</td>
                                    </tr>

                                    <tr>
                                        <th>TIN</th>
                                        <td>{{ $employee->tin }}</td>
                                    </tr>

                                </table>

                            </div>

                            <div class="col-md-6">

                                <table class="table table-bordered">

                                    <tr>
                                        <th>Pag-IBIG</th>
                                        <td>{{ $employee->pagibig }}</td>
                                    </tr>

                                    <tr>
                                        <th>PhilHealth</th>
                                        <td>{{ $employee->philhealth }}</td>
                                    </tr>

                                </table>

                            </div>

                        </div>

                    </div>

                </div>

                <!-- EMPLOYMENT -->
                <div class="card shadow-sm">

                    <div class="card-header bg-dark text-white">

                        <strong>Employment Information</strong>

                    </div>

                    <div class="card-body">

                        <table class="table table-bordered">

                            <tr>
                                <th>LESP Number</th>
                                <td>{{ $employee->lesp_num }}</td>
                            </tr>

                            <tr>
                                <th>LESP Issued</th>
                                <td>{{ $employee->lesp_issued }}</td>
                            </tr>

                            <tr>
                                <th>LESP Expiry</th>

                                <td>
                                    {{ $employee->lesp_expiry?->format('F d, Y') }}

                                    @if($employee->lesp_expiry && $employee->lesp_expiry->isPast())
                                        <span class="badge badge-danger ml-2">
                                        Expired
                                    </span>
                                    @elseif($employee->lesp_expiry && $employee->lesp_expiry->diffInDays(now()) <= 90)
                                        <span class="badge badge-warning ml-2">
                                        Expiring Soon
                                    </span>
                                    @endif
                                </td>

                            </tr>

                            <tr>
                                <th>Date Hired</th>
                                <td>{{ $employee->date_hired?->format('F d, Y') }}</td>
                            </tr>

                            <tr>
                                <th>Years of Service</th>
                                <td>{{ $employee->date_hired?->diffInYears(now()) }} Years</td>
                            </tr>

                        </table>

                    </div>

                </div>

            </div>

        </div>

    </div>
@endsection
