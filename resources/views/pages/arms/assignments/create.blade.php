@extends('layouts.app')

@section('title','Assign Firearm')

@section('content')

    <div class="container-fluid">

        <div class="row mb-3">

            <div class="col-md-12">

                <h3>

                    <i class="fas fa-user-plus"></i>

                    New Firearm Assignment

                </h3>

            </div>

        </div>

        <form
            action="{{ route('arms.assignments.store') }}"
            method="POST">

            @include('arms.assignments._form')

        </form>

    </div>

@endsection
