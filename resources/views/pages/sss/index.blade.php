@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush

@section('content')
    <div class="card">

        <div class="card-header">

            <a
                href="{{ route('sss.create') }}"
                class="btn btn-primary">

                Add Contribution

            </a>

        </div>

        <div class="card-body">

            <table
                id="datatable"
                class="table table-bordered">

                <thead>

                <tr>
                    <th>From</th>
                    <th>To</th>
                    <th>Employee</th>
                    <th>Employer</th>
                    <th>EC</th>
                    <th>Rate</th>
                    <th>Status</th>
                    <th>Action</th>
                </tr>

                </thead>

                <tbody>

                @foreach($records as $row)

                    <tr>

                        <td>{{ $row->from_salary }}</td>
                        <td>{{ $row->to_salary }}</td>
                        <td>{{ $row->employee_share }}</td>
                        <td>{{ $row->employer_share }}</td>
                        <td>{{ $row->ec }}</td>
                        <td>{{ $row->rate }}</td>
                        <td>
                            {{ $row->active ? 'Active' : 'Inactive' }}
                        </td>

                        <td>

                            <a
                                href="{{ route('sss.edit',$row) }}"
                                class="btn btn-warning btn-sm">
                                Edit
                            </a>

                            <form
                                method="POST"
                                action="{{ route('sss.destroy',$row) }}"
                                style="display:inline">

                                @csrf
                                @method('DELETE')

                                <button
                                    class="btn btn-danger btn-sm">

                                    Delete

                                </button>

                            </form>

                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>

    </div>
@endsection
