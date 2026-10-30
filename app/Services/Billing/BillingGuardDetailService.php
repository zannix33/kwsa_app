<?php

namespace App\Services\Billing;

use App\Models\DailyTimeRecord;
use App\Models\PayRate;
use App\Models\PayrollPeriod;
use Illuminate\Support\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Validation\ValidationException;

/**
 * Builds the guard-by-guard data used by the Excel-style billing sheet.
 * This class reads attendance and effective regional daily rates only;
 * it does not create or modify payroll records.
 */
class BillingGuardDetailService
{
    /**
     * Return rows grouped as area -> branch -> guards for a closed payroll period.
     */
    public function buildForPeriod(PayrollPeriod $period): array
    {
        if (strtolower((string) $period->status) !== 'closed') {
            throw ValidationException::withMessages([
                'payroll_period_id' => 'Billing can only be prepared after the payroll period is closed.',
            ]);
        }

        $payrollIds = $period->payrolls()->pluck('id');

        if ($payrollIds->isEmpty()) {
            throw ValidationException::withMessages([
                'payroll_period_id' => 'No payroll records were found for this period.',
            ]);
        }

        $dtrs = DailyTimeRecord::query()
            ->whereIn('payroll_id', $payrollIds)
            ->whereBetween('work_date', [$period->date_from, $period->date_to])
            ->with(['user', 'area.region', 'branch.area.region', 'branch.company'])
            ->orderBy('area_id')
            ->orderBy('branch_id')
            ->orderBy('user_id')
            ->orderBy('work_date')
            ->get();

        if ($dtrs->isEmpty()) {
            throw ValidationException::withMessages([
                'payroll_period_id' => 'No payroll-linked daily time records were found for this period.',
            ]);
        }

        $areas = [];
        $unassignedBranches = [];

        foreach ($dtrs as $dtr) {
            $area = $dtr->area ?: optional($dtr->branch)->area;
            $branch = $dtr->branch;

            if (!$area || !$area->region_id) {
                throw ValidationException::withMessages([
                    'billing' => "DTR #{$dtr->id} has no area with a regional assignment.",
                ]);
            }

            if (!$branch) {
                throw ValidationException::withMessages([
                    'billing' => "DTR #{$dtr->id} has no branch assignment; billing rows require a branch.",
                ]);
            }

            if (empty($branch->company_id)) {
                $unassignedBranches[$area->id.'|'.$branch->id] = ['area_name' => $area->name, 'branch_name' => $branch->name];
                continue;
            }

            if (!$dtr->user) {
                throw ValidationException::withMessages([
                    'billing' => "DTR #{$dtr->id} has no assigned guard/user.",
                ]);
            }

            $workDate = Carbon::parse($dtr->work_date)->toDateString();
            $rate = $this->regionalRateForDate((int) $area->region_id, $workDate);
            $dailyRate = (float) $rate->daily_rate;
            $hourlyRate = $dailyRate / 8;

            $areaKey = (string) $area->id;
            $branchKey = (string) $branch->id;
            $guardKey = (string) $dtr->user_id;

            if (!isset($areas[$areaKey])) {
                $areas[$areaKey] = [
                    'area_id' => $area->id,
                    'area_name' => $area->name,
                    'client_id' => $area->client_id,
                    'client_name' => optional($area->client)->name,
                    'region_id' => $area->region_id,
                    'branches' => [],
                ];
            }

            if (!isset($areas[$areaKey]['branches'][$branchKey])) {
                $areas[$areaKey]['branches'][$branchKey] = [
                    'branch_id' => $branch->id,
                    'branch_name' => $branch->name,
                    'company_id' => $branch->company_id,
                    'company_name' => optional($branch->company)->name,
                    'guards' => [],
                ];
            }

            if (!isset($areas[$areaKey]['branches'][$branchKey]['guards'][$guardKey])) {
                $areas[$areaKey]['branches'][$branchKey]['guards'][$guardKey] = [
                    'user_id' => $dtr->user_id,
                    'guard_name' => $dtr->user->name,
                    'days' => [],
                    'regular_hours' => 0.0,
                    'regular_ot_hours' => 0.0,
                    'sunday_special_ot_hours' => 0.0,
                    'regular_holiday_ot_hours' => 0.0,
                    'regular_nd_hours' => 0.0,
                    'sunday_special_nd_hours' => 0.0,
                    'legal_holiday_nd_hours' => 0.0,
                    'late_hours' => 0.0,
                    'undertime_hours' => 0.0,
                    'regional_rates_by_date' => [],
                    'dtr_ids' => [],
                ];
            }

            $guard =& $areas[$areaKey]['branches'][$branchKey]['guards'][$guardKey];
            $dayKey = $workDate;
            $attended = $dtr->time_in && $dtr->time_out;

            // Count each attended calendar date once per guard/branch.
            if (!isset($guard['days'][$dayKey])) {
                $guard['days'][$dayKey] = [
                    'work_date' => $workDate,
                    'attended' => (bool) $attended,
                    'daily_rate' => $dailyRate,
                    'pay_rate_id' => $rate->id,
                    'regular_hours' => 0.0,
                    'overtime_hours' => 0.0,
                    'extended_hours' => 0.0,
                    'night_differential_hours' => 0.0,
                    'late_hours' => 0.0,
                    'undertime_hours' => 0.0,
                    'is_holiday' => (bool) $dtr->is_holiday,
                    'special_holiday' => (bool) $dtr->special_holiday,
                    'is_rest_day' => (bool) $dtr->is_rest_day,
                ];
            } else {
                // A duplicate DTR date must not double-count the attended day.
                $guard['days'][$dayKey]['attended'] =
                    $guard['days'][$dayKey]['attended'] || (bool) $attended;
            }

            $regularHours = (float) ($dtr->regular_hours ?? 0);
            $otHours = (float) ($dtr->overtime_hours ?? 0);
            $extendedHours = (float) ($dtr->extended_hours ?? 0);
            $ndHours = (float) ($dtr->night_differential_hours ?? 0);
            $lateHours = (float) ($dtr->late_hours ?? 0);
            $undertimeHours = (float) ($dtr->undertime_hours ?? 0);
            $isSunday = Carbon::parse($workDate)->isSunday();
            $totalOt = $otHours + $extendedHours;

            $guard['days'][$dayKey]['regular_hours'] += $regularHours;
            $guard['days'][$dayKey]['overtime_hours'] += $totalOt;
            $guard['days'][$dayKey]['extended_hours'] += $extendedHours;
            $guard['days'][$dayKey]['night_differential_hours'] += $ndHours;
            $guard['days'][$dayKey]['late_hours'] += $lateHours;
            $guard['days'][$dayKey]['undertime_hours'] += $undertimeHours;

            $guard['regular_hours'] += $regularHours;
            if ($dtr->is_holiday) {
                $guard['regular_holiday_ot_hours'] += $totalOt;
                $guard['legal_holiday_nd_hours'] += $ndHours;
            } elseif ($isSunday || $dtr->special_holiday || $dtr->is_rest_day) {
                $guard['sunday_special_ot_hours'] += $totalOt;
                $guard['sunday_special_nd_hours'] += $ndHours;
            } else {
                $guard['regular_ot_hours'] += $totalOt;
                $guard['regular_nd_hours'] += $ndHours;
            }
            $guard['late_hours'] += $lateHours;
            $guard['undertime_hours'] += $undertimeHours;
            $guard['regional_rates_by_date'][$workDate] = [
                'pay_rate_id' => $rate->id,
                'daily_rate' => round($dailyRate, 2),
                'hourly_rate' => round($hourlyRate, 6),
            ];
            $guard['dtr_ids'][] = $dtr->id;
            unset($guard);
        }

        // Convert keyed structures to arrays and compute rate-based amounts.
        foreach ($areas as &$areaData) {
            $areaData['branches'] = array_values($areaData['branches']);
            foreach ($areaData['branches'] as &$branchData) {
                $branchData['guards'] = array_values($branchData['guards']);
                foreach ($branchData['guards'] as &$guardData) {
                    $days = array_values($guardData['days']);
                    $guardData['attended_days'] = count(array_filter($days, function ($day) {
                        return $day['attended'];
                    }));
                    $guardData['days'] = $days;
                    $guardData['regular_hours'] = round($guardData['regular_hours'], 2);
                    foreach ([
                        'regular_ot_hours', 'sunday_special_ot_hours',
                        'regular_holiday_ot_hours', 'regular_nd_hours',
                        'sunday_special_nd_hours', 'legal_holiday_nd_hours',
                        'late_hours', 'undertime_hours',
                    ] as $field) {
                        $guardData[$field] = round($guardData[$field], 2);
                    }
                    $guardData['dtr_ids'] = array_values(array_unique($guardData['dtr_ids']));
                }
                unset($guardData);
            }
            unset($branchData);
        }
        unset($areaData);

        return [
            'period' => [
                'id' => $period->id,
                'name' => $period->name,
                'date_from' => Carbon::parse($period->date_from)->toDateString(),
                'date_to' => Carbon::parse($period->date_to)->toDateString(),
            ],
            'areas' => array_values($areas),
            'unassigned_branches' => array_values($unassignedBranches),
        ];
    }

    protected function regionalRateForDate(int $regionId, string $workDate): PayRate
    {
        $rate = PayRate::query()
            ->where('region_id', $regionId)
            ->whereDate('effective_from', '<=', $workDate)
            ->where(function ($query) use ($workDate) {
                $query->whereNull('effective_to')
                    ->orWhereDate('effective_to', '>=', $workDate);
            })
            ->orderByDesc('effective_from')
            ->first();

        if (!$rate) {
            throw ValidationException::withMessages([
                'billing' => "No regional pay rate exists for region {$regionId} on {$workDate}.",
            ]);
        }

        return $rate;
    }
}
