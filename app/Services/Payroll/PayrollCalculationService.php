<?php

namespace App\Services\Payroll;

use App\Models\User;
use Illuminate\Support\Collection;

class PayrollCalculationService
{
    public function calculate(
        User $user,
        Collection $dtrs
    ): array {

        $dailyRate = 450; //$user->monthly_salary / 26;
        $hourlyRate = $dailyRate / 8;

        $daysWorked = 0;

        $regularHours = 0;
        $overtimeHours = 0;
        $nightDiffHours = 0;

        $lateMinutes = 0;
        $undertimeMinutes = 0;

        $holidayPay = 0;

        foreach ($dtrs as $dtr) {

            if ($dtr->time_in && $dtr->time_out) {
                $daysWorked++;
            }

            $regularHours +=
                $dtr->regular_hours ?? 0;

            $overtimeHours +=
                $dtr->overtime_hours ?? 0;

            $nightDiffHours +=
                $dtr->night_differential_hours ?? 0;

            $lateMinutes +=
                $dtr->late_minutes ?? 0;

            $undertimeMinutes +=
                $dtr->undertime_minutes ?? 0;

            /*
            |--------------------------------------------------------------------------
            | Holiday Computation
            |--------------------------------------------------------------------------
            */

            if ($dtr->holiday_type === 'regular') {

                $holidayPay +=
                    ($dtr->regular_hours ?? 8)
                    * $hourlyRate;

            } elseif ($dtr->holiday_type === 'special') {

                $holidayPay +=
                    (
                        ($dtr->regular_hours ?? 8)
                        * $hourlyRate
                    ) * 0.30;
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Earnings
        |--------------------------------------------------------------------------
        */

        $basicPay =
            $daysWorked *
            $dailyRate;

        $overtimePay =
            $overtimeHours *
            ($hourlyRate * 1.25);

        $nightDiffPay =
            $nightDiffHours *
            ($hourlyRate * 0.10);

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

                'overtime_pay' =>
                    round($overtimePay, 2),

                'night_diff_pay' =>
                    round($nightDiffPay, 2),

                'holiday_pay' =>
                    round($holidayPay, 2),

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
