@extends('layout.master')
@section('content')
<div class="container-fluid">
 <div class="row mb-3"><div class="col-md-6"><h3>Clients</h3></div><div class="col-md-6 text-right"><a href="{{ route('clients.create') }}" class="btn btn-primary"><i class="fa fa-plus"></i> Create Client</a></div></div>
 @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
 @if(session('error'))<div class="alert alert-danger">{{ session('error') }}</div>@endif
 <div class="card shadow"><div class="card-header">Client Groups</div><div class="card-body p-0"><table class="table table-bordered table-hover mb-0"><thead class="thead-light"><tr><th width="80">ID</th><th>Client Name</th><th>Category</th><th>Age Limit</th><th>Status</th><th>Companies</th><th width="180">Actions</th></tr></thead><tbody>
 @forelse($clients as $client)<tr><td>{{ $client->id }}</td><td>{{ $client->name }}</td><td>{{ ucfirst($client->category) }}</td><td>{{ $client->age_limit ?? 'N/A' }}</td><td>@if($client->active)<span class="badge badge-success">Active</span>@else<span class="badge badge-danger">Inactive</span>@endif</td><td>{{ $client->companies_count ?? $client->companies()->count() }}</td><td><a href="{{ route('clients.show',$client) }}" class="btn btn-sm btn-info">View</a> <a href="{{ route('clients.edit',$client) }}" class="btn btn-sm btn-warning">Edit</a></td></tr>@empty<tr><td colspan="7" class="text-center">No clients found.</td></tr>@endforelse
 </tbody></table></div><div class="card-footer">{{ $clients->links() }}</div></div>
</div>
@endsection
