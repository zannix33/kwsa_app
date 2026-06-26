<div class="card">

    <div class="card-body">

        <div class="mb-3">

            <button type="button"
                    id="markSundays"
                    class="btn btn-warning btn-sm">

                Sundays Rest Day

            </button>

        </div>

        <div class="table-responsive">

            <table class="table table-bordered table-sm">

                <thead>

                <tr>

                    <th>Date</th>
                    <th>Day</th>

                    <th>Operation Start</th>
                    <th>Operation End</th>

                    <th>Break</th>

                    <th>Scheduled</th>

                    <th>Time In</th>
                    <th>Time Out</th>

                    <th>Regular</th>
                    <th>OT</th>
                    <th>ND</th>
                    <th>Late</th>
                    <th>UT</th>

                    <th>Rest</th>
                    <th>Holiday</th>

                    <th>Remarks</th>

                </tr>

                </thead>

                <tbody>

                @foreach($period as $date)

                    <tr>

                        <td>

                            {{ $date->format('M d, Y') }}

                            <input type="hidden"
                                   name="records[{{ $loop->index }}][work_date]"
                                   value="{{ $date->format('Y-m-d') }}">

                        </td>

                        <td>{{ $date->format('l') }}</td>

                        <td>
                            <input type="time"
                                   class="form-control operation_start"
                                   name="records[{{ $loop->index }}][operation_start]">
                        </td>

                        <td>
                            <input type="time"
                                   class="form-control operation_end"
                                   name="records[{{ $loop->index }}][operation_end]">
                        </td>

                        <td>
                            <input type="number"
                                   class="form-control break_minutes"
                                   value="60"
                                   name="records[{{ $loop->index }}][break_minutes]">
                        </td>

                        <td>
                            <input type="text"
                                   readonly
                                   class="form-control scheduled_hours">

                            <input type="hidden"
                                   class="scheduled_hours_hidden"
                                   name="records[{{ $loop->index }}][scheduled_hours]">
                        </td>

                        <td>
                            <input type="time"
                                   class="form-control time_in"
                                   name="records[{{ $loop->index }}][time_in]">
                        </td>

                        <td>
                            <input type="time"
                                   class="form-control time_out"
                                   name="records[{{ $loop->index }}][time_out]">
                        </td>

                        <td>
                            <input type="text"
                                   readonly
                                   class="form-control regular_hours">

                            <input type="hidden"
                                   class="regular_hours_hidden"
                                   name="records[{{ $loop->index }}][regular_hours]">
                        </td>

                        <td>
                            <input type="text"
                                   readonly
                                   class="form-control overtime_hours">

                            <input type="hidden"
                                   class="overtime_hours_hidden"
                                   name="records[{{ $loop->index }}][overtime_hours]">
                        </td>

                        <td>
                            <input type="text"
                                   readonly
                                   class="form-control nd_hours">

                            <input type="hidden"
                                   class="nd_hours_hidden"
                                   name="records[{{ $loop->index }}][night_differential_hours]">
                        </td>

                        <td>
                            <input type="text"
                                   readonly
                                   class="form-control late_hours">

                            <input type="hidden"
                                   class="late_hours_hidden"
                                   name="records[{{ $loop->index }}][late_hours]">
                        </td>

                        <td>
                            <input type="text"
                                   readonly
                                   class="form-control undertime_hours">

                            <input type="hidden"
                                   class="undertime_hours_hidden"
                                   name="records[{{ $loop->index }}][undertime_hours]">
                        </td>

                        <td>
                            <input type="checkbox"
                                   class="rest_day"
                                   name="records[{{ $loop->index }}][is_rest_day]">
                        </td>

                        <td>
                            <input type="checkbox"
                                   name="records[{{ $loop->index }}][is_holiday]">
                        </td>

                        <td>
                            <input type="text"
                                   class="form-control"
                                   name="records[{{ $loop->index }}][remarks]">
                        </td>

                    </tr>

                @endforeach

                </tbody>

            </table>

        </div>

    </div>

</div>
