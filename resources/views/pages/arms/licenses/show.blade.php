@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush
@section('content')<div class="container-fluid"><h3>License {{ $license->license_number }}</h3><p>Firearm: {{ optional($license->arm)->property_no }}</p><p>Type: {{ $license->license_type }}</p><p>Issue: {{ $license->issue_date }} | Expiry: {{ $license->expiry_date }}</p><p>Status: {{ $license->status }}</p><a class="btn btn-primary" href="{{ route('arms.licenses.edit',$license) }}">Edit</a></div>@endsection
