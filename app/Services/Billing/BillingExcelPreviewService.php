<?php

namespace App\Services\Billing;

use App\Models\PayrollPeriod;

/** Read-only billing projection; never modifies payroll, DTR or billing records. */
class BillingExcelPreviewService
{
    private BillingGuardBillingDataService $source;

    public function __construct(BillingGuardBillingDataService $source)
    {
        $this->source = $source;
    }

    public function build(PayrollPeriod $period): array
    {
        $data = $this->source->buildForPeriod($period); // validates closed period
        $data['areas'] = array_map(function ($area) {
            $area['branches'] = array_values(array_filter($area['branches'], function ($branch) { return !empty($branch['company_id']); }));
            return $area;
        }, $data['areas']);
        foreach ($data['areas'] as &$area) {
            $area['billing_totals'] = $this->zeroTotals();
            foreach ($area['branches'] as &$branch) {
                $branch['billing_totals'] = $this->zeroTotals();
                foreach ($branch['guards'] as &$guard) {
                    $daily = (float) $guard['billing_daily_rate'];
                    $monthly = (float) $guard['contract_cost']['contract_cost_excluding_retirement'];
                    $retirement = (float) $guard['contract_cost']['retirement_contribution'];
                    $halfMonth = $monthly / 2;
                    $regularOt = (float) $guard['regular_ot_hours'] * ($daily / 8 * 1.25);
                    $specialOt = (float) $guard['sunday_special_ot_hours'] * ($daily / 8 * 1.69);
                    $holidayOt = (float) $guard['regular_holiday_ot_hours'] * ($daily / 8 * 2.60);
                    $regularNd = (float) $guard['regular_nd_hours'] * ($daily / 8 * 1.25 * .10);
                    $specialNd = (float) $guard['sunday_special_nd_hours'] * ($daily / 8 * 1.69 * .10);
                    $holidayNd = (float) $guard['legal_holiday_nd_hours'] * ($daily / 8 * 2.60 * .10);
                    // Workbook T13 = (monthly contract + retirement + 20% agency fee)
                    //                * 12 / 365 / 8. Keep the workbook's basis.
                    $deductionHourly = ($monthly + $retirement + $monthly * .20) * 12 / 365 / 8;
                    $deductionHours = (float) $guard['late_hours'] + (float) $guard['undertime_hours'];
                    $deductions = $deductionHours * $deductionHourly;
                    // Additional posts, penalties and the workbook's separate 5%-of-fee
                    // adjustment require approved billing inputs; they are NOT inferred.
                    $additionalPostDays = 0.0;
                    $penaltyHours = 0.0;
                    $additionalPostAmount = $additionalPostDays * ($monthly * 12 / 365);
                    $beforeFee = $halfMonth + $additionalPostAmount + $regularOt + $specialOt + $holidayOt
                        + $regularNd + $specialNd + $holidayNd - $deductions;
                    $fee = $beforeFee * .20;
                    $ewt = $fee * .02;
                    $retirementHalf = $retirement / 2;
                    $gross = $beforeFee + $fee - $ewt + $retirementHalf;
                    $penaltyAmount = $penaltyHours * 200;
                    $net = $gross - $penaltyAmount;
                    $guard['billing'] = [
                        'guard_count' => 1,
                        'half_month_rate' => round($halfMonth, 2),
                        'additional_post_days' => $additionalPostDays,
                        'additional_post_amount' => round($additionalPostAmount, 2),
                        'regular_ot' => round($regularOt, 2),
                        'special_ot' => round($specialOt, 2),
                        'holiday_ot' => round($holidayOt, 2),
                        'regular_nd' => round($regularNd, 2),
                        'special_nd' => round($specialNd, 2),
                        'holiday_nd' => round($holidayNd, 2),
                        'deduction_hours' => round($deductionHours, 2),
                        'deductions' => round($deductions, 2),
                        'before_fee' => round($beforeFee, 2),
                        'agency_fee' => round($fee, 2),
                        'vat' => 0.0,
                        'ewt' => round($ewt, 2),
                        'retirement_half' => round($retirementHalf, 2),
                        'gross' => round($gross, 2),
                        'penalty_hours' => $penaltyHours,
                        'penalty_amount' => round($penaltyAmount, 2),
                        'net_payable' => round($net, 2),
                    ];
                    foreach ($guard['billing'] as $key => $value) {
                        if (!isset($area['billing_totals'][$key])) continue;
                        $area['billing_totals'][$key] += $value;
                        $branch['billing_totals'][$key] += $value;
                    }
                }
            }
        }
        unset($area, $branch, $guard);
        return $data;
    }

    private function zeroTotals(): array
    {
        return array_fill_keys([
            'guard_count', 'half_month_rate', 'additional_post_amount', 'regular_ot',
            'special_ot', 'holiday_ot', 'regular_nd', 'special_nd', 'holiday_nd',
            'deductions', 'before_fee', 'agency_fee', 'vat', 'ewt', 'retirement_half',
            'gross', 'penalty_amount', 'net_payable',
        ], 0);
    }
}
