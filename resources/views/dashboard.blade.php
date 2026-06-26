@extends('layout.master')

@push('plugin-styles')
  <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')
<div class="row">
  <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 grid-margin stretch-card">
      <div class="card">
          <div class="card-body">
              <h4 class="card-title">Expired License</h4>
              <div class="table-responsive">
                  <table class="table">
                      <thead>
                      <tr>
                          <th>Name</th>
                          <th>LESP #</th>
                          <th>LESP Expiration</th>
                      </tr>
                      </thead>
                      <tbody>
                      @foreach($lesp_expiry as $lesp)
                          <tr>
                              <td>{{ $lesp->firstname }} {{ $lesp->lastname }}</td>
                              <td>{{ $lesp->lesp }}</td>
                              <td>{{ $lesp->lesp_expiry }}</td>
                          </tr>
                      @endforeach
                      </tbody>
                  </table>
              </div>
          </div>
      </div>
  </div>

    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Tests</h4>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>LESP Expiration</th>
                            <th>Drug Test</th>
                            <th>Neuro</th>
                            <th>Neuro</th>
                        </tr>
                        </thead>
                        <tbody>

                        <tr>
                            <td>Jacob</td>
                            <td>53275531</td>
                            <td>12 May 2017</td>
                            <td>
                            </td>
                        </tr>

                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <div class="col-xl-4 col-lg-4 col-md-4 col-sm-6 grid-margin stretch-card">
        <div class="card">
            <div class="card-body">
                <h4 class="card-title">Close to Over Age Guards</h4>
                <div class="table-responsive">
                    <table class="table">
                        <thead>
                        <tr>
                            <th>Name</th>
                            <th>LESP</th>
                            <th>Birthdate</th>
                            <th>Age</th>
                        </tr>
                        </thead>
                        <tbody>
                        @foreach($emp_age as $age)
                            <tr>
                                <td>{{ $age->firstname }} {{ $age->lastname }} </td>
                                <td>{{ $age->lesp_num }} </td>
                                <td>{{ $age->birthdate->toDateString() }} </td>
                                <td>{{ $age->birthdate->age }} </td>
                            </tr>
                        @endforeach
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>





@endsection

@push('plugin-scripts')
  {!! Html::script('/assets/plugins/chartjs/chart.min.js') !!}
  {!! Html::script('/assets/plugins/jquery-sparkline/jquery.sparkline.min.js') !!}
@endpush

@push('custom-scripts')
  {!! Html::script('/assets/js/dashboard.js') !!}
@endpush
