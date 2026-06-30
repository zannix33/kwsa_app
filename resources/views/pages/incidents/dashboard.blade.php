@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush


@section('content')

    <div class="container-fluid">

        <div class="d-flex justify-content-between mb-4">

            <h3>

                Incident Dashboard

            </h3>

            <form>

                <select
                    name="year"
                    onchange="this.form.submit()"
                    class="form-control">

                    @for($y=date('Y');$y>=2020;$y--)

                        <option
                            value="{{$y}}"
                            {{$year==$y?'selected':''}}>

                            {{$y}}

                        </option>

                    @endfor

                </select>

            </form>

        </div>

        <div class="row">

            <div class="col-md-2">

                <div class="card border-danger">

                    <div class="card-body text-center">

                        <h2>{{$major}}</h2>

                        Major Incidents

                    </div>

                </div>

            </div>

            <div class="col-md-2">

                <div class="card border-warning">

                    <div class="card-body text-center">

                        <h2>{{$minor}}</h2>

                        Minor Incidents

                    </div>

                </div>

            </div>

            <div class="col-md-2">

                <div class="card border-primary">

                    <div class="card-body text-center">

                        <h2>{{$open}}</h2>

                        Open

                    </div>

                </div>

            </div>

            <div class="col-md-2">

                <div class="card border-info">

                    <div class="card-body text-center">

                        <h2>{{$investigating}}</h2>

                        Investigating

                    </div>

                </div>

            </div>

            <div class="col-md-2">

                <div class="card border-success">

                    <div class="card-body text-center">

                        <h2>{{$resolved}}</h2>

                        Resolved

                    </div>

                </div>

            </div>

            <div class="col-md-2">

                <div class="card border-secondary">

                    <div class="card-body text-center">

                        <h2>{{$closed}}</h2>

                        Closed

                    </div>

                </div>

            </div>

        </div>

        <br>

        <div class="row">

            <div class="col-md-8">

                <div class="card">

                    <div class="card-header">

                        Monthly Incidents

                    </div>

                    <div class="card-body">

                        <canvas id="incidentChart"></canvas>

                    </div>

                </div>

            </div>

            <div class="col-md-4">

                <div class="card">

                    <div class="card-header">

                        Top Employees with Incidents

                    </div>

                    <div class="card-body">

                        <table class="table table-sm">

                            <thead>

                            <tr>

                                <th>Employee</th>

                                <th>Total</th>

                            </tr>

                            </thead>

                            <tbody>

                            @foreach($topEmployees as $row)

                                <tr>

                                    <td>

                                        {{ optional($row->user)->full_name }}

                                    </td>

                                    <td>

                                        {{ $row->total }}

                                    </td>

                                </tr>

                            @endforeach

                            </tbody>

                        </table>

                    </div>

                </div>

            </div>

        </div>

        <br>

        <div class="card">

            <div class="card-header">

                Recent Incidents

            </div>

            <div class="card-body">

                <table class="table table-bordered">

                    <thead>

                    <tr>

                        <th>Date</th>

                        <th>Employee</th>

                        <th>Branch</th>

                        <th>Category</th>

                        <th>Status</th>

                        <th></th>

                    </tr>

                    </thead>

                    <tbody>

                    @foreach($recent as $incident)

                        <tr>

                            <td>

                                {{ $incident->incident_date }}

                            </td>

                            <td>

                                {{ optional($incident->user)->full_name }}

                            </td>

                            <td>

                                {{ optional($incident->branch)->name }}

                            </td>

                            <td>

                                @if($incident->category=="Major")

                                    <span class="badge badge-danger">

Major

</span>

                                @else

                                    <span class="badge badge-warning">

Minor

</span>

                                @endif

                            </td>

                            <td>

                                {{ $incident->status }}

                            </td>

                            <td>

                                <a
                                    href="{{route('incidents.show',$incident)}}"
                                    class="btn btn-sm btn-primary">

                                    View

                                </a>

                            </td>

                        </tr>

                    @endforeach

                    </tbody>

                </table>

            </div>

        </div>

    </div>

@endsection

@section('scripts')

    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <script>

        new Chart(

            document.getElementById('incidentChart'),

            {

                type:'bar',

                data:{

                    labels:[
                        'Jan',
                        'Feb',
                        'Mar',
                        'Apr',
                        'May',
                        'Jun',
                        'Jul',
                        'Aug',
                        'Sep',
                        'Oct',
                        'Nov',
                        'Dec'
                    ],

                    datasets:[{

                        label:'Incidents',

                        data:[

                            {{$monthly[1]??0}},
                            {{$monthly[2]??0}},
                            {{$monthly[3]??0}},
                            {{$monthly[4]??0}},
                            {{$monthly[5]??0}},
                            {{$monthly[6]??0}},
                            {{$monthly[7]??0}},
                            {{$monthly[8]??0}},
                            {{$monthly[9]??0}},
                            {{$monthly[10]??0}},
                            {{$monthly[11]??0}},
                            {{$monthly[12]??0}}

                        ]

                    }]

                }

            }

        );

    </script>

@endsection
