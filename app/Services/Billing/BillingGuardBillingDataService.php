<?php

namespace App\Services\Billing;

use App\Models\PayrollPeriod;

/**
 * Combines guard-level attendance/rate details with contract-cost components.
 * Read-only with respect to payroll and DTR records.
 */
class BillingGuardBillingDataService
{
    protected BillingGuardDetailService $details;
    protected BillingContractCostCalculator $costs;

    public function __construct(
        BillingGuardDetailService $details,
        BillingContractCostCalculator $costs
    ) {
        $this->details = $details;
        $this->costs = $costs;
    }

    /** Return area -> branch -> guard rows with regional rate and cost columns. */
    public function buildForPeriod(PayrollPeriod $period): array
    {
        $result = $this->details->buildForPeriod($period);
        $areaTotals = [];

        foreach ($result['areas'] as &$area) {
            $area['totals'] = [
                'guard_count' => 0,
                'attended_guard_days' => 0,
                'contract_cost_excluding_retirement' => 0.0,
                'retirement_contribution' => 0.0,
                'contract_cost_including_retirement' => 0.0,
            ];

            foreach ($area['branches'] as &$branch) {
                $branch['totals'] = [
                    'guard_count' => 0,
                    'attended_guard_days' => 0,
                    'contract_cost_excluding_retirement' => 0.0,
                    'retirement_contribution' => 0.0,
                    'contract_cost_including_retirement' => 0.0,
                ];

                foreach ($branch['guards'] as &$guard) {
                    // If regional rates changed during the period, retain the
                    // daily rates in detail and use the attended-day-weighted
                    // average for this period's per-guard cost schedule.
                    $rateByDate = [];
                    foreach ($guard['days'] as $day) {
                        if (!empty($day['attended'])) {
                            $rateByDate[$day['work_date']] = (float) $day['daily_rate'];
                        }
                    }
                    if (!$rateByDate) {
                        foreach ($guard['regional_rates_by_date'] as $rate) {
                            $rateByDate[] = (float) $rate['daily_rate'];
                        }
                    }
                    if (!$rateByDate) {
                        throw new \InvalidArgumentException('No regional daily rate is available for guard ID '.$guard['user_id'].'.');
                    }

                    $dailyRate = array_sum($rateByDate) / count($rateByDate);
                    $guard['contract_cost'] = $this->costs->calculate($dailyRate);
                    $guard['billing_daily_rate'] = round($dailyRate, 2);
                    $guard['billing_hourly_rate'] = round($dailyRate / 8, 6);

                    $area['totals']['guard_count']++;
                    $branch['totals']['guard_count']++;
                    $area['totals']['attended_guard_days'] += (int) $guard['attended_days'];
                    $branch['totals']['attended_guard_days'] += (int) $guard['attended_days'];
                    foreach (['contract_cost_excluding_retirement', 'retirement_contribution', 'contract_cost_including_retirement'] as $key) {
                        $amount = (float) $guard['contract_cost'][$key];
                        $area['totals'][$key] += $amount;
                        $branch['totals'][$key] += $amount;
                    }
                }
                unset($guard);
                foreach (['contract_cost_excluding_retirement', 'retirement_contribution', 'contract_cost_including_retirement'] as $key) {
                    $branch['totals'][$key] = round($branch['totals'][$key], 2);
                }
            }
            unset($branch);
            foreach (['contract_cost_excluding_retirement', 'retirement_contribution', 'contract_cost_including_retirement'] as $key) {
                $area['totals'][$key] = round($area['totals'][$key], 2);
            }
        }
        unset($area);

        return $result;
    }
}
