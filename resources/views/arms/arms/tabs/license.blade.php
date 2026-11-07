<div class="table-responsive">

    <table class="table table-hover table-bordered">

        <thead class="thead-light">

        <tr>

            <th>License No.</th>

            <th>Issue Date</th>

            <th>Expiry Date</th>

            <th>Status</th>

            <th>Issued By</th>

            <th></th>

        </tr>

        </thead>

        <tbody>

        @forelse($arm->licenses->sortByDesc('issue_date') as $license)

            <tr>

                <td>

                    {{ $license->license_number }}

                </td>

                <td>

                    {{ optional($license->issue_date)->format('M d, Y') }}

                </td>

                <td>

                    {{ optional($license->expiry_date)->format('M d, Y') }}

                </td>

                <td>

                    @if($license->expiry_date->isFuture())

                        <span class="badge badge-success">

Active

</span>

                    @else

                        <span class="badge badge-danger">

Expired

</span>

                    @endif

                </td>

                <td>

                    {{ $license->issued_by }}

                </td>

                <td>

                    <a
                        href="{{ route('arms.licenses.show',$license) }}"
                        class="btn btn-sm btn-primary">

                        View

                    </a>

                </td>

            </tr>

        @empty

            <tr>

                <td colspan="6" class="text-center">

                    No license history.

                </td>

            </tr>

        @endforelse

        </tbody>

    </table>

</div>
