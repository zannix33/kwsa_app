<h2>

    Incident Report

</h2>

<table border="1" width="100%">

    <tr>

        <th>Date</th>

        <th>Employee</th>

        <th>Category</th>

        <th>Status</th>

    </tr>

    @foreach($incidents as $incident)

        <tr>

            <td>

                {{ $incident->incident_date }}

            </td>

            <td>

                {{ optional($incident->user)->full_name }}

            </td>

            <td>

                {{ $incident->category }}

            </td>

            <td>

                {{ $incident->status }}

            </td>

        </tr>

    @endforeach

</table>
