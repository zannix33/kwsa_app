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
            $area['branches'] = array_values(array_filter($area['branches'], function ($branch) {
                return !empty($branch['company_id']) && !empty($branch['guards']);
            }));
            return $area;
        }, $data['areas']);
        foreach ($data['areas'] as &$area) {
            $area['billing_totals'] = $this->zeroTotals();
            foreach ($area['branches'] as &$branch) {
                $branch['billing_totals'] = $this->zeroTotals();
                $branch['assigned_guard_count'] = max(1, (int) ($branch['assigned_guard_count'] ?? 1));

                // Retirement eligibility is ranked once across the entire payroll period
                // for this branch, not recalculated day by day.
                usort($branch['guards'], function ($a, $b) {
                    $days = (int) ($b['attended_days'] ?? 0) <=> (int) ($a['attended_days'] ?? 0);
                    if ($days !== 0) return $days;
                    $names = strcasecmp((string) ($a['guard_name'] ?? ''), (string) ($b['guard_name'] ?? ''));
                    return $names !== 0 ? $names : ((int) ($a['user_id'] ?? 0) <=> (int) ($b['user_id'] ?? 0));
                });

                $eligiblePostCount = 0;
                foreach ($branch['guards'] as &$rankedGuard) {
                    $eligible = (int) ($rankedGuard['attended_days'] ?? 0) > 0
                        && $eligiblePostCount < $branch['assigned_guard_count'];
                    $rankedGuard['is_contracted_post'] = $eligible;
                    $rankedGuard['is_additional_post'] = false;
                    $rankedGuard['retirement_rank'] = $eligible ? ++$eligiblePostCount : null;
                }
                unset($rankedGuard);

                // One additional-post guard is allowed per branch only when at
                // least two guards worked and combined attendance exceeds 15 days.
                $guardsOnDuty = count(array_filter($branch['guards'], fn ($g) => (int) ($g['attended_days'] ?? 0) > 0));
                $totalAttendedDays = array_sum(array_map(fn ($g) => (int) ($g['attended_days'] ?? 0), $branch['guards']));
                $additionalPostEligible = $guardsOnDuty >= 2 && $totalAttendedDays > 15;
                if ($additionalPostEligible) {
                    foreach ($branch['guards'] as &$candidateGuard) {
                        if (!(bool) ($candidateGuard['is_contracted_post'] ?? false)
                            && (int) ($candidateGuard['attended_days'] ?? 0) > 0) {
                            $candidateGuard['is_additional_post'] = true;
                            break; // guards are already ranked; exactly one extra guard
                        }
                    }
                    unset($candidateGuard);
                }

                foreach ($branch['guards'] as &$guard) {
                    $daily = (float) $guard['billing_daily_rate'];
                    $monthly = (float) $guard['contract_cost']['contract_cost_excluding_retirement'];
                    $retirement = (float) $guard['contract_cost']['retirement_contribution'];
                    $isContractedPost = (bool) ($guard['is_contracted_post'] ?? false);
                    $halfMonth = $isContractedPost ? $monthly / 2 : 0.0;
                    $isAdditionalPost = (bool) ($guard['is_additional_post'] ?? false);
                    $additionalPostDays = $isAdditionalPost ? (float) ($guard['attended_days'] ?? 0) : 0.0;
                    $additionalPostDailyRate = $monthly * 12 / 365; // workbook's additional-post daily rate
                    $applyOvertimePremium = (bool) ($area['apply_overtime_rate'] ?? true);
                    $applyNightDiffPremium = (bool) ($area['apply_night_diff_rate'] ?? true);
                    $regularOtMultiplier = $applyOvertimePremium ? 1.25 : 1.0;
                    $specialOtMultiplier = $applyOvertimePremium ? 1.69 : 1.0;
                    $holidayOtMultiplier = $applyOvertimePremium ? 2.60 : 1.0;
                    $regularOt = (float) $guard['regular_ot_hours'] * ($daily / 8 * $regularOtMultiplier);
                    $specialOt = (float) $guard['sunday_special_ot_hours'] * ($daily / 8 * $specialOtMultiplier);
                    $holidayOt = (float) $guard['regular_holiday_ot_hours'] * ($daily / 8 * $holidayOtMultiplier);
                    $regularNd = $applyNightDiffPremium ? (float) $guard['regular_nd_hours'] * ($daily / 8 * $regularOtMultiplier * .10) : 0;
                    $specialNd = $applyNightDiffPremium ? (float) $guard['sunday_special_nd_hours'] * ($daily / 8 * $specialOtMultiplier * .10) : 0;
                    $holidayNd = $applyNightDiffPremium ? (float) $guard['legal_holiday_nd_hours'] * ($daily / 8 * $holidayOtMultiplier * .10) : 0;
                    // Deductions follow the applicable post basis: contracted posts
                    // include retirement; additional posts do not.
                    $deductionHourly = ($monthly + ($isContractedPost ? $retirement : 0.0) + $monthly * .20) * 12 / 365 / 8;
                    $deductionHours = (float) $guard['late_hours'] + (float) $guard['undertime_hours'];
                    $deductions = $deductionHours * $deductionHourly;
                    // Additional posts are billed at the daily additional-post rate
                    // for the guard's attended days in this 15-day payroll period.
                    $penaltyHours = 0.0;
                    $additionalPostAmount = $additionalPostDays * $additionalPostDailyRate;
                    $beforeFee = $halfMonth + $additionalPostAmount + $regularOt + $specialOt + $holidayOt
                        + $regularNd + $specialNd + $holidayNd - $deductions;
                    $fee = $beforeFee * .20;
                    $ewt = $fee * .02;
                    $retirementHalf = $isContractedPost ? $retirement / 2 : 0.0;
                    $gross = $beforeFee + $fee - $ewt + $retirementHalf;
                    $penaltyAmount = $penaltyHours * 200;
                    $net = $gross - $penaltyAmount;
                    $guard['billing'] = [
                        'premium_policy' => [
                            'client_id' => $area['client_id'] ?? null,
                            'apply_overtime_rate' => $applyOvertimePremium,
                            'apply_night_diff_rate' => $applyNightDiffPremium,
                            'premium_combination_method' => $area['premium_combination_method'] ?? 'billing_formula',
                        ],
                        'guard_count' => $isContractedPost ? 1 : 0,
                        'is_contracted_post' => $isContractedPost,
                        'is_additional_post' => $isAdditionalPost,
                        'retirement_rank' => $guard['retirement_rank'] ?? null,
                        'half_month_rate' => round($halfMonth, 2),
                        'additional_post_days' => $additionalPostDays,
                        'additional_post_daily_rate' => round($additionalPostDailyRate, 2),
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
                        if (!isset($area['billing_totals'][$key]) || !is_numeric($value)) continue;
                        $area['billing_totals'][$key] += $value;
                        $branch['billing_totals'][$key] += $value;
                    }
                }
                foreach ($branch['billing_totals'] as $key => $value) {
                    if ($key !== 'guard_count') {
                        $branch['billing_totals'][$key] = round((float) $value, 2);
                    }
                }
            }
            foreach ($area['billing_totals'] as $key => $value) {
                if ($key !== 'guard_count') {
                    $area['billing_totals'][$key] = round((float) $value, 2);
                }
            }
        }
        unset($area, $branch, $guard);

        // The worksheet header is company-specific. Split an area containing
        // multiple companies into separate area/company sections so rows are
        // never presented under the wrong company's invoice details.
        $companyAreas = [];
        foreach ($data['areas'] as $area) {
            $byCompany = [];
            foreach ($area['branches'] as $branch) {
                $companyKey = (string) ($branch['company_id'] ?? '');
                $byCompany[$companyKey][] = $branch;
            }

            foreach ($byCompany as $companyId => $companyBranches) {
                $companyArea = $area;
                $companyArea['branches'] = array_values($companyBranches);
                $companyArea['company_id'] = $companyId !== '' ? (int) $companyId : null;
                $companyArea['company_name'] = $companyBranches[0]['company_name'] ?? null;
                $companyArea['billing_totals'] = $this->zeroTotals();
                foreach ($companyBranches as $companyBranch) {
                    foreach ($companyBranch['billing_totals'] as $key => $value) {
                        if (isset($companyArea['billing_totals'][$key]) && is_numeric($value)) {
                            $companyArea['billing_totals'][$key] += $value;
                        }
                    }
                }
                foreach ($companyArea['billing_totals'] as $key => $value) {
                    if ($key !== 'guard_count') {
                        $companyArea['billing_totals'][$key] = round((float) $value, 2);
                    }
                }
                $companyAreas[] = $companyArea;
            }
        }
        // Branch rows first; the shared area-personnel group is last on the
        // same company/area worksheet and invoice.
        foreach ($companyAreas as &$companyArea) {
            usort($companyArea['branches'], function ($a, $b) {
                return (int) !empty($a['is_area_personnel']) <=> (int) !empty($b['is_area_personnel']);
            });
            foreach ($companyArea['branches'] as &$branch) {
                if (empty($branch['is_area_personnel'])) continue;
                $positionOrder = ['Area Supervisor' => 1, 'Security Officer' => 2, 'Roving Guard' => 3];
                usort($branch['guards'], function ($a, $b) use ($positionOrder) {
                    $aRank = $positionOrder[$a['position_name'] ?? ''] ?? 99;
                    $bRank = $positionOrder[$b['position_name'] ?? ''] ?? 99;
                    if ($aRank !== $bRank) return $aRank <=> $bRank;
                    return strcasecmp((string) ($a['guard_name'] ?? ''), (string) ($b['guard_name'] ?? ''));
                });
            }
            unset($branch);
        }
        unset($companyArea);
        $data['areas'] = $companyAreas;

        return $data;
    }

    private function zeroTotals(): array
    {
        return array_fill_keys([
            'guard_count', 'half_month_rate', 'additional_post_amount', 'regular_ot',
            'special_ot', 'holiday_ot', 'regular_nd', 'special_nd', 'holiday_nd',
            'deduction_hours', 'deductions', 'before_fee', 'agency_fee', 'vat', 'ewt', 'retirement_half',
            'gross', 'penalty_amount', 'net_payable',
        ], 0);
    }
}
