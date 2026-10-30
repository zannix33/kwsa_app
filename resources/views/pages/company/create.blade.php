@php($company = new \App\Models\Company())
@extends('layout.master')
@section('content')
<div class="container"><div class="row justify-content-center"><div class="col-md-8"><div class="card"><div class="card-header">{{ $company->exists ? 'Edit Company' : 'Add Company' }} — {{ $client->name }}</div><div class="card-body">
@if($errors->any())<div class="alert alert-danger"><ul class="mb-0">@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
<form method="POST" action="{{ $company->exists ? route('clients.companies.update',[$client,$company]) : route('clients.companies.store',$client) }}">@csrf @if($company->exists) @method('PUT') @endif
<div class="form-group"><label>Company Name</label><input name="name" class="form-control" value="{{ old('name',$company->name) }}" required maxlength="255"></div>
<div class="form-group"><label>Status</label><select name="active" class="form-control"><option value="1" {{ old('active',$company->active ?? 1)==1?'selected':'' }}>Active</option><option value="0" {{ old('active',$company->active ?? 1)==0?'selected':'' }}>Inactive</option></select></div>
<div class="text-right"><a href="{{ route('clients.show',$client) }}" class="btn btn-secondary">Cancel</a> <button class="btn btn-primary">Save Company</button></div></form></div></div></div></div></div>
@endsection
