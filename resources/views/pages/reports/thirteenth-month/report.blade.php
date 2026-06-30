@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')

    <div class="card">

        <div class="card-header">

            <h4>13th Month Pay Report</h4>

        </div>

        <div class="card-body table-responsive">

            <table class="table table-bordered table-striped">

                <thead>

                <tr>

                    <th>Employee</th>

                    <th>Branch</th>

                    <th>Total Hours</th>

                    <th>Days Worked</th>

                    <th>Cash Bond</th>

                    <th>13th Month</th>

                    <th>Total</th>

                </tr>

                </thead>

                <tbody>

                @php

                    $totalHours=0;
                    $totalDays=0;
                    $totalCashBond=0;
                    $total13=0;
                    $grand=0;

                @endphp

                @foreach($report as $row)

                    @php

                        $totalHours += $row['hours'];
                        $totalDays += $row['days'];
                        $totalCashBond += $row['cash_bond'];
                        $total13 += $row['thirteenth'];
                        $grand += $row['total'];

                    @endphp

                    <tr>

                        <td>{{ $row['employee']->firstname }} {{ $row['employee']->lastname }}</td>

                        <td>{{ $row['branch'] }}</td>

                        <td>{{ number_format($row['hours'],2) }}</td>

                        <td>{{ number_format($row['days'],2) }}</td>

                        <td>{{ number_format($row['cash_bond'],2) }}</td>

                        <td>{{ number_format($row['thirteenth'],2) }}</td>

                        <td>{{ number_format($row['total'],2) }}</td>

                    </tr>

                @endforeach

                </tbody>

                <tfoot>

                <tr class="font-weight-bold bg-light">

                    <td colspan="2">
                        TOTAL
                    </td>

                    <td>{{ number_format($totalHours,2) }}</td>

                    <td>{{ number_format($totalDays,2) }}</td>

                    <td>{{ number_format($totalCashBond,2) }}</td>

                    <td>{{ number_format($total13,2) }}</td>

                    <td>{{ number_format($grand,2) }}</td>

                </tr>

                </tfoot>

            </table>

        </div>

    </div>

@endsection
