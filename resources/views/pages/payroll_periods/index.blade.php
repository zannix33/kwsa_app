@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')
    <div class="card">
        <div class="card-header">

            <a href="{{ route('payroll-periods.create') }}"
               class="btn btn-primary btn-sm">
                Create Payroll Period
            </a>

        </div>

        <div class="card-body">

            <table
                id="datatable"
                class="table table-bordered">

                <thead>
                <tr>
                    <th>Name</th>
                    <th>Date From</th>
                    <th>Date To</th>
                    <th>Status</th>
                    <th>Payrolls</th>
                    <th>Total Net Pay</th>
                    <th width="150">
                        Action
                    </th>
                </tr>
                </thead>

                <tbody>

                @foreach($periods as $period)

                    <tr>

                        <td>
                            {{ $period->name }}
                        </td>

                        <td>
                            {{ $period->date_from->format('M d, Y') }}
                        </td>

                        <td>
                            {{ $period->date_to->format('M d, Y') }}
                        </td>

                        <td>
                            {{ $period->status }}
                        </td>

                        <td>
                            {{ $period->employee_count }}
                        </td>

                        <td>
                            {{ number_format(
                                $period->total_net_pay,
                                2
                            ) }}
                        </td>

                        <td>
                            @if($period->status == 'Open')

                                <form
                                    action="{{ route('payroll-generation.generate',$period) }}"
                                    method="POST"
                                    style="display:inline">

                                    @csrf

                                    <button
                                        class="btn btn-success btn-sm"
                                        onclick="return confirm('Generate payroll?')">

                                        Generate Payroll

                                    </button>

                                </form>

                            @endif

                            <a href="{{ route(
                            'payroll-periods.edit',
                            $period
                        ) }}"
                               class="btn btn-warning btn-sm">
                                Edit
                            </a>

                            <form
                                action="{{ route(
                                'payroll-periods.destroy',
                                $period
                            ) }}"
                                method="POST"
                                style="display:inline">

                                @csrf
                                @method('DELETE')

                                <button
                                    class="btn btn-danger btn-sm"
                                    onclick="
                                    return confirm(
                                    'Delete?'
                                    )
                                ">
                                    Delete
                                </button>

                            </form>

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>
    </div>
@endsection

@push('custom-scripts')
    <script>
        $(function(){
            $('#datatable').DataTable();
        });
    </script>
@endpush
