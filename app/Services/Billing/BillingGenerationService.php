<?php

namespace App\Services\Billing;

use App\Models\Area;
use App\Models\Billing;
use App\Models\BillingItem;
use App\Models\DailyTimeRecord;
use App\Models\Payroll;
use App\Models\PayrollPeriod;
use App\Models\PayRate;
use Carbon\Carbon;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

class BillingGenerationService
{
    /**
     * Generate one draft bill per area for a closed payroll period.
     *
     * Important: this service reads payroll-linked DTRs but does not modify
     * payrolls, payroll items, or DTRs.
     */
    public function generate(PayrollPeriod $period): Collection
    {
        if (strtolower(trim((string) $period->status)) !== 'closed') {
            throw ValidationException::withMessages([
                'billing' => 'Billing can only be generated for a Closed payroll period.',
            ]);
        }

        return DB::transaction(function () use ($period) {
            $payrollIds = Payroll::where('payroll_period_id', $period->id)
                ->pluck('id');

            if ($payrollIds->isEmpty()) {
                throw ValidationException::withMessages([
                    'billing' => 'No payroll records were found for this payroll period.',
                ]);
            }

            $dtrs = DailyTimeRecord::with(['area', 'branch.area', 'user'])
                ->whereIn('payroll_id', $payrollIds)
                ->whereDate('work_date', '>=', Carbon::parse($period->date_from)->toDateString())
                ->whereDate('work_date', '<=', Carbon::parse($period->date_to)->toDateString())
                ->orderBy('work_date')
                ->get();

            if ($dtrs->isEmpty()) {
                throw ValidationException::withMessages([
                    'billing' => 'No payroll-linked DTRs were found for this payroll period.',
                ]);
            }

            // A DTR may have a direct area assignment or inherit its branch area.
            $dtrsByArea = $dtrs->groupBy(function ($dtr) {
                if ($dtr->area_id) {
                    return (string) $dtr->area_id;
                }

                if ($dtr->branch && $dtr->branch->area_id) {
                    return (string) $dtr->branch->area_id;
                }

                if ($dtr->area) {
                    return (string) $dtr->area->id;
                }

                if ($dtr->branch && $dtr->branch->area) {
                    return (string) $dtr->branch->area->id;
                }

                return 'missing-area';
            });

            if ($dtrsByArea->has('missing-area')) {
                throw ValidationException::withMessages([
                    'billing' => 'Some payroll-linked DTRs have no area assignment. Assign an area or branch before generating billing.',
                ]);
            }

            $created = collect();

            foreach ($dtrsByArea as $areaId => $areaDtrs) {
                $area = Area::find($areaId);

                if (!$area || !$area->client_id) {
                    throw ValidationException::withMessages([
                        'billing' => "Area ID {$areaId} does not have a valid client.",
                    ]);
                }

                if (Billing::where('area_id', $area->id)
                    ->where('payroll_period_id', $period->id)
                    ->exists()) {
                    throw ValidationException::withMessages([
                        'billing' => "A billing record already exists for area '{$area->name}' in this payroll period.",
                    ]);
                }

                $created->push($this->createAreaBilling($area, $period, $areaDtrs));
            }

            return $created;
        });
    }

