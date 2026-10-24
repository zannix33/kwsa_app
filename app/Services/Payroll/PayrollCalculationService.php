<?php

namespace App\Services\Payroll;

use App\Models\User;
use App\Models\PayRate;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

class PayrollCalculationService
{
    public function calculate(User $user, Collection $dtrs): array
    {
        $daysWorked = 0;

        $regularHours = 0;
        $overtimeHours = 0;
        $specialOvertimeHours = 0;
        $regularHolidayOvertimeHours = 0;
        $nightDiffHours = 0;

        $lateMinutes = 0;
        $undertimeMinutes = 0;

        $basicPay = 0;
        $regularOvertimePay = 0;
        $specialOvertimePay = 0;
        $regularHolidayOvertimePay = 0;
        $nightDiffPay = 0;
        $holidayPay = 0;
        $spHolidayPay = 0;

        $lateDeduction = 0;
        $undertimeDeduction = 0;

        // Used for reporting only. Do not use these averages
        // to reconstruct earnings when rates vary by work date.
        $dailyRateTotal = 0;
        $hourlyRateTotal = 0;
        $rateCount = 0;


        // Group calculations by the actual effective pay rate.
        $rateBreakdown = [];

        foreach ($dtrs as $dtr) {
            /*
             * Resolve area.
             * Prefer the DTR's direct area assignment.
             * Otherwise use the branch's area.
             */
            $area = $dtr->area;

            if (!$area && $dtr->branch) {
                $area = $dtr->branch->area;
            }

            if (!$area || !$area->region_id) {
                throw ValidationException::withMessages([
                    'payroll' =>
                        "DTR ID {$dtr->id} has no area or regional assignment.",
                ]);
            }

            if (!$dtr->work_date) {
                throw ValidationException::withMessages([
                    'payroll' =>
                        "DTR ID {$dtr->id} has no work date.",
                ]);
            }

            $workDate = Carbon::parse($dtr->work_date)->toDateString();

            /*
             * Find the regional rate effective on this DTR's date.
             */
            $payRate = PayRate::where('region_id', $area->region_id)
                ->whereDate('effective_from', '<=', $workDate)
                ->where(function ($query) use ($workDate) {
                    $query->whereNull('effective_to')
                        ->orWhereDate('effective_to', '>=', $workDate);
                })
                ->orderByDesc('effective_from')
                ->first();

            if (!$payRate) {
                throw ValidationException::withMessages([
                    'payroll' =>
                        "No regional pay rate exists for area "
                        . "{$area->name} on {$workDate}.",
                ]);
            }

            $dailyRate = (float) $payRate->daily_rate;
            $hourlyRate = $dailyRate / 8;

            $dailyRateTotal += $dailyRate;
            $hourlyRateTotal += $hourlyRate;
            $rateCount++;

            /*
             * Attendance fields.
             */
            if ($dtr->time_in && $dtr->time_out) {
                $daysWorked++;
            }

            $dtrRegularHours = (float) ($dtr->regular_hours ?? 0);
            $dtrOvertimeHours = (float) ($dtr->overtime_hours ?? 0);
            $dtrExtendedHours = (float) ($dtr->extended_hours ?? 0);
            $dtrNightDiffHours =
                (float) ($dtr->night_differential_hours ?? 0);

            // Support either minute fields or existing hour fields.
            $dtrLateMinutes = $dtr->late_minutes !== null
                ? (float) $dtr->late_minutes
                : ((float) ($dtr->late_hours ?? 0) * 60);

            $dtrUndertimeMinutes = $dtr->undertime_minutes !== null
                ? (float) $dtr->undertime_minutes
                : ((float) ($dtr->undertime_hours ?? 0) * 60);

            $nightDiffHours += $dtrNightDiffHours;
            $lateMinutes += $dtrLateMinutes;
            $undertimeMinutes += $dtrUndertimeMinutes;

            /*
             * Holiday pay.
             *
             * A regular holiday takes precedence if both flags
             * are accidentally set on the same DTR.
             */
            if ($dtr->is_holiday) {
                $holidayHours = min(8, $dtrRegularHours);

                $holidayAmount =
                    $holidayHours * $hourlyRate * 2;

                $holidayPay += $holidayAmount;
                $dtrRegularHours -= $holidayHours;
            } elseif ($dtr->special_holiday) {
                $specialHolidayHours = min(8, $dtrRegularHours);

                $specialHolidayAmount =
                    $specialHolidayHours * $hourlyRate * 1.30;

                $spHolidayPay += $specialHolidayAmount;
                $dtrRegularHours -= $specialHolidayHours;
            }

            $dtrRegularHours = max(0, $dtrRegularHours);

            $regularHours += $dtrRegularHours;

            $dtrBasicPay = $dtrRegularHours * $hourlyRate;
            $basicPay += $dtrBasicPay;

            /*
             * Overtime and extended hours.
             */
            $totalOvertimeHours =
                $dtrOvertimeHours + $dtrExtendedHours;

            $isSunday = Carbon::parse($workDate)->isSunday();

            $dtrRegularOvertimePay = 0;
            $dtrSpecialOvertimePay = 0;
            $dtrRegularHolidayOvertimePay = 0;

            if ($totalOvertimeHours > 0) {
                // Regular/legal holiday overtime takes precedence, even if it is Sunday.
                if ($dtr->is_holiday) {
                    $regularHolidayOvertimeHours += $totalOvertimeHours;
                    $dtrRegularHolidayOvertimePay =
                        $totalOvertimeHours * $hourlyRate * 2.60;
                    $regularHolidayOvertimePay += $dtrRegularHolidayOvertimePay;
                } elseif ($isSunday || $dtr->special_holiday) {
                    $specialOvertimeHours += $totalOvertimeHours;

                    $dtrSpecialOvertimePay =
                        $totalOvertimeHours * $hourlyRate * 1.69;

                    $specialOvertimePay += $dtrSpecialOvertimePay;
                } else {
                    $overtimeHours += $totalOvertimeHours;

                    $dtrRegularOvertimePay =
                        $totalOvertimeHours * $hourlyRate * 1.25;

                    $regularOvertimePay += $dtrRegularOvertimePay;
                }
            }

            /*
             * Night differential and attendance deductions.
             */
            $dtrNightDiffPay =
                $dtrNightDiffHours * $hourlyRate * 0.10;

            $nightDiffPay += $dtrNightDiffPay;

            $dtrLateDeduction =
                ($hourlyRate / 60) * $dtrLateMinutes;

            $dtrUndertimeDeduction =
                ($hourlyRate / 60) * $dtrUndertimeMinutes;

            $lateDeduction += $dtrLateDeduction;
            $undertimeDeduction += $dtrUndertimeDeduction;

            /*
             * Rate-specific breakdown.
             * Use the effective rate ID as the grouping key.
             */
            $key = (string) $payRate->id;

            if (!isset($rateBreakdown[$key])) {
                $rateBreakdown[$key] = [
                    'pay_rate_id' => $payRate->id,
                    'region_id' => $area->region_id,
                    'daily_rate' => $dailyRate,
                    'hourly_rate' => round($hourlyRate, 6),
                    'days_worked' => 0,
                    'regular_hours' => 0,
                    'overtime_hours' => 0,
                    'special_overtime_hours' => 0,
                    'regular_holiday_overtime_hours' => 0,
                    'night_diff_hours' => 0,
                    'basic_pay' => 0,
                    'regular_overtime_pay' => 0,
                    'special_overtime_pay' => 0,
                    'regular_holiday_overtime_pay' => 0,
                    'night_diff_pay' => 0,
                    'holiday_pay' => 0,
                    'sp_holiday_pay' => 0,
                    'late_minutes' => 0,
                    'undertime_minutes' => 0,
                    'late_deduction' => 0,
                    'undertime_deduction' => 0,
                ];
            }

            $rateBreakdown[$key]['days_worked'] +=
                ($dtr->time_in && $dtr->time_out) ? 1 : 0;

            $rateBreakdown[$key]['regular_hours'] += $dtrRegularHours;
            $rateBreakdown[$key]['overtime_hours'] +=
                ($dtrRegularOvertimePay > 0) ? $totalOvertimeHours : 0;
            $rateBreakdown[$key]['special_overtime_hours'] +=
                ($dtrSpecialOvertimePay > 0) ? $totalOvertimeHours : 0;
            $rateBreakdown[$key]['regular_holiday_overtime_hours'] +=
                ($dtrRegularHolidayOvertimePay > 0) ? $totalOvertimeHours : 0;
            $rateBreakdown[$key]['night_diff_hours'] += $dtrNightDiffHours;

            $rateBreakdown[$key]['basic_pay'] += $dtrBasicPay;
            $rateBreakdown[$key]['regular_overtime_pay'] += $dtrRegularOvertimePay;
            $rateBreakdown[$key]['special_overtime_pay'] += $dtrSpecialOvertimePay;
            $rateBreakdown[$key]['regular_holiday_overtime_pay'] +=
                $dtrRegularHolidayOvertimePay;
            $rateBreakdown[$key]['night_diff_pay'] += $dtrNightDiffPay;
            $rateBreakdown[$key]['holiday_pay'] +=
                $dtr->is_holiday ? $holidayAmount : 0;
            $rateBreakdown[$key]['sp_holiday_pay'] +=
                (!$dtr->is_holiday && $dtr->special_holiday)
                    ? $specialHolidayAmount
                    : 0;

            $rateBreakdown[$key]['late_minutes'] += $dtrLateMinutes;
            $rateBreakdown[$key]['undertime_minutes'] += $dtrUndertimeMinutes;
            $rateBreakdown[$key]['late_deduction'] += $dtrLateDeduction;
            $rateBreakdown[$key]['undertime_deduction'] += $dtrUndertimeDeduction;
        }

        $overallPay =
            $basicPay
            + $regularOvertimePay
            + $specialOvertimePay
            + $regularHolidayOvertimePay
            + $nightDiffPay
            + $holidayPay
            + $spHolidayPay;

        // Round the breakdown before returning it.
        foreach ($rateBreakdown as &$breakdown) {
            foreach ([
                         'regular_hours',
                         'overtime_hours',
                         'special_overtime_hours',
                         'regular_holiday_overtime_hours',
                         'night_diff_hours',
                         'late_minutes',
                         'undertime_minutes',
                     ] as $field) {
                $breakdown[$field] = round($breakdown[$field], 2);
            }

            foreach ([
                         'basic_pay',
                         'regular_overtime_pay',
                         'special_overtime_pay',
                         'regular_holiday_overtime_pay',
                         'night_diff_pay',
                         'holiday_pay',
                         'sp_holiday_pay',
                         'late_deduction',
                         'undertime_deduction',
                     ] as $field) {
                $breakdown[$field] = round($breakdown[$field], 2);
            }
        }
        unset($breakdown);

        return [
            'earnings' => [
                'basic_pay' => round($basicPay, 2),
                'regular_overtime_pay' => round($regularOvertimePay, 2),
                'night_diff_pay' => round($nightDiffPay, 2),
                'holiday_pay' => round($holidayPay, 2),
                'sp_holiday_pay' => round($spHolidayPay, 2),
                'special_overtime_pay' => round($specialOvertimePay, 2),
                'regular_holiday_overtime_pay' => round($regularHolidayOvertimePay, 2),
                'overall_pay' => round($overallPay, 2),
            ],

            'deductions' => [
                'late' => round($lateDeduction, 2),
                'CB' => 75.00,
                'undertime' => round($undertimeDeduction, 2),
            ],

            'metrics' => [
                'days_worked' => $daysWorked,
                'regular_hours' => round($regularHours, 2),
                'overtime_hours' => round($overtimeHours, 2),
                'special_overtime_hours' => round($specialOvertimeHours, 2),
                'regular_holiday_overtime_hours' => round($regularHolidayOvertimeHours, 2),
                'night_diff_hours' => round($nightDiffHours, 2),
                'late_minutes' => round($lateMinutes, 2),
                'undertime_minutes' => round($undertimeMinutes, 2),

                // Reporting averages only; do not use these averages
                // to reconstruct pay items for mixed-rate periods.
                'daily_rate' => $rateCount
                    ? round($dailyRateTotal / $rateCount, 2)
                    : 0,

                'hourly_rate' => $rateCount
                    ? round($hourlyRateTotal / $rateCount, 2)
                    : 0,
            ],

            'rate_breakdown' => array_values($rateBreakdown),
        ];
    }
}
