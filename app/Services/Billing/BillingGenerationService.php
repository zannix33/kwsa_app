<?php

namespace App\Services\Billing;

use App\Models\Billing;
use App\Models\BillingItem;
use App\Models\PayrollPeriod;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;

/** Saves bills from the exact guard-level values used by the Excel preview. */
class BillingGenerationService
{
    public function __construct(private BillingExcelPreviewService $preview) {}

    public function generate(PayrollPeriod $period): array
    {
        if (strtolower(trim((string) $period->status)) !== 'closed') {
            throw ValidationException::withMessages(['billing' => 'Billing can only be generated for a Closed payroll period.']);
        }

        $data = $this->preview->build($period);
        $unassigned = $data['unassigned_branches'] ?? [];
        $groups = [];
        foreach ($data['areas'] as $area) {
            foreach ($area['branches'] as $branch) {
                if (empty($branch['company_id'])) continue;
                $key = $area['area_id'].'|'.$branch['company_id'];
                if (!isset($groups[$key])) $groups[$key] = ['area' => $area, 'company_id' => $branch['company_id'], 'company_name' => $branch['company_name'], 'branches' => []];
                $groups[$key]['branches'][] = $branch;
            }
        }

        return DB::transaction(function () use ($groups, $period, $unassigned) {
            foreach ($groups as $group) {
                $exists = Billing::where('area_id', $group['area']['area_id'])
                    ->where('company_id', $group['company_id'])
                    ->where('payroll_period_id', $period->id)->exists();
                if ($exists) {
                    throw ValidationException::withMessages(['billing' => "A bill already exists for {$group['area']['area_name']} / {$group['company_name']} in this payroll period."]);
                }
            }

            $created = collect();
            foreach ($groups as $group) {
                $rows = collect($group['branches'])->flatMap(function ($branch) {
                    return collect($branch['guards'])->map(function ($guard) use ($branch) { $guard['_branch_id'] = $branch['branch_id']; return $guard; });
                });
                $sum = function ($key) use ($rows) { return round($rows->sum(function ($g) use ($key) { return (float) ($g['billing'][$key] ?? 0); }), 2); };
                $subtotal = $sum('before_fee'); $fee = $sum('agency_fee'); $ewt = $sum('ewt');
                $retirement = $sum('retirement_half'); $penalty = $sum('penalty_amount'); $vat = $sum('vat');
                $total = $sum('net_payable');
                $billing = Billing::create([
                    'client_id' => $group['area']['client_id'], 'company_id' => $group['company_id'],
                    'area_id' => $group['area']['area_id'], 'payroll_period_id' => $period->id,
                    'status' => 'draft', 'subtotal' => $subtotal, 'agency_fee_amount' => $fee,
                    'retirement_fund_amount' => $retirement, 'penalties_amount' => $penalty,
                    'ewt_amount' => $ewt, 'vat_amount' => $vat, 'total_amount' => $total,
                    'tax_type' => 'non_vat', 'calculation_snapshot' => [
                        'source' => 'excel_preview', 'company_id' => $group['company_id'],
                        'company_name' => $group['company_name'], 'period_id' => $period->id,
                        'guard_count' => $rows->count(), 'unassigned_branches_excluded' => $unassigned,
                    ],
                ]);
                foreach ($rows as $guard) {
                    $v = $guard['billing']; $branchId = $guard['_branch_id'];
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
                        if (abs((float) $amount) < 0.005) continue;
                        BillingItem::create(['billing_id' => $billing->id, 'branch_id' => $branchId,
                            'user_id' => $guard['user_id'], 'code' => $code, 'type' => $amount < 0 ? 'deduction' : 'charge',
                            'description' => $description, 'quantity' => $qty, 'unit' => $unit,
                            'rate' => $amount, 'amount' => round($amount, 2), 'calculation_details' => $v]);
                    }
                }
                $created->push($billing);
            }
            return ['billings' => $created, 'unassigned_branches' => $unassigned];
        });
    }
}
