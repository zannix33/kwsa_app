@push('custom-scripts')
    <script>
        flatpickr(".military-time", {

            enableTime: true,

            noCalendar: true,

            dateFormat: "H:i",

            time_24hr: true,

            minuteIncrement: 1

        });

    </script>




    <script>

        document.addEventListener('change', function(e){

            if(e.target.classList.contains('holiday'))
            {
                if(e.target.checked)
                {
                    e.target
                        .closest('tr')
                        .querySelector('.special_holiday')
                        .checked = false;
                }
            }

            if(e.target.classList.contains('special_holiday'))
            {
                if(e.target.checked)
                {
                    e.target
                        .closest('tr')
                        .querySelector('.holiday')
                        .checked = false;
                }
            }

        });

        function calculateND(start, end)
        {
            let ndMinutes = 0;

            let current = new Date(start);

            while(current < end)
            {
                let hour = current.getHours();

                if(hour >= 22 || hour < 6)
                {
                    ndMinutes++;
                }

                current.setMinutes(
                    current.getMinutes() + 1
                );
            }

            return ndMinutes / 60;
        }

        function calculateRow(row)
        {
            let operationStart =
                row.querySelector('.operation_start').value;

            let operationEnd =
                row.querySelector('.operation_end').value;

            let timeIn =
                row.querySelector('.time_in').value;

            let timeOut =
                row.querySelector('.time_out').value;

            let breakMinutes =
                parseFloat(
                    row.querySelector('.break_minutes').value || 0
                );

            if(
                !operationStart ||
                !operationEnd ||
                !timeIn ||
                !timeOut
            )
            {
                return;
            }

            let schedStart =
                new Date(
                    '2000-01-01 ' + operationStart
                );

            let schedEnd =
                new Date(
                    '2000-01-01 ' + operationEnd
                );

            let actualIn =
                new Date(
                    '2000-01-01 ' + timeIn
                );

            let actualOut =
                new Date(
                    '2000-01-01 ' + timeOut
                );

            /*
            |--------------------------------------------------------------------------
            | Overnight Schedule
            |--------------------------------------------------------------------------
            */

            if(schedEnd < schedStart)
            {
                schedEnd.setDate(
                    schedEnd.getDate() + 1
                );
            }

            if(actualOut < actualIn)
            {
                actualOut.setDate(
                    actualOut.getDate() + 1
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Scheduled Hours
            |--------------------------------------------------------------------------
            */

            let scheduledHours =
                (
                    ((schedEnd - schedStart) / 1000 / 60)
                    -
                    breakMinutes
                ) / 60;

            /*
            |--------------------------------------------------------------------------
            | Worked Hours
            |--------------------------------------------------------------------------
            */

            let workedHours =
                (
                    ((actualOut - actualIn) / 1000 / 60)
                    -
                    breakMinutes
                ) / 60;

            /*
            |--------------------------------------------------------------------------
            | Late
            |--------------------------------------------------------------------------
            */

            let lateHours = 0;

            if(actualIn > schedStart)
            {
                lateHours =
                    (
                        actualIn - schedStart
                    ) / 1000 / 60 / 60;
            }

            /*
            |--------------------------------------------------------------------------
            | Undertime
            |--------------------------------------------------------------------------
            */

            let undertimeHours = 0;

            if(actualOut < schedEnd)
            {
                undertimeHours =
                    (
                        schedEnd - actualOut
                    ) / 1000 / 60 / 60;
            }

            const REGULAR_LIMIT = 8;

            /*
                |--------------------------------------------------------------------------
                | Regular
                |--------------------------------------------------------------------------
            */

            let regularHours =
                Math.max(
                    scheduledHours
                    -
                    (scheduledHours - REGULAR_LIMIT)
                    -
                    lateHours
                    -
                    undertimeHours,
                    0
                );

            /*
             |--------------------------------------------------------------------------
             | Actual Payable Hours
             |--------------------------------------------------------------------------
             */

            let payableHours =
                Math.max(
                    workedHours
                    -
                    lateHours
                    -
                    undertimeHours,
                    0
                );


            /*
            |--------------------------------------------------------------------------
            | OT
            |--------------------------------------------------------------------------
            */

            let overtimeHours =
                Math.max(
                    payableHours -
                    REGULAR_LIMIT,
                    0
                );

            /*
            |--------------------------------------------------------------------------
            | ND
            |--------------------------------------------------------------------------
            */

            let ndHours =
                calculateND(
                    actualIn,
                    actualOut
                );

            /*
            |--------------------------------------------------------------------------
            | Display
            |--------------------------------------------------------------------------
            */

            row.querySelector('.scheduled_hours').value =
                scheduledHours.toFixed(2);

            row.querySelector('.regular_hours').value =
                regularHours.toFixed(2);

            row.querySelector('.overtime_hours').value =
                overtimeHours.toFixed(2);

            row.querySelector('.nd_hours').value =
                ndHours.toFixed(2);

            row.querySelector('.late_hours').value =
                lateHours.toFixed(2);

            row.querySelector('.undertime_hours').value =
                undertimeHours.toFixed(2);

            /*
            |--------------------------------------------------------------------------
            | Hidden Inputs
            |--------------------------------------------------------------------------
            */

            row.querySelector('.scheduled_hours_hidden').value =
                scheduledHours.toFixed(2);

            row.querySelector('.regular_hours_hidden').value =
                regularHours.toFixed(2);

            row.querySelector('.overtime_hours_hidden').value =
                overtimeHours.toFixed(2);

            row.querySelector('.nd_hours_hidden').value =
                ndHours.toFixed(2);

            row.querySelector('.late_hours_hidden').value =
                lateHours.toFixed(2);

            row.querySelector('.undertime_hours_hidden').value =
                undertimeHours.toFixed(2);

            calculateTotals();
        }

        function calculateTotals()
        {
            let scheduled = 0;
            let regular = 0;
            let ot = 0;
            let nd = 0;
            let late = 0;
            let ut = 0;

            document
                .querySelectorAll('tbody tr')
                .forEach(function(row){

                    scheduled += parseFloat(
                        row.querySelector('.scheduled_hours_hidden')?.value || 0
                    );

                    regular += parseFloat(
                        row.querySelector('.regular_hours_hidden')?.value || 0
                    );

                    ot += parseFloat(
                        row.querySelector('.overtime_hours_hidden')?.value || 0
                    );

                    nd += parseFloat(
                        row.querySelector('.nd_hours_hidden')?.value || 0
                    );

                    late += parseFloat(
                        row.querySelector('.late_hours_hidden')?.value || 0
                    );

                    ut += parseFloat(
                        row.querySelector('.undertime_hours_hidden')?.value || 0
                    );
                });

            document.getElementById('totalScheduled').innerHTML =
                scheduled.toFixed(2);

            document.getElementById('totalRegular').innerHTML =
                regular.toFixed(2);

            document.getElementById('totalOT').innerHTML =
                ot.toFixed(2);

            document.getElementById('totalND').innerHTML =
                nd.toFixed(2);

            document.getElementById('totalLate').innerHTML =
                late.toFixed(2);

            document.getElementById('totalUT').innerHTML =
                ut.toFixed(2);
        }

        /*
        |--------------------------------------------------------------------------
        | Apply Defaults
        |--------------------------------------------------------------------------
        */

        document
            .getElementById('applyDefaults')
            .addEventListener('click', function(){

                let operationStart =
                    document.getElementById(
                        'default_operation_start'
                    ).value;

                let operationEnd =
                    document.getElementById(
                        'default_operation_end'
                    ).value;

                let timeIn =
                    document.getElementById(
                        'default_time_in'
                    ).value;

                let timeOut =
                    document.getElementById(
                        'default_time_out'
                    ).value;

                let breakMinutes =
                    document.getElementById(
                        'default_break'
                    ).value;

                document
                    .querySelectorAll('tbody tr')
                    .forEach(function(row){

                        row.querySelector('.operation_start').value =
                            operationStart;

                        row.querySelector('.operation_end').value =
                            operationEnd;

                        row.querySelector('.time_in').value =
                            timeIn;

                        row.querySelector('.time_out').value =
                            timeOut;

                        row.querySelector('.break_minutes').value =
                            breakMinutes;

                        calculateRow(row);

                    });

            });

        /*
        |--------------------------------------------------------------------------
        | Sundays Rest Day
        |--------------------------------------------------------------------------
        */

        document
            .getElementById('markSundays')
            .addEventListener('click', function(){

                document
                    .querySelectorAll('tbody tr')
                    .forEach(function(row){

                        let day =
                            row.children[1]
                                .innerText
                                .trim();

                        if(day === 'Sunday')
                        {
                            row.querySelector(
                                '.rest_day'
                            ).checked = true;
                        }

                    });

            });

        /*
        |--------------------------------------------------------------------------
        | Recalculate On Change
        |--------------------------------------------------------------------------
        */

        document
            .addEventListener('change', function(e){

                if(
                    e.target.classList.contains('operation_start') ||
                    e.target.classList.contains('operation_end') ||
                    e.target.classList.contains('time_in') ||
                    e.target.classList.contains('time_out') ||
                    e.target.classList.contains('break_minutes')
                )
                {
                    calculateRow(
                        e.target.closest('tr')
                    );
                }

            });

        /*
        |--------------------------------------------------------------------------
        | Initial Totals
        |--------------------------------------------------------------------------
        */

        calculateTotals();

        document.getElementById('addRow').addEventListener('click', function () {

            let tbody = document.getElementById('dtrTableBody');

            let index = parseInt(document.getElementById('rowIndex').value);

            let row = `
                    <tr>

                    <td>

                    <input
                    type="date"
                    class="form-control work_date"
                    name="records[${index}][work_date]">

                    </td>

                    <td class="day_name"></td>

                    <td>
                    <input
                    type="time"
                    class="form-control military-time operation_start"
                    name="records[${index}][operation_start]">
                    </td>

                    <td>
                    <input
                    type="time"
                    class="form-control military-time operation_end"
                    name="records[${index}][operation_end]">
                    </td>

                    <td>

                    <input
                    type="number"
                    class="form-control break_minutes"
                    name="records[${index}][break_minutes]"
                    value="60">

                    </td>

                    <td>

                    <input
                    type="text"
                    class="form-control scheduled_hours"
                    readonly>

                    <input
                    type="hidden"
                    class="scheduled_hours_hidden"
                    name="records[${index}][scheduled_hours]">

                    </td>

                    <td>

                    <input
                    type="time"
                    class="form-control military-time time_in"
                    name="records[${index}][time_in]">

                    </td>

                    <td>

                    <input
                    type="time"
                    class="form-control military-time time_out"
                    name="records[${index}][time_out]">

                    </td>

                    <td>

                    <input
                    type="text"
                    readonly
                    class="form-control regular_hours">

                    <input
                    type="hidden"
                    class="regular_hours_hidden"
                    name="records[${index}][regular_hours]">

                    </td>

                    <td>

                    <input
                    type="text"
                    readonly
                    class="form-control overtime_hours">

                    <input
                    type="hidden"
                    class="overtime_hours_hidden"
                    name="records[${index}][overtime_hours]">

                    </td>

                    <td>

                    <input
                    type="text"
                    readonly
                    class="form-control nd_hours">

                    <input
                    type="hidden"
                    class="nd_hours_hidden"
                    name="records[${index}][night_differential_hours]">

                    </td>

                    <td>

                    <input
                    type="text"
                    readonly
                    class="form-control late_hours">

                    <input
                    type="hidden"
                    class="late_hours_hidden"
                    name="records[${index}][late_hours]">

                    </td>

                    <td>

                    <input
                    type="text"
                    readonly
                    class="form-control undertime_hours">

                    <input
                    type="hidden"
                    class="undertime_hours_hidden"
                    name="records[${index}][undertime_hours]">

                    </td>

                    <td class="text-center">

                    <input
                    type="checkbox"
                    class="rest_day"
                    name="records[${index}][is_rest_day]">

                    </td>

                    <td class="text-center">

                    <input
                    type="checkbox"
                    class="holiday"
                    name="records[${index}][is_holiday]">

                    </td>

                    <td class="text-center">

                    <input
                    type="checkbox"
                    class="special_holiday"
                    name="records[${index}][special_holiday]">

                    </td>

                    <td class="text-center">

                    <input
                    type="checkbox"
                    class="extended_hours"
                    name="records[${index}][is_extended_hours]" checked>

                    </td>

                    <td>

                    <input
                    type="text"
                    class="form-control"
                    name="records[${index}][remarks]">

                    </td>

                    <td class="text-center">

                    <button
                    type="button"
                    class="btn btn-danger btn-sm removeRow">

                    <i class="fa fa-trash"></i>

                    </button>

                    </td>

                    </tr>
                    `;

            tbody.insertAdjacentHTML('beforeend', row);

            document.getElementById('rowIndex').value = index + 1;

        });

        document.addEventListener('change', function (e) {

            if (e.target.classList.contains('work_date')) {

                let row = e.target.closest('tr');

                let date = new Date(e.target.value);

                if (!isNaN(date)) {

                    row.querySelector('.day_name').innerHTML =
                        date.toLocaleDateString('en-US', {
                            weekday: 'long'
                        });

                }

            }

        });

        document.addEventListener('click', function (e) {

            let button = e.target.closest('.removeRow');

            if (button) {

                button.closest('tr').remove();

                calculateTotals();

            }

        });

    </script>
@endpush
