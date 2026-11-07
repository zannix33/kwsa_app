@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush
@section('content')<div class="container-fluid"><h3>Create Firearm License</h3><form method="POST" action="{{ route('arms.licenses.store') }}">@csrf @include('arms.licenses._form')<button class="btn btn-primary">Save License</button></form></div>@endsection
