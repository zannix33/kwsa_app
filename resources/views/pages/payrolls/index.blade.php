@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')

    <div class="card">

        <div class="card-header">
            Payroll List
        </div>

        <div class="card-body">

            <table class="table table-bordered">

                <thead>

                <tr>
                    <th>Employee</th>
                    <th>Period</th>
                    <th>Gross Pay</th>
                    <th>Deductions</th>
                    <th>Net Pay</th>
                    <th>Status</th>
                    <th width="220">
                        Action
                    </th>
                </tr>

                </thead>

                <tbody>

                @foreach($payrolls as $payroll)

                    <tr>

                        <td>
                            {{ $payroll->user->full_name }}
                        </td>

                        <td>
                            {{ $payroll->period->name }}
                        </td>

                        <td>
                            {{ number_format(
                                $payroll->gross_pay,
                                2
                            ) }}
                        </td>

                        <td>
                            {{ number_format(
                                $payroll->total_deductions,
                                2
                            ) }}
                        </td>

                        <td>
                            {{ number_format(
                                $payroll->net_pay,
                                2
                            ) }}
                        </td>

                        <td>

                            @if(
                                $payroll->status == 'Paid'
                            )

                                <span
                                    class="badge badge-success">

                                Paid

                            </span>

                            @else

                                <span
                                    class="badge badge-warning">

                                Processed

                            </span>

                            @endif

                        </td>

                        <td>

                            <a
                                href="{{ route(
                                'payrolls.show',
                                $payroll
                            ) }}"
                                class="btn btn-info btn-sm">

                                View

                            </a>

                            @if(
                                $payroll->status != 'Paid'
                            )

                                <form
                                    action="{{ route(
                                'payrolls.mark-paid',
                                $payroll
                            ) }}"
                                    method="POST"
                                    style="display:inline">

                                    @csrf

                                    <button
                                        class="btn btn-success btn-sm">

                                        Mark Paid

                                    </button>

                                </form>

                            @endif

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

            {{ $payrolls->links() }}

        </div>

    </div>

@endsection
