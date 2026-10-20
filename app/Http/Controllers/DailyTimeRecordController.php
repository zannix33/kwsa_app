<?php

namespace App\Http\Controllers;

use App\Models\DailyTimeRecord;
use App\Models\PayrollPeriod;
use App\Models\User;
use App\Models\Area;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailyTimeRecordController extends Controller
{
    public function createBulk()
    {
        //$users = User::orderBy('name')
        //    ->get();

        $users = User::where(function ($query) {
            $query->whereHas('areas')
                ->orWhereHas('branches');
        })
            ->orderBy('lastname')
            ->orderBy('firstname')
            ->get();

        return view(
            'pages.dtr.bulk-create',
            compact('users')
        );
    }

    public function generateBulk(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'from_date'   => 'required|date',
            'to_date'     => 'required|date|after_or_equal:from_date',
        ]);

        //$user = User::findOrFail(
        //    $request->user_id
        //);

        //$user = User::with([
        //    'areas.branches'
        //])->findOrFail($request->user_id);

        $period = CarbonPeriod::create(
            $request->from_date,
            $request->to_date
        );

        $user = User::with([
            'areas.branches'
        ])->findOrFail($request->user_id);

        $areas = Area::with([
                'branches'
            ])->get();

        //$payrollPeriod = PayrollPeriod::findOrFail(
        //    $period->id
        //);

        $dates = [];

        $current = Carbon::parse(
            $request->from_date,
        );

        $end = Carbon::parse(
            $request->to_date
        );

        while($current->lte($end)){
            $dates[] = $current->copy();
            $current->addDay();
        }

        return view(
            'pages.dtr.bulk-entry',
            [

                'user'=>$user,

                'areas'=>$areas, //$user->areas,

                'dates'=>$dates,

                'period'=>$period

            ]
        );

        //return view(
        //    'pages.dtr.bulk-entry',
        //    compact(
        //        'user',
        //        'period'
        //    )
        //);
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'records'     => 'required|array'
        ]);

        DB::transaction(function () use ($request) {

            foreach ($request->records as $record) {

                if (
                    empty($record['time_in']) &&
                    empty($record['time_out'])
                ) {
                    continue;
                }

                $calculated =
                    $this->calculateDtr($record);

                DailyTimeRecord::updateOrCreate(
                    [
                        'user_id' => $request->user_id,
                        'work_date'   => $record['work_date'],
                        'area_id'=>$record['area_id'],
                        'branch_id'=>$record['branch_id'],
                    ],
                    [
                        'operation_start' =>
                            $calculated['operation_start'],

                        'operation_end' =>
                            $calculated['operation_end'],

                        'time_in' =>
                            $calculated['time_in'],

                        'time_out' =>
                            $calculated['time_out'],

                        'break_minutes' =>
                            $record['break_minutes'] ?? 60,

                        'scheduled_hours' =>
                            $calculated['scheduled_hours'],

                        'regular_hours' =>
                            $calculated['regular_hours'],

                        'overtime_hours' =>
                            $calculated['overtime_hours'],

                        'extended_hours' =>
                            $calculated['extended_hours'],

                        'night_differential_hours' =>
                            $calculated['night_differential_hours'],

                        'late_hours' =>
                            $calculated['late_hours'],

                        'undertime_hours' =>
                            $calculated['undertime_hours'],

                        'total_hours' =>
                            $calculated['worked_hours'],

                        'is_rest_day' =>
                            isset($record['is_rest_day']),

                        'is_holiday' =>
                            isset($record['is_holiday']),

                        'remarks' =>
                            $record['remarks'] ?? null,
                    ]
                );
            }
        });

        return redirect()
            ->route('dtr.bulk.create')
            ->with(
                'success',
                'DTR records saved successfully.'
            );
    }

    private function calculateDtr(array $record)
    {
        $regularLimit = 8;

        $workDate =
            Carbon::parse(
                $record['work_date']
            );

        /*
        |--------------------------------------------------------------------------
        | Schedule
        |--------------------------------------------------------------------------
        */

        $operationStart =
            Carbon::parse(
                $workDate->format('Y-m-d')
                .' '.
                $record['operation_start']
            );

        $operationEnd =
            Carbon::parse(
                $workDate->format('Y-m-d')
                .' '.
                $record['operation_end']
            );

        if ($operationEnd->lt($operationStart)) {
            $operationEnd->addDay();
        }

        /*
        |--------------------------------------------------------------------------
        | Actual Attendance
        |--------------------------------------------------------------------------
        */

        $timeIn =
            Carbon::parse(
                $workDate->format('Y-m-d')
                .' '.
                $record['time_in']
            );

        $timeOut =
            Carbon::parse(
                $workDate->format('Y-m-d')
                .' '.
                $record['time_out']
            );

        if ($timeOut->lt($timeIn)) {
            $timeOut->addDay();
        }

        $breakMinutes =
            (int)($record['break_minutes'] ?? 60);

        /*
        |--------------------------------------------------------------------------
        | Scheduled Hours
        |--------------------------------------------------------------------------
        */

        $scheduledHours =
            (
                $operationStart->diffInMinutes(
                    $operationEnd
                )
                -
                $breakMinutes
            ) / 60;

        /*
        |--------------------------------------------------------------------------
        | Worked Hours
        |--------------------------------------------------------------------------
        */

        $workedHours =
            (
                $timeIn->diffInMinutes(
                    $timeOut
                )
                -
                $breakMinutes
            ) / 60;

        /*
        |--------------------------------------------------------------------------
        | Late
        |--------------------------------------------------------------------------
        */

        $lateHours = 0;

        if ($timeIn->gt($operationStart)) {

            $lateHours =
                $operationStart
                    ->diffInMinutes(
                        $timeIn
                    ) / 60;
        }

        /*
        |--------------------------------------------------------------------------
        | Undertime
        |--------------------------------------------------------------------------
        */

        $undertimeHours = 0;

        if ($timeOut->lt($operationEnd)) {

            $undertimeHours =
                $timeOut
                    ->diffInMinutes(
                        $operationEnd
                    ) / 60;
        }

        /*
        |--------------------------------------------------------------------------
        | Regular Hours
        |--------------------------------------------------------------------------
        */
        $payableHours = max(
            $workedHours - $lateHours - $undertimeHours,
            0
        );

        $regularHours = min(
            $payableHours,
            $regularLimit
        );


        /*
        |--------------------------------------------------------------------------
        | Extended Hours
        |--------------------------------------------------------------------------
        |
        | Hours worked after operation_end.
        |
        */

        $extendedHours = 0;

        if ($operationEnd && $timeOut->greaterThan($operationEnd)) {

            $extendedHours = round(
                $operationEnd->diffInMinutes($timeOut) / 60,
                2
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Overtime
        |--------------------------------------------------------------------------
        */

        $overtimeHours = max(
            $payableHours - $regularLimit,
            0
        );

        if($extendedHours != 0) {
            $overtimeHours -= $extendedHours;
        }

        /*
        |--------------------------------------------------------------------------
        | Night Differential
        |--------------------------------------------------------------------------
        */

        $nightDifferentialHours =
            $this->calculateND(
                $timeIn,
                $timeOut
            );

        return [

            'operation_start' =>
                $operationStart,

            'operation_end' =>
                $operationEnd,

            'time_in' =>
                $timeIn,

            'time_out' =>
                $timeOut,

            'scheduled_hours' =>
                round(
                    $scheduledHours,
                    2
                ),

            'worked_hours' =>
                round(
                    $workedHours,
                    2
                ),

            'regular_hours' =>
                round(
                    $regularHours,
                    2
                ),

            'overtime_hours' =>
                round(
                    $overtimeHours,
                    2
                ),

            'extended_hours' =>
                round(
                    $extendedHours,
                    2
                ),

            'late_hours' =>
                round(
                    $lateHours,
                    2
                ),

            'undertime_hours' =>
                round(
                    $undertimeHours,
                    2
                ),

            'night_differential_hours' =>
                round(
                    $nightDifferentialHours,
                    2
                ),
        ];
    }

    private function calculateND(
        Carbon $timeIn,
        Carbon $timeOut
    ): float
    {
        $minutes = 0;

        $current = $timeIn->copy();

        while ($current < $timeOut) {

            $hour = $current->hour;

            if (
                $hour >= 22 ||
                $hour < 6
            ) {
                $minutes++;
            }

            $current->addMinute();
        }

        return $minutes / 60;
    }

    public function assignments(User $employee)
    {
        $employee->load([
            'areas.branches'
        ]);

        return response()->json([
            'areas' => $employee->areas
        ]);
    }
}
