<div class="table-responsive">

    <table class="table table-hover table-striped">

        <thead class="thead-light">

        <tr>

            <th width="150">Assigned Date</th>

            <th>Employee</th>

            <th>Branch</th>

            <th>Purpose</th>

            <th width="120">Returned</th>

            <th width="100">Status</th>

            <th width="90"></th>

        </tr>

        </thead>

        <tbody>

        @forelse($arm->assignments->sortByDesc('assigned_at') as $assignment)

            <tr>

                <td>
                    {{ optional($assignment->assigned_at)->format('M d, Y') }}
                </td>

                <td>
                    {{ optional($assignment->user)->name }}
                </td>

                <td>
                    {{ optional(optional($assignment->user)->branch)->name }}
                </td>

                <td>
                    {{ $assignment->purpose }}
                </td>

                <td>

                    @if($assignment->returned_at)

                        {{ $assignment->returned_at->format('M d, Y') }}

                    @else

                        -

                    @endif

                </td>

                <td>

                    @if($assignment->returned_at)

                        <span class="badge badge-success">

                            Returned

                        </span>

                    @else

                        <span class="badge badge-warning">

                            Active

                        </span>

                    @endif

                </td>

                <td>

                    <a
                        href="{{ route('arms.assignments.show',$assignment) }}"
                        class="btn btn-sm btn-primary">

                        View

                    </a>

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="7" class="text-center">

                    No assignment history.

                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

</div>
