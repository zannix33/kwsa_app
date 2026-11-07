@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')<div class="container-fluid"><h3>Edit Firearm Maintenance</h3><form method="POST" action="{{ route('arms.maintenances.update',$maintenance) }}">@csrf @method('PUT')@include('arms.maintenances._form')<button class="btn btn-primary">Save Maintenance</button> <a class="btn btn-secondary" href="{{ route('arms.maintenances.index') }}">Cancel</a></form></div>@endsection
