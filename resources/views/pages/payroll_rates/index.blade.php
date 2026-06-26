@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')

    <div class="container">

        <div class="d-flex justify-content-between mb-3">
            <h4>Payroll Rates</h4>
            <a href="{{ route('payroll-rates.create') }}" class="btn btn-primary">Add Rate</a>
        </div>

        @if(session('success'))
            <div class="alert alert-success">{{ session('success') }}</div>
        @endif

        <table class="table table-bordered table-striped" id="ratesTable">
            <thead>
            <tr>
                <th>Name</th>
                <th>Slug</th>
                <th>Rate</th>
                <th>Active</th>
                <th width="180">Action</th>
            </tr>
            </thead>

            <tbody>
            @foreach($rates as $rate)
                <tr>
                    <td>{{ $rate->name }}</td>
                    <td>{{ $rate->slug }}</td>
                    <td>{{ number_format($rate->rate, 2) }}</td>
                    <td>
                    <span class="badge badge-{{ $rate->active ? 'success' : 'danger' }}">
                        {{ $rate->active ? 'Active' : 'Inactive' }}
                    </span>
                    </td>
                    <td>
                        <a href="{{ route('payroll-rates.edit', $rate->id) }}" class="btn btn-sm btn-warning">Edit</a>

                        <form action="{{ route('payroll-rates.destroy', $rate->id) }}" method="POST" style="display:inline;">
                            @csrf
                            @method('DELETE')
                            <button class="btn btn-sm btn-danger" onclick="return confirm('Delete this rate?')">
                                Delete
                            </button>
                        </form>
                    </td>
                </tr>
            @endforeach
            </tbody>
        </table>

    </div>

@endsection

@push('custom-scripts')
    <script>
        $(document).ready(function () {
            $('#ratesTable').DataTable({
                pageLength: 10,
                lengthMenu: [5, 10, 25, 50, 100],
                ordering: true,
                searching: true
            });
        });
    </script>
@endpush
