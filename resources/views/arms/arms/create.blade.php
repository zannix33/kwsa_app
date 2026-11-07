@extends('layout.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Register Firearm</h4>
            <a href="{{ route('arms.index') }}" class="btn btn-secondary btn-sm">Back to Inventory</a>
        </div>
        <div class="card-body">
            <form action="{{ route('arms.store') }}" method="POST" enctype="multipart/form-data">
                @include('arms.arms._form')
            </form>
        </div>
    </div>
</div>
@endsection