    protected function createAreaBilling(
        Area $area,
        PayrollPeriod $period,
        Collection $dtrs
    ): Billing {
        if (!$area->region_id) {
            throw ValidationException::withMessages([
                'billing' => "Area '{$area->name}' has no region assigned, so its regional pay rate cannot be determined.",
            ]);
        }

        $guards = $dtrs->filter(function ($dtr) {
            return !empty($dtr->user_id);
        })->pluck('user_id')->unique()->values();

        if ($guards->isEmpty()) {
            throw ValidationException::withMessages([
                'billing' => "No employees were found in area '{$area->name}' for this period.",
            ]);
        }

        // Resolve the same region-specific daily rate used by payroll, effective
        // on each DTR work date. Billing does not use Area.rate or the legacy
        // payroll_rate accessor.
        $rateByDtr = [];
        $baseAmount = 0.0;
        $guardDays = [];
        $weightedDailyRateTotal = 0.0;
        $regularOtHours = 0.0;
        $specialOtHours = 0.0;
        $regularHolidayOtHours = 0.0;
        $nightDiffHours = 0.0;
        $lateHours = 0.0;
        $undertimeHours = 0.0;
        $regularOtAmount = 0.0;
        $specialOtAmount = 0.0;
        $regularHolidayOtAmount = 0.0;
        $nightDiffAmount = 0.0;
        $lateAmount = 0.0;
        $undertimeAmount = 0.0;

        foreach ($dtrs as $dtr) {
            if (!$dtr->work_date) {
                throw ValidationException::withMessages([
                    'billing' => "DTR ID {$dtr->id} has no work date, so its regional rate cannot be determined.",
                ]);
            }

            $workDate = Carbon::parse($dtr->work_date)->toDateString();
            $payRate = PayRate::where('region_id', $area->region_id)
                ->whereDate('effective_from', '<=', $workDate)
                ->where(function ($query) use ($workDate) {
                    $query->whereNull('effective_to')
                        ->orWhereDate('effective_to', '>=', $workDate);
                })
                ->orderByDesc('effective_from')
                ->first();

            if (!$payRate || (float) $payRate->daily_rate <= 0) {
                throw ValidationException::withMessages([
                    'billing' => "No valid regional daily pay rate exists for area '{$area->name}' on {$workDate}.",
                ]);
            }

            $dailyRate = (float) $payRate->daily_rate;
            $hourlyRate = $dailyRate / 8;
            $rateByDtr[$dtr->id] = [
                'daily_rate' => $dailyRate,
                'hourly_rate' => $hourlyRate,
                'pay_rate_id' => $payRate->id,
                'work_date' => $workDate,
            ];

            // Option B: charge the daily regional rate for each attended
            // guard-day. Count a user/date only once to avoid duplicate DTRs
            // inflating the base contract charge.
            if ($dtr->user_id && $dtr->time_in && $dtr->time_out) {
                $guardDayKey = $dtr->user_id . '|' . $workDate;
                if (!isset($guardDays[$guardDayKey])) {
                    $guardDays[$guardDayKey] = true;
                    $baseAmount += $dailyRate;
                    $weightedDailyRateTotal += $dailyRate;
                }
            }

            $otHours = (float) ($dtr->overtime_hours ?? 0)
                + (float) ($dtr->extended_hours ?? 0);

            $workDateCarbon = Carbon::parse($workDate);
            if ($otHours > 0 && $dtr->is_holiday) {
                $regularHolidayOtHours += $otHours;
                $regularHolidayOtAmount += $otHours * $hourlyRate
                    * (float) $area->billing_regular_holiday_ot_multiplier;
            } elseif ($otHours > 0 && (
                $workDateCarbon->isSunday() ||
                (bool) $dtr->special_holiday ||
                (bool) $dtr->is_rest_day
            )) {
                $specialOtHours += $otHours;
                $specialOtAmount += $otHours * $hourlyRate
                    * (float) $area->billing_special_ot_multiplier;
            } else {
                $regularOtHours += $otHours;
                $regularOtAmount += $otHours * $hourlyRate
                    * (float) $area->billing_regular_ot_multiplier;
            }

            $dtrNightDiffHours = (float) ($dtr->night_differential_hours ?? 0);
            $dtrLateHours = (float) ($dtr->late_hours ?? 0);
            $dtrUndertimeHours = (float) ($dtr->undertime_hours ?? 0);

            $nightDiffHours += $dtrNightDiffHours;
            $lateHours += $dtrLateHours;
            $undertimeHours += $dtrUndertimeHours;
            $nightDiffAmount += $dtrNightDiffHours * $hourlyRate
                * (float) $area->billing_night_diff_percent;
            $lateAmount += $dtrLateHours * $hourlyRate;
            $undertimeAmount += $dtrUndertimeHours * $hourlyRate;
        }

        $guardDayCount = count($guardDays);
        if ($guardDayCount === 0) {
            throw ValidationException::withMessages([
                'billing' => "No attended guard-days were found for area '{$area->name}' in this payroll period.",
            ]);
        }

        $baseAmount = round($baseAmount, 2);
        $regularOtAmount = round($regularOtAmount, 2);
        $specialOtAmount = round($specialOtAmount, 2);
        $regularHolidayOtAmount = round($regularHolidayOtAmount, 2);
        $nightDiffAmount = round($nightDiffAmount, 2);
        $lateAmount = round($lateAmount, 2);
        $undertimeAmount = round($undertimeAmount, 2);
        $averageDailyRate = $weightedDailyRateTotal / $guardDayCount;
        $averageHourlyRate = $averageDailyRate / 8;

        // Additional posts and absences are not auto-computed yet because the
        // current DTR schema has no post-count or scheduled-guard-days field.
        $subtotal = round(
            $baseAmount
            + $regularOtAmount
            + $specialOtAmount
            + $regularHolidayOtAmount
            + $nightDiffAmount
            - $lateAmount
            - $undertimeAmount,
            2
        );

        $agencyFee = round($subtotal * (float) $area->billing_agency_fee_percent, 2);

        $retirementAmount = $area->billing_retirement_fund_amount !== null
            ? (float) $area->billing_retirement_fund_amount
            : round($subtotal * (float) ($area->billing_retirement_fund_percent ?? 0), 2);

        $penalties = round((float) ($area->billing_penalty_amount ?? 0), 2);
        $ewt = round($agencyFee * (float) $area->billing_ewt_percent, 2);

        if ($area->billing_tax_type && strtolower($area->billing_tax_type) !== 'non_vat') {
            throw ValidationException::withMessages([
                'billing' => "VAT calculation is not configured for area '{$area->name}'. This service currently supports non-VAT billing only.",
            ]);
        }

        $total = round($subtotal + $agencyFee + $retirementAmount + $penalties - $ewt, 2);

        $billing = Billing::create([
            'client_id' => $area->client_id,
            'area_id' => $area->id,
            'payroll_period_id' => $period->id,
            'status' => 'draft',
            'subtotal' => $subtotal,
            'agency_fee_amount' => $agencyFee,
            'retirement_fund_amount' => $retirementAmount,
            'penalties_amount' => $penalties,
            'ewt_amount' => $ewt,
            'vat_amount' => 0,
            'total_amount' => $total,
            'tax_type' => $area->billing_tax_type ?: 'non_vat',
            'billing_date' => now()->toDateString(),
            'calculation_snapshot' => [
                'source' => 'payroll-linked DTRs',
                'payroll_period_id' => $period->id,
                'area_id' => $area->id,
                'guard_count' => $guards->count(),
                'area_pay_rate_source' => 'PayRate.daily_rate selected by area.region_id and each DTR work_date, matching PayrollCalculationService',
                'base_billing_formula' => 'sum of regional daily_rate for each distinct attended user/work_date (guard-day)',
                'guard_day_count' => $guardDayCount,
                'average_daily_rate_for_display' => round($averageDailyRate, 2),
                'average_hourly_rate_for_display' => round($averageHourlyRate, 4),
                'regional_rates_by_dtr' => $rateByDtr,
                'regular_ot_multiplier' => (float) $area->billing_regular_ot_multiplier,
                'special_ot_multiplier' => (float) $area->billing_special_ot_multiplier,
                'regular_holiday_ot_multiplier' => (float) $area->billing_regular_holiday_ot_multiplier,
                'night_diff_percent' => (float) $area->billing_night_diff_percent,
                'agency_fee_percent' => (float) $area->billing_agency_fee_percent,
                'ewt_percent' => (float) $area->billing_ewt_percent,
                'note' => 'Base uses actual attended guard-days. Additional posts and absence deductions are not included because the current DTR schema does not capture the required billing inputs.',
            ],
        ]);

        $this->addItem($billing, 'BASE_CONTRACT', 'charge',
            'Regional daily rate × attended guard-days', $guardDayCount, 'guard-days',
            $averageDailyRate, $baseAmount);

        $this->addItem($billing, 'REGULAR_OT', 'charge',
            'Regular overtime', $regularOtHours, 'hours',
            $regularOtHours > 0 ? $regularOtAmount / $regularOtHours : 0, $regularOtAmount);

        $this->addItem($billing, 'SPECIAL_OT', 'charge',
            'Sunday / special-holiday overtime', $specialOtHours, 'hours',
            $specialOtHours > 0 ? $specialOtAmount / $specialOtHours : 0, $specialOtAmount);

        $this->addItem($billing, 'REGULAR_HOLIDAY_OT', 'charge',
            'Regular/legal holiday overtime', $regularHolidayOtHours, 'hours',
            $regularHolidayOtHours > 0 ? $regularHolidayOtAmount / $regularHolidayOtHours : 0, $regularHolidayOtAmount);

        $this->addItem($billing, 'NIGHT_DIFF', 'charge',
            'Night differential', $nightDiffHours, 'hours',
            $nightDiffHours > 0 ? $nightDiffAmount / $nightDiffHours : 0, $nightDiffAmount);

        $this->addItem($billing, 'LATE', 'deduction',
            'Late deduction', $lateHours, 'hours',
            $averageHourlyRate, -$lateAmount);

        $this->addItem($billing, 'UNDERTIME', 'deduction',
            'Undertime deduction', $undertimeHours, 'hours',
            $averageHourlyRate, -$undertimeAmount);

        $this->addItem($billing, 'AGENCY_FEE', 'charge',
            'Agency fee', 1, 'subtotal percentage',
            (float) $area->billing_agency_fee_percent, $agencyFee);

        if ($retirementAmount != 0.0) {
            $this->addItem($billing, 'RETIREMENT_FUND', 'charge',
                'Retirement fund', 1, 'configured amount/percentage',
                $retirementAmount, $retirementAmount);
        }

        if ($penalties != 0.0) {
            $this->addItem($billing, 'PENALTY', 'charge',
                'Penalty', 1, 'fixed amount',
                $penalties, $penalties);
        }

        $this->addItem($billing, 'EWT', 'deduction',
            'Expanded withholding tax', 1, 'agency fee percentage',
            (float) $area->billing_ewt_percent, -$ewt);

        return $billing->load('items', 'client', 'area', 'payrollPeriod');
    }

    protected function addItem(
        Billing $billing,
        string $code,
        string $type,
        string $description,
        float $quantity,
        string $unit,
        float $rate,
        float $amount
    ): void {
        if (abs($amount) < 0.005 && abs($quantity) < 0.005) {
            return;
        }

        BillingItem::create([
            'billing_id' => $billing->id,
            'code' => $code,
            'type' => $type,
            'description' => $description,
            'quantity' => round($quantity, 2),
            'unit' => $unit,
            'rate' => round($rate, 4),
            'amount' => round($amount, 2),
        ]);
    }
}
