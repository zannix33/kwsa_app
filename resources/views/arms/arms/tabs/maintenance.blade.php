<div class="table-responsive">

    <table class="table table-bordered table-hover">

        <thead class="thead-light">

        <tr>

            <th>Date</th>

            <th>Type</th>

            <th>Performed By</th>

            <th>Cost</th>

            <th>Status</th>

            <th>Next Due</th>

            <th></th>

        </tr>

        </thead>

        <tbody>

        @forelse($arm->maintenances->sortByDesc('maintenance_date') as $maintenance)

            <tr>

                <td>

                    {{ optional($maintenance->maintenance_date)->format('M d, Y') }}

                </td>

                <td>

                    {{ $maintenance->maintenance_type }}

                </td>

                <td>

                    {{ $maintenance->performed_by }}

                </td>

                <td>

                    ₱ {{ number_format($maintenance->cost,2) }}

                </td>

                <td>

                    @if($maintenance->status=='Completed')

                        <span class="badge badge-success">

Completed

</span>

                    @else

                        <span class="badge badge-warning">

Pending

</span>

                    @endif

                </td>

                <td>

                    {{ optional($maintenance->next_maintenance)->format('M d, Y') }}

                </td>

                <td>

                    <a
                        href="{{ route('arms.maintenances.show',$maintenance) }}"
                        class="btn btn-primary btn-sm">

                        View

                    </a>

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="7" class="text-center">

                    No maintenance records.

                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

</div>
