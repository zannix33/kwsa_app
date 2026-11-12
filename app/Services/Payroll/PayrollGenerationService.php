<?php

namespace App\Services\Payroll;

use App\Models\Branch;
use App\Models\PayrollItem;
use App\Models\User;
use App\Models\Payroll;
use App\Models\PayrollPeriod;
use App\Models\DailyTimeRecord;
use App\Models\PayrollRate;
use App\Models\EmployeeAllowance;
use App\Models\EmployeeReimbursement;
use App\Services\PayrollItemService;
use Illuminate\Support\Facades\DB;
use App\Models\Loan;
use App\Models\LoanPayment;
use Illuminate\Support\Facades\Log;
use Illuminate\Validation\ValidationException;

class PayrollGenerationService
{
    protected $calculator;

    public function __construct(
        PayrollCalculationService $calculator
    ) {
        $this->calculator = $calculator;
    }

    /**
     * Generate payroll for all employees
     */

    public function generate(PayrollPeriod $period)
    {
        Log::info('Payroll generation started', [
            'period_id' => $period->id,
            'date_from' => $period->date_from,
            'date_to' => $period->date_to,
        ]);

        try {
            return DB::transaction(function () use ($period) {

                $users = User::where('active', 1)->get();

                Log::info('Payroll generation: active employees found', [
                    'count' => $users->count(),
                    'period_id' => $period->id,
                ]);

                \Illuminate\Support\Facades\Log::info('Payroll period DTR diagnostic', [
                    'period_id' => $period->id,
                    'date_from' => \Carbon\Carbon::parse($period->date_from)->toDateString(),
                    'date_to' => \Carbon\Carbon::parse($period->date_to)->toDateString(),

                    'dtrs_in_period' => DailyTimeRecord::whereDate(
                        'work_date',
                        '>=',
                        \Carbon\Carbon::parse($period->date_from)->toDateString()
                    )->whereDate(
                        'work_date',
                        '<=',
                        \Carbon\Carbon::parse($period->date_to)->toDateString()
                    )->count(),

                    'unassigned_dtrs_in_period' => DailyTimeRecord::whereNull('payroll_id')
                        ->whereDate(
                            'work_date',
                            '>=',
                            \Carbon\Carbon::parse($period->date_from)->toDateString()
                        )
                        ->whereDate(
                            'work_date',
                            '<=',
                            \Carbon\Carbon::parse($period->date_to)->toDateString()
                        )
                        ->count(),
                ]);

                foreach ($users as $user) {
                    Log::info('Processing employee payroll', [
                        'user_id' => $user->id,
                        'period_id' => $period->id,
                    ]);

                    $this->generateEmployee($user, $period);
                }

                $period->update([
                    'status' => 'Closed',
                    'processed_at' => now(),
                ]);

                Log::info('Payroll generation completed', [
                    'period_id' => $period->id,
                ]);

                return true;
            });
        } catch (Throwable $e) {
            Log::error('Payroll generation failed', [
                'period_id' => $period->id,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            throw $e;
        }
    }


    /**
     * Generate employee payroll
     */
    protected function generateEmployee(
        User $user,
        PayrollPeriod $period
    ) {

        return DB::transaction(function () use ($user, $period) {

            // Keep your existing generateEmployee() code here,
            // including its early returns, calculations,
            // payroll creation, deductions, and DTR locking.

            /*
            |--------------------------------------------------------------------------
            | Prevent Duplicate Payroll
            |--------------------------------------------------------------------------
            */

            $existingPayroll = Payroll::where('payroll_period_id', $period->id)
                ->where('user_id', $user->id)
                ->lockForUpdate()
                ->first();

            if ($existingPayroll) {
                // Safely fill in newly assigned extras on an already-generated
                // payroll, but never change a payroll that has been marked Paid.
                if ($existingPayroll->status !== 'Paid') {
                    $this->addAllowancesAndReimbursements($user, $period, $existingPayroll);
                    PayrollItemService::recalculate($existingPayroll);
                }
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Fetch DTR
            |--------------------------------------------------------------------------
            */


            $dtrs = DailyTimeRecord::where('user_id', $user->id)
                ->whereNull('payroll_id')
                ->whereDate(
                    'work_date',
                    '>=',
                    \Carbon\Carbon::parse($period->date_from)->toDateString()
                )
                ->whereDate(
                    'work_date',
                    '<=',
                    \Carbon\Carbon::parse($period->date_to)->toDateString()
                )
                ->orderBy('work_date')
                ->get();

            \Illuminate\Support\Facades\Log::info('Payroll DTR lookup', [
                'user_id' => $user->id,
                'period_id' => $period->id,
                'date_from' => $period->date_from,
                'date_to' => $period->date_to,
                'dtr_count' => $dtrs->count(),
                'dtr_ids' => $dtrs->pluck('id')->all(),
            ]);

            // Allowances are paid per payroll period when their effective dates
            // overlap this period. Reimbursements are matched by filed_date.
            $allowances = EmployeeAllowance::with('allowanceType')
                ->where('user_id', $user->id)
                ->where('status', 'Active')
                ->whereDate('effective_from', '<=', $period->date_to)
                ->where(function ($query) use ($period) {
                    $query->whereNull('effective_to')
                        ->orWhereDate('effective_to', '>=', $period->date_from);
                })
                ->get();

            $reimbursements = EmployeeReimbursement::where('user_id', $user->id)
                ->whereNull('payroll_id')
                ->where('status', 'Filed')
                ->whereBetween('filed_date', [$period->date_from, $period->date_to])
                ->lockForUpdate()
                ->get();

            // Do not skip employees who have payroll earnings from allowances or
            // reimbursements even when they have no attendance records this period.
            if ($dtrs->isEmpty() && $allowances->isEmpty() && $reimbursements->isEmpty()) {
                return;
            }

            /*
            |--------------------------------------------------------------------------
            | Calculate attendance-based payroll (when DTRs exist)
            |--------------------------------------------------------------------------
            */

            $result = $dtrs->isNotEmpty()
                ? $this->calculator->calculate($user, $dtrs)
                : [
                    'rate_breakdown' => [],
                    'earnings' => ['overall_pay' => 0],
                    'deductions' => ['late' => 0, 'undertime' => 0, 'CB' => 0],
                ];

            /*
            |--------------------------------------------------------------------------
            | Create Payroll
            |--------------------------------------------------------------------------
            */

            $payroll = Payroll::create([

                'payroll_period_id' => $period->id,

                'user_id' => $user->id,

                'gross_pay' => 0,

                'total_earnings' => 0,

                'total_deductions' => 0,

                'net_pay' => 0,

                'status' => 'Processed'
            ]);



            /*
            |--------------------------------------------------------------------------
            | Earnings — grouped by effective pay rate
            |--------------------------------------------------------------------------
            */

            foreach ($result['rate_breakdown'] as $breakdown) {
                $rateLabel = 'Daily Rate: ₱' . number_format(
                        $breakdown['daily_rate'],
                        2
                    );

                // Basic pay
                if ($breakdown['basic_pay'] > 0) {
                    PayrollItemService::earning(
                        $payroll,
                        PayrollItemService::BASIC,
                        'Basic Pay (' . $rateLabel . ')',
                        $breakdown['basic_pay'],
                        $breakdown['regular_hours'],
                        $breakdown['hourly_rate']
                    );
                }

                // Regular overtime
                if ($breakdown['regular_overtime_pay'] > 0) {
                    PayrollItemService::earning(
                        $payroll,
                        PayrollItemService::OT,
                        'Regular Overtime (' . $rateLabel . ')',
                        $breakdown['regular_overtime_pay'],
                        $breakdown['overtime_hours'],
                        $breakdown['hourly_rate'] * 1.25
                    );
                }

                // Special overtime
                if ($breakdown['special_overtime_pay'] > 0) {
                    PayrollItemService::earning(
                        $payroll,
                        PayrollItemService::SOT,
                        'Special Overtime (' . $rateLabel . ')',
                        $breakdown['special_overtime_pay'],
                        $breakdown['special_overtime_hours'],
                        $breakdown['hourly_rate'] * 1.69
                    );
                }

                // Regular/legal holiday overtime (260%).
                if ($breakdown['regular_holiday_overtime_pay'] > 0) {
                    PayrollItemService::earning(
                        $payroll,
                        'REGHOLIDAYOT',
                        'Regular Holiday Overtime (' . $rateLabel . ')',
                        $breakdown['regular_holiday_overtime_pay'],
                        $breakdown['regular_holiday_overtime_hours'],
                        $breakdown['hourly_rate'] * 2.60
                    );
                }

                // Night differential
                if ($breakdown['night_diff_pay'] > 0) {
                    PayrollItemService::earning(
                        $payroll,
                        PayrollItemService::ND,
                        'Night Differential (' . $rateLabel . ')',
                        $breakdown['night_diff_pay'],
                        $breakdown['night_diff_hours'],
                        $breakdown['hourly_rate'] * 0.10
                    );
                }

                // Regular holiday
                if ($breakdown['holiday_pay'] > 0) {
                    PayrollItemService::earning(
                        $payroll,
                        PayrollItemService::REGHOLIDAY,
                        'Regular Holiday Pay (' . $rateLabel . ')',
                        $breakdown['holiday_pay']
                    );
                }

                // Special holiday
                if ($breakdown['sp_holiday_pay'] > 0) {
                    PayrollItemService::earning(
                        $payroll,
                        PayrollItemService::SPHOLIDAY,
                        'Special Holiday Pay (' . $rateLabel . ')',
                        $breakdown['sp_holiday_pay']
                    );
                }
            }



            /*
            |--------------------------------------------------------------------------
            | Deductions
            |--------------------------------------------------------------------------
            */

            PayrollItemService::deduction(
                $payroll,
                'LATE',
                'Late Deduction',
                $result['deductions']['late']
            );

            PayrollItemService::deduction(
                $payroll,
                'UT',
                'Undertime Deduction',
                $result['deductions']['undertime']
            );

            PayrollItemService::deduction(
                $payroll,
                'CB',
                'Cash Bond',
                $result['deductions']['CB']
            );

            /*
            |--------------------------------------------------------------------------
            | Government Contributions: Independent 7-Day Rule Per Payroll
            |--------------------------------------------------------------------------
            */

            $periodStart = \Carbon\Carbon::parse($period->date_from)->startOfDay();
            $periodEnd = \Carbon\Carbon::parse($period->date_to)->endOfDay();

            // Count distinct DTR dates only within this payroll period.
            // There is no carryover between the first and second payrolls.
            $daysWorkedInPeriod = DailyTimeRecord::where('user_id', $user->id)
                ->whereBetween('work_date', [
                    $periodStart->toDateString(),
                    $periodEnd->toDateString(),
                ])
                ->distinct()
                ->count('work_date');

            if ($daysWorkedInPeriod >= 7) {
                $sss = SSSService::compute(
                    $result['earnings']['overall_pay']
                );

                $pagibigRate = PayrollRate::where('slug', 'pagibig')
                    ->firstOrFail()
                    ->rate;

                $philhealthRate = PayrollRate::where('slug', 'philhealth')
                    ->firstOrFail()
                    ->rate;

                PayrollItemService::deduction(
                    $payroll,
                    'SSS',
                    'SSS Contribution',
                    $sss
                );

                PayrollItemService::deduction(
                    $payroll,
                    'PHIC',
                    'PhilHealth Contribution',
                    $philhealthRate
                );

                PayrollItemService::deduction(
                    $payroll,
                    'PAGIBIG',
                    'PagIBIG Contribution',
                    $pagibigRate
                );
            }

            /*
            |--------------------------------------------------------------------------
            | Allowances and Reimbursements — additional payroll earnings
            |--------------------------------------------------------------------------
            */

            $this->addAllowancesAndReimbursements($user, $period, $payroll);

            /*
            |--------------------------------------------------------------------------
            | Recalculate Totals
            |--------------------------------------------------------------------------
            */

            // Loan deductions remain tied to attendance-based payroll.
            if ($dtrs->isNotEmpty()) {
                $this->applyLoanDeductions(
                    $user,
                    $period,
                    $payroll
                );
            }

            // Recalculate payroll after all deductions are added.
            PayrollItemService::recalculate(
                $payroll
            );

            /*
            |--------------------------------------------------------------------------
            | Lock DTRs
            |--------------------------------------------------------------------------
            */

            DailyTimeRecord::whereIn(
                'id',
                $dtrs->pluck('id')
            )->update([
                'payroll_id' => $payroll->id
            ]);
        });
    }

    /**
     * Add eligible period allowances and unassigned reimbursements exactly once.
     */
    protected function addAllowancesAndReimbursements(
        User $user,
        PayrollPeriod $period,
        Payroll $payroll
    ): void {
        $allowances = EmployeeAllowance::with('allowanceType')
            ->where('user_id', $user->id)
            ->where('status', 'Active')
            ->whereDate('effective_from', '<=', $period->date_to)
            ->where(function ($query) use ($period) {
                $query->whereNull('effective_to')
                    ->orWhereDate('effective_to', '>=', $period->date_from);
            })
            ->get();

        foreach ($allowances as $allowance) {
            $code = 'ALLOWANCE-' . $allowance->id;
            $alreadyAdded = $payroll->items()->where('code', $code)->exists();
            if ($alreadyAdded) {
                continue;
            }

            $allowanceName = optional($allowance->allowanceType)->name ?: 'Allowance';
            PayrollItemService::earning(
                $payroll,
                $code,
                $allowanceName . ' Allowance',
                (float) $allowance->amount
            );
        }

        $reimbursements = EmployeeReimbursement::where('user_id', $user->id)
            ->whereNull('payroll_id')
            ->where('status', 'Filed')
            ->whereBetween('filed_date', [$period->date_from, $period->date_to])
            ->lockForUpdate()
            ->get();

        foreach ($reimbursements as $reimbursement) {
            PayrollItemService::earning(
                $payroll,
                'REIMBURSEMENT',
                'Reimbursement - ' . $reimbursement->category . ': ' . $reimbursement->description,
                (float) $reimbursement->amount
            );

            $reimbursement->update([
                'payroll_period_id' => $period->id,
                'payroll_id' => $payroll->id,
                'status' => 'Included',
            ]);
        }
    }

    /**
     * Apply semi-monthly loan deductions to payroll.
     */
    protected function applyLoanDeductions(
        User $user,
        PayrollPeriod $period,
        Payroll $payroll
    ) {

        $periodEnd = \Carbon\Carbon::parse($period->date_to);

        if ($periodEnd->day < 16) {
            return;
        }

        // Must run inside the transaction used to generate this payroll.
        $loans = Loan::where('user_id', $user->id)
            ->where('status', 'Active')
            ->where('outstanding_balance', '>', 0)
            ->whereDate(
                'deduction_start_date',
                '<=',
                $period->date_to
            )
            ->where(function ($query) use ($period) {
                $query->whereNull('deduction_end_date')
                    ->orWhereDate(
                        'deduction_end_date',
                        '>=',
                        $period->date_from
                    );
            })
            ->lockForUpdate()
            ->get();

        foreach ($loans as $loan) {

            $periodStart = \Carbon\Carbon::parse($period->date_from);
            $periodEnd = \Carbon\Carbon::parse($period->date_to);

            // Has this loan already been paid in the same calendar month?
            $alreadyPaidThisMonth = LoanPayment::where(
                'loan_id',
                $loan->id
            )
                ->whereYear('payment_date', $periodEnd->year)
                ->whereMonth('payment_date', $periodEnd->month)
                ->exists();

            if ($alreadyPaidThisMonth) {
                continue;
            }
            // Do not record the same loan twice for this payroll period.
            $alreadyPaid = LoanPayment::where('loan_id', $loan->id)
                ->where('payroll_period_id', $period->id)
                ->exists();

            if ($alreadyPaid) {
                continue;
            }

            $monthlyAmortization = (float) $loan->monthly_amortization;
            $outstandingBalance = (float) $loan->outstanding_balance;

            $deductionAmount = round(
                min($monthlyAmortization, $outstandingBalance),
                2
            );

            if ($deductionAmount <= 0) {
                continue;
            }

            switch ($loan->loan_provider) {
                case 'SSS':
                    $code = 'SSS_LOAN';
                    $description = 'SSS Loan Repayment';
                    break;

                case 'Pag-IBIG':
                    $code = 'PAGIBIG_LOAN';
                    $description = 'Pag-IBIG Loan Repayment';
                    break;

                case 'Company':
                    $code = 'COMPANY_LOAN';
                    $description = 'Company Loan Repayment';
                    break;

                default:
                    // Ignore unknown providers rather than misclassify them.
                    continue 2;
            }

            // Add the deduction to this payroll.
            PayrollItemService::deduction(
                $payroll,
                $code,
                $description . ' - ' . $loan->loan_reference,
                $deductionAmount
            );

            // Record the repayment against this payroll period.
            LoanPayment::create([
                'loan_id' => $loan->id,
                'payroll_period_id' => $period->id,
                'payment_date' => $period->date_to,
                'amount' => $deductionAmount,
                'payment_method' => 'Payroll',
            ]);

            // Update the loan balance.
            $newTotalPaid = round(
                (float) $loan->total_paid + $deductionAmount,
                2
            );

            $newBalance = round(
                max(0, $outstandingBalance - $deductionAmount),
                2
            );

            $loan->update([
                'total_paid' => $newTotalPaid,
                'outstanding_balance' => $newBalance,
                'status' => $newBalance <= 0
                    ? 'Fully Paid'
                    : 'Active',
            ]);
        }
    }


    public function generate13thMonth($areaId,$from,$to)
    {

        $employees = Branch::leftJoin('users', 'branches.id', '=', 'users.branch_id')
            ->where('branches.area_id', $areaId)
            ->select(
                'branches.id as branch_id',
                'branches.name as branch_name',
                'users.id as user_id',
                'users.firstname',
                'users.middlename',
                'users.lastname'
            )
            ->orderBy('branches.name')
            ->orderBy('users.lastname')
            ->get();

        /*$employees = User::whereHas('branches',function($q) use ($areaId){

            $q->where('area_id',$areaId);

        })
            ->with('branches')
            ->get();
        */

        $rows=[];

        $periods = PayrollPeriod::whereYear('date_from', $from)
            ->orderBy('date_from')
            ->pluck('id');


        foreach($employees as $employee){

            $payrolls = Payroll::where('user_id',$employee->user_id)
                ->whereIn('payroll_period_id',[$periods])
                ->pluck('id');





            foreach($payrolls as $payroll){

                $dtrs = DailyTimeRecord::where('user_id',$employee->user_id)
                    ->whereIn('payroll_id',[$payrolls])
                    ->get();

                //dd($dtrs);
                $hours=0;
                $cashBond=0;
                $basicSalary=0;

                foreach($dtrs as $dtr){

                    $hours += $dtr->regular_hours;

                    $basicSalary += 450;

                    $cashBond += PayrollItem::where('payroll_id',$payroll)
                        ->where('code','CB')
                        ->sum('amount');

                }

                $daysWorked = $hours / 8;

                $thirteenth = 500*365/12/12/30* $daysWorked;

                $rows[]=[

                    'employee'=>$employee,

                    'branch'=>$employee->branch_name,

                    'hours'=>$hours,

                    'days'=>$daysWorked,

                    'cash_bond'=>$cashBond,

                    'thirteenth'=>$thirteenth,

                    'total'=>$cashBond+$thirteenth

                ];



            }



        }

        return collect($rows);
    }
}
