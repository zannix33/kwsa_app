@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')

    <div class="card">

        <div class="card-header">

            Payslip
            <a
                href="{{ route('payrolls.pdf',$payroll) }}"
                class="btn btn-danger mb-3">

                Download PDF

            </a>


        </div>



        <div class="card-body">

            <div class="row">

                <div class="col-md-6">

                    <strong>Employee:</strong>

                    {{ $payroll->user->full_name }}

                </div>

                <div class="col-md-6">

                    <strong>Payroll Period:</strong>

                    {{ $payroll->period->name }}

                </div>

            </div>

            <hr>

            <h5>Earnings</h5>

            <table
                class="table table-bordered">

                <thead>

                <tr>

                    <th>Description</th>

                    <th width="150">
                        Amount
                    </th>

                </tr>

                </thead>

                <tbody>

                @foreach(
                    $payroll->items
                    ->where('type','earning')
                    as $item
                )

                    <tr>

                        <td>
                            {{ $item->description }}
                        </td>

                        <td>

                            {{ number_format(
                                $item->amount,
                                2
                            ) }}

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

            <h5>Deductions</h5>

            <table
                class="table table-bordered">

                <thead>

                <tr>

                    <th>Description</th>

                    <th width="150">
                        Amount
                    </th>

                </tr>

                </thead>

                <tbody>

                @foreach(
                    $payroll->items
                    ->where(
                        'type',
                        'deduction'
                    )
                    as $item
                )

                    <tr>

                        <td>
                            {{ $item->description }}
                        </td>

                        <td>

                            {{ number_format(
                                $item->amount,
                                2
                            ) }}

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

            <div class="row">

                <div class="col-md-4 offset-md-8">

                    <table
                        class="table table-bordered">

                        <tr>

                            <th>
                                Gross Pay
                            </th>

                            <td>

                                {{ number_format(
                                    $payroll->gross_pay,
                                    2
                                ) }}

                            </td>

                        </tr>

                        <tr>

                            <th>
                                Deductions
                            </th>

                            <td>

                                {{ number_format(
                                    $payroll->total_deductions,
                                    2
                                ) }}

                            </td>

                        </tr>

                        <tr>

                            <th>
                                Net Pay
                            </th>

                            <td>

                                <strong>

                                    {{ number_format(
                                        $payroll->net_pay,
                                        2
                                    ) }}

                                </strong>

                            </td>

                        </tr>

                    </table>

                </div>

            </div>

        </div>

    </div>

@endsection
