@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')

    <div class="card">
        <div class="card-header">
            Payslip

            <a
                href="{{ route('payrolls.pdf', $payroll) }}"
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

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-right">Hours</th>
                        <th class="text-right">Rate</th>
                        <th class="text-right">Amount</th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse(
                        $payroll->items->where('type', 'earning')
                        as $item
                    )
                        <tr>
                            <td>
                                {{ $item->description }}
                            </td>

                            <td class="text-right">
                                @if(
                                    in_array($item->code, [
                                        'BASIC',
                                        'OT',
                                        'SOT',
                                        'ND',
                                        'REGHOLIDAY',
                                        'SPHOLIDAY',
                                        'HOLIDAY'
                                    ])
                                )
                                    {{ number_format((float) $item->quantity, 2) }}
                                @else
                                    —
                                @endif
                            </td>

                            <td class="text-right">
                                @if(
                                    in_array($item->code, [
                                        'BASIC',
                                        'OT',
                                        'SOT',
                                        'ND',
                                        'REGHOLIDAY',
                                        'SPHOLIDAY',
                                        'HOLIDAY'
                                    ])
                                    && (float) $item->rate > 0
                                )
                                    {{ number_format((float) $item->rate, 2) }}
                                @else
                                    —
                                @endif
                            </td>

                            <td class="text-right">
                                {{ number_format((float) $item->amount, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="4" class="text-center">
                                No earnings found.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <h5>Deductions</h5>

            <div class="table-responsive">
                <table class="table table-bordered">
                    <thead>
                    <tr>
                        <th>Description</th>
                        <th class="text-right" width="150">
                            Amount
                        </th>
                    </tr>
                    </thead>

                    <tbody>
                    @forelse(
                        $payroll->items->where('type', 'deduction')
                        as $item
                    )
                        <tr>
                            <td>
                                {{ $item->description }}
                            </td>

                            <td class="text-right">
                                {{ number_format((float) $item->amount, 2) }}
                            </td>
                        </tr>
                    @empty
                        <tr>
                            <td colspan="2" class="text-center">
                                No deductions found.
                            </td>
                        </tr>
                    @endforelse
                    </tbody>
                </table>
            </div>

            <div class="row">
                <div class="col-md-4 offset-md-8">
                    <table class="table table-bordered">
                        <tr>
                            <th>Gross Pay</th>
                            <td class="text-right">
                                {{ number_format((float) $payroll->gross_pay, 2) }}
                            </td>
                        </tr>

                        <tr>
                            <th>Deductions</th>
                            <td class="text-right">
                                {{ number_format((float) $payroll->total_deductions, 2) }}
                            </td>
                        </tr>

                        <tr>
                            <th>Net Pay</th>
                            <td class="text-right">
                                <strong>
                                    {{ number_format((float) $payroll->net_pay, 2) }}
                                </strong>
                            </td>
                        </tr>
                    </table>
                </div>
            </div>

        </div>
    </div>

@endsection
