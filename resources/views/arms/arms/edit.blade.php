@extends('layout.master')

@section('content')
<div class="container-fluid">
    <div class="card shadow">
        <div class="card-header d-flex justify-content-between align-items-center">
            <h4 class="mb-0">Edit Firearm</h4>
            <a href="{{ route('arms.show', $arm) }}" class="btn btn-secondary btn-sm">Back to Details</a>
        </div>
        <div class="card-body">
            <form action="{{ route('arms.update', $arm) }}" method="POST" enctype="multipart/form-data">
                @include('arms.arms._form')
            </form>
        </div>
    </div>
</div>
@endsection
