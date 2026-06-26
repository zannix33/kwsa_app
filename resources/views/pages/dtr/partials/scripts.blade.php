@push('custom-scripts')
    <script>

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

            /*
            |--------------------------------------------------------------------------
            | Regular
            |--------------------------------------------------------------------------
            */

            let regularHours =
                Math.max(
                    scheduledHours
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
                    workedHours -
                    regularHours,
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

    </script>
@endpush
