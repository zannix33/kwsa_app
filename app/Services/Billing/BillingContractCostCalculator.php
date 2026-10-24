<?php

namespace App\Services\Billing;

/**
 * Calculates the per-guard contract-cost components from the regional daily
 * wage rate. This is billing-side math only; it does not write payroll data.
 *
 * Business rules supplied by the user:
 * - Average pay/month uses the user-confirmed 378-day factor divided by 12.
 * - 13th month retains the supplied 365 / 12 / 12 formula.
 * - SSS premium is a fixed total of PHP 2,200 per guard per month.
 * - PhilHealth is 2.5% of average monthly pay.
 * - Pag-IBIG is PHP 200 fixed.
 * - Uniform allowance is PHP 100, matching the supplied workbook example.
 */
class BillingContractCostCalculator
{
    public function calculate(float $dailyPayRate): array
    {
        if ($dailyPayRate <= 0) {
            throw new \InvalidArgumentException('The regional daily pay rate must be greater than zero.');
        }

        $averagePayMonth = $dailyPayRate * 378 / 12;
        $thirteenthMonth = $dailyPayRate * 365 / 12 / 12;
        $fiveDayIncentive = $dailyPayRate * 5 / 12;
        $retirementContribution = $dailyPayRate * 22.5 / 12;
        $sssPremium = 2200.00;
        $philHealth = $averagePayMonth * 0.025;
        $pagIbig = 200.00;
        $uniformAllowance = 100.00;

        // The subtotal excludes retirement because the workbook presents the
        // retirement fund separately. Callers can add/subtract it according
        // to the selected invoice presentation rather than counting it twice.
        $contractCostExcludingRetirement = $averagePayMonth
            + $thirteenthMonth
            + $fiveDayIncentive
            + $uniformAllowance
            + $sssPremium
            + $philHealth
            + $pagIbig;

        return [
            'daily_pay_rate' => round($dailyPayRate, 2),
            'average_pay_month' => round($averagePayMonth, 2),
            'thirteenth_month_pay' => round($thirteenthMonth, 2),
            'five_day_incentive' => round($fiveDayIncentive, 2),
            'uniform_allowance' => round($uniformAllowance, 2),
            'sss_premium' => round($sssPremium, 2),
            'philhealth' => round($philHealth, 2),
            'pagibig' => round($pagIbig, 2),
            'retirement_contribution' => round($retirementContribution, 2),
            'contract_cost_excluding_retirement' => round($contractCostExcludingRetirement, 2),
            'contract_cost_including_retirement' => round($contractCostExcludingRetirement + $retirementContribution, 2),
        ];
    }
}
