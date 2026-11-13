<?php

namespace App\Services\Billing;

use App\Models\Billing;
use App\Models\BillingItem;
use App\Models\PayrollPeriod;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/**
 * Generates one bill per company + area + payroll period.
 * Branch identity is retained on each billing item, not on the bill header.
 */
class BillingGenerationService
{
    public function __construct(private BillingExcelPreviewService $preview) {}

    public function generate(PayrollPeriod $period, int $clientId): array
    {
        if (strtolower(trim((string) $period->status)) !== 'closed') {
            throw ValidationException::withMessages(['billing' => 'Billing can only be generated for a Closed payroll period.']);
        }

        $data = $this->preview->build($period);
        $areas = array_values(array_filter(
            $data['areas'],
            fn ($area) => (int) ($area['client_id'] ?? 0) === $clientId
        ));
        $unassigned = $data['unassigned_branches'] ?? [];
        $unassignedPersonnel = $data['unassigned_personnel'] ?? [];
        $groups = [];

        foreach ($areas as $area) {
            foreach ($area['branches'] as $branch) {
                if (empty($branch['company_id'])) {
                    continue;
                }

                // A bill represents the company across every branch in this area.
                $key = $area['area_id'] . '|' . $branch['company_id'];
                if (!isset($groups[$key])) {
                    $groups[$key] = [
                        'area' => $area,
                        'company_id' => $branch['company_id'],
                        'company_name' => $branch['company_name'],
                        'branches' => [],
                    ];
                }
                $groups[$key]['branches'][] = $branch;
            }
        }

        if (empty($groups)) {
            throw ValidationException::withMessages([
                'billing' => 'No billable branch or area-personnel DTRs were found for this client and payroll period.',
            ]);
        }

        return DB::transaction(function () use ($groups, $period, $unassigned, $unassignedPersonnel) {
            $created = collect();
            $updated = collect();
            $skipped = collect();

            foreach ($groups as $group) {
                $branchIds = collect($group['branches'])->pluck('branch_id')->filter()->unique()->values();

                // Lock all records for this company/area/period, including legacy branch-level bills.
                $existingBills = Billing::where('area_id', $group['area']['area_id'])
                    ->where('company_id', $group['company_id'])
                    ->where('payroll_period_id', $period->id)
                    ->lockForUpdate()
                    ->orderByRaw('branch_id IS NOT NULL')
                    ->orderBy('id')
                    ->get();

                $protected = $existingBills->first(
                    fn ($bill) => strtolower(trim((string) $bill->status)) !== 'draft'
                );

                if ($protected) {
                    $skipped->push([
                        'billing_id' => $protected->id,
                        'area_name' => $group['area']['area_name'],
                        'company_name' => $group['company_name'],
                        'status' => $protected->status,
                        'reason' => 'A non-draft bill exists for this company, area and period; the group was left unchanged.',
                    ]);
                    continue;
                }

                $rows = collect($group['branches'])->flatMap(function ($branch) {
                    return collect($branch['guards'])->map(function ($guard) use ($branch) {
                        $guard['_branch_id'] = $branch['branch_id'];
                        return $guard;
                    });
                })->values();

                $sum = fn ($key) => round($rows->sum(
                    fn ($guard) => (float) ($guard['billing'][$key] ?? 0)
                ), 2);

                $snapshot = [
                    'source' => 'excel_preview',
                    'company_id' => $group['company_id'],
                    'company_name' => $group['company_name'],
                    'area_id' => $group['area']['area_id'],
                    'area_name' => $group['area']['area_name'],
                    'period_id' => $period->id,
                    'branch_ids' => $branchIds->all(),
                    'branch_names' => collect($group['branches'])->pluck('branch_name')->values()->all(),
                    'guard_count' => $rows->count(),
                    'unassigned_branches_excluded' => $unassigned,
                    'unassigned_personnel_excluded' => $unassignedPersonnel,
                    'area_personnel_count' => (int) collect($group['branches'])->filter(fn ($b) => !empty($b['is_area_personnel']))->sum(fn ($b) => count($b['guards'] ?? [])),
                    'premium_policy' => [
                        'client_id' => $group['area']['client_id'] ?? null,
                        'apply_overtime_rate' => $group['area']['apply_overtime_rate'] ?? true,
                        'apply_night_diff_rate' => $group['area']['apply_night_diff_rate'] ?? true,
                        'premium_combination_method' => $group['area']['premium_combination_method'] ?? 'billing_formula',
                    ],
                ];

                // Prefer an existing area-level bill; otherwise retain the oldest draft
                // so invoice number, dates and notes remain stable during reprocessing.
                $billing = $existingBills->first();
                if ($billing) {
                    foreach ($existingBills->skip(1) as $duplicate) {
                        // The records are all drafts (protected records were handled above).
                        $duplicate->items()->delete();
                        $duplicate->delete();
                    }
                }

                $attributes = [
                    'client_id' => $group['area']['client_id'],
                    'company_id' => $group['company_id'],
                    'area_id' => $group['area']['area_id'],
                    'branch_id' => null,
                    'payroll_period_id' => $period->id,
                    'subtotal' => $sum('before_fee'),
                    'agency_fee_amount' => $sum('agency_fee'),
                    'retirement_fund_amount' => $sum('retirement_half'),
                    'penalties_amount' => $sum('penalty_amount'),
                    'ewt_amount' => $sum('ewt'),
                    'vat_amount' => $sum('vat'),
                    'total_amount' => $sum('net_payable'),
                    'tax_type' => $billing?->tax_type ?? 'non_vat',
                    'calculation_snapshot' => $snapshot,
                ];

                if ($billing) {
                    $billing->fill($attributes);
                    $billing->save();
                    $billing->items()->delete();
                    $updated->push($billing);
                } else {
                    $billing = Billing::create($attributes + ['status' => 'draft']);
                    $created->push($billing);
                }

                foreach ($rows as $guard) {
                    $v = $guard['billing'];
                    $branchId = $guard['_branch_id'];
                    $items = [
                        ['BASE_CONTRACT', 'Half-month contract base', 'guards', 1, $v['half_month_rate']],
                        ['REGULAR_OT', 'Regular overtime', 'amount', 1, $v['regular_ot']],
                        ['SPECIAL_OT', 'Sunday / special overtime', 'amount', 1, $v['special_ot']],
                        ['HOLIDAY_OT', 'Legal holiday overtime', 'amount', 1, $v['holiday_ot']],
                        ['REGULAR_ND', 'Regular night differential', 'amount', 1, $v['regular_nd']],
                        ['SPECIAL_ND', 'Sunday / special night differential', 'amount', 1, $v['special_nd']],
                        ['HOLIDAY_ND', 'Legal holiday night differential', 'amount', 1, $v['holiday_nd']],
                        ['LATE_UNDERTIME', 'Late / undertime deduction', 'amount', 1, -$v['deductions']],
                        ['AGENCY_FEE', 'Agency fee (20%)', 'amount', 1, $v['agency_fee']],
                        ['EWT', 'Expanded withholding tax (2% of agency fee)', 'amount', 1, -$v['ewt']],
                        ['RETIREMENT_FUND', 'Retirement fund (half month)', 'amount', 1, $v['retirement_half']],
                        ['PENALTY', 'Penalty', 'amount', 1, -$v['penalty_amount']],
                    ];

                    foreach ($items as [$code, $description, $unit, $qty, $amount]) {
                        if (abs((float) $amount) < 0.005) {
                            continue;
                        }
                        BillingItem::create([
                            'billing_id' => $billing->id,
                            'branch_id' => $branchId,
                            'user_id' => $guard['user_id'],
                            'code' => $code,
                            'type' => $amount < 0 ? 'deduction' : 'charge',
                            'description' => $description,
                            'quantity' => $qty,
                            'unit' => $unit,
                            'rate' => $amount,
                            'amount' => round($amount, 2),
                            'calculation_details' => $v,
                        ]);
                    }
                }
            }

            return [
                'billings' => $created,
                'created' => $created,
                'updated' => $updated,
                'skipped' => $skipped,
                'unassigned_branches' => $unassigned,
                'unassigned_personnel' => $unassignedPersonnel,
            ];
        });
    }
}
