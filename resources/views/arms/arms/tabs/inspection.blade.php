<div class="table-responsive">

    <table class="table table-striped table-bordered">

        <thead>

        <tr>

            <th>Date</th>

            <th>Inspector</th>

            <th>Inspection</th>

            <th>Result</th>

            <th>Next Inspection</th>

            <th></th>

        </tr>

        </thead>

        <tbody>

        @forelse($arm->inspections->sortByDesc('inspection_date') as $inspection)

            <tr>

                <td>

                    {{ optional($inspection->inspection_date)->format('M d, Y') }}

                </td>

                <td>

                    {{ $inspection->inspector }}

                </td>

                <td>

                    {{ $inspection->inspection_type }}

                </td>

                <td>

                    @if($inspection->result=='Passed')

                        <span class="badge badge-success">

Passed

</span>

                    @else

                        <span class="badge badge-danger">

Failed

</span>

                    @endif

                </td>

                <td>

                    {{ optional($inspection->next_inspection)->format('M d, Y') }}

                </td>

                <td>

                    <a
                        href="{{ route('arms.inspections.show',$inspection) }}"
                        class="btn btn-sm btn-primary">

                        View

                    </a>

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="6">

                    No inspection history.

                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

</div>
