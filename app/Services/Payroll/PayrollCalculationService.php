<?php

namespace App\Services\Payroll;

use App\Models\User;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;

class PayrollCalculationService
{
    public function calculate(
        User $user,
        Collection $dtrs
    ): array {

        //dd($user->daily_rate);

        $maximumRegularHours = 8;

        $dailyRate = $user->daily_rate; //$user->monthly_salary / 26;
        $monthlySalary = $dailyRate * 30;
        $hourlyRate = $dailyRate / 8;

        $daysWorked = 0;

        $regularHours = 0;
        $overtimeHours = 0;
        $nightDiffHours = 0;

        $lateMinutes = 0;
        $undertimeMinutes = 0;

        $holidayPay = 0;
        $spHolidayPay = 0;

        $regularOvertimePay = 0;
        $specialOvertimeHours = 0;
        $specialOvertimePay = 0;

        foreach ($dtrs as $dtr) {

            if ($dtr->time_in && $dtr->time_out) {
                $daysWorked++;
            }

            $nightDiffHours +=
                $dtr->night_differential_hours ?? 0;

            $lateMinutes +=
                $dtr->late_minutes ?? 0;

            $undertimeMinutes +=
                $dtr->undertime_minutes ?? 0;

            $regularHours +=
                $dtr->regular_hours ?? 0;

            /*
            |--------------------------------------------------------------------------
            | Holiday Computation
            |--------------------------------------------------------------------------
            */

            /*if ($dtr->holiday_type === 'regular') {

                $holidayPay +=
                    ($dtr->regular_hours ?? 8)
                    * $hourlyRate
                    * 2;

            } elseif ($dtr->holiday_type === 'special') {

                $holidayPay +=

                        ($dtr->regular_hours ?? 8)
                        * $hourlyRate
                     * 1.30;
            }*/

            if ($dtr->is_holiday) {

                $holidayPay +=
                    ($dtr->regular_hours ?? 8)
                    * $hourlyRate
                    * 2;

                $regularHours -= 8;

            }

            if ($dtr->special_holiday) {

                $spHolidayPay +=
                    ($dtr->regular_hours ?? 8)
                    * $hourlyRate
                    * 1.30;

                $regularHours -= 8;
            }

            // Sunday Computation for Overtime

            $isSunday = Carbon::parse($dtr->time_out)->isSunday();


            if ($dtr->overtime_hours > 0) {

                if ($isSunday || $dtr->special_holiday) {
                    $specialOvertimeHours +=
                        $dtr->overtime_hours ?? 0;

                    $specialOvertimePay +=
                        $dtr->overtime_hours *
                        $hourlyRate *
                        1.69;

                } else {
                    $overtimeHours +=
                        $dtr->overtime_hours ?? 0;

                    $regularOvertimePay +=
                        $dtr->overtime_hours *
                        $hourlyRate *
                        1.25;

                }

            }

        }

        /*
        |--------------------------------------------------------------------------
        | Earnings
        |--------------------------------------------------------------------------
        */

        $basicPay =
            $regularHours *
            $hourlyRate;

        $overtimePay =
            $overtimeHours *
            ($hourlyRate * 1.25);

        $nightDiffPay =
            $nightDiffHours *
            ($hourlyRate * 0.10);

        $overallPay =
            $basicPay +
            $overtimePay +
            $nightDiffPay;

        /*
        |--------------------------------------------------------------------------
        | Deductions
        |--------------------------------------------------------------------------
        */

        $lateDeduction =
            ($hourlyRate / 60)
            * $lateMinutes;

        $undertimeDeduction =
            ($hourlyRate / 60)
            * $undertimeMinutes;



        return [

            'earnings' => [

                'basic_pay' =>
                    round($basicPay, 2),

                'regular_overtime_pay' =>
                    round($regularOvertimePay, 2),

                'night_diff_pay' =>
                    round($nightDiffPay, 2),

                'holiday_pay' =>
                    round($holidayPay, 2),
                'sp_holiday_pay' =>
                    round($spHolidayPay, 2),
                'overall_pay' =>
                    round($overallPay, 2),
                'special_overtime_pay' =>
                    round($specialOvertimePay, 2)

            ],

            'deductions' => [

                'late' =>
                    round($lateDeduction, 2),

                'CB' =>
                    round(75, 2),

                'undertime' =>
                    round($undertimeDeduction, 2)

            ],

            'metrics' => [

                'days_worked' =>
                    $daysWorked,

                'regular_hours' =>
                    $regularHours,

                'overtime_hours' =>
                    $overtimeHours,

                'night_diff_hours' =>
                    $nightDiffHours,

                'late_minutes' =>
                    $lateMinutes,

                'undertime_minutes' =>
                    $undertimeMinutes,

                'daily_rate' =>
                    round($dailyRate, 2),

                'hourly_rate' =>
                    round($hourlyRate, 2)
            ]
        ];
    }
}
