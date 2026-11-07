@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')<div class="container-fluid"><h3>Create Firearm Maintenance</h3><form method="POST" action="{{ route('arms.maintenances.store') }}">@csrf @include('arms.maintenances._form')<button class="btn btn-primary">Save Maintenance</button> <a class="btn btn-secondary" href="{{ route('arms.maintenances.index') }}">Cancel</a></form></div>@endsection
