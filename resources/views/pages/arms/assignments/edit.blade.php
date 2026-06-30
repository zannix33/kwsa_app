@extends('layouts.app')

@section('title','Return Firearm')

@section('content')

    <div class="container-fluid">

        <div class="row mb-3">

            <div class="col-md-12">

                <h3>

                    <i class="fas fa-undo"></i>

                    Return Firearm

                </h3>

            </div>

        </div>

        <form
            action="{{ route('arms.assignments.update',$assignment) }}"
            method="POST">

            @include('arms.assignments._form')

        </form>

    </div>

@endsection
