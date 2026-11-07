@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush
@section('content')<div class="container-fluid"><h3>Edit Firearm License</h3><form method="POST" action="{{ route('arms.licenses.update',$license) }}">@csrf @method('PUT')@include('arms.licenses._form')<button class="btn btn-primary">Save License</button></form></div>@endsection
