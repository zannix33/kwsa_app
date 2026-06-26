<?php

namespace App\Services\Payroll;

use App\Models\User;
use App\Models\Payroll;
use App\Models\PayrollPeriod;
use App\Models\DailyTimeRecord;
use App\Services\PayrollItemService;
use Illuminate\Support\Facades\DB;

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
    public function generate(
        PayrollPeriod $period
    ) {
        return DB::transaction(function () use ($period) {

            $users = User::where(
                'active',
                1
            )->get();

            foreach ($users as $user) {

                $this->generateEmployee(
                    $user,
                    $period
                );
            }

            $period->update([
                'status' => 'Closed',
                'processed_at' => now()
            ]);

            return true;
        });
    }

    /**
     * Generate employee payroll
     */
    protected function generateEmployee(
        User $user,
        PayrollPeriod $period
    ) {

        /*
        |--------------------------------------------------------------------------
        | Prevent Duplicate Payroll
        |--------------------------------------------------------------------------
        */

        $exists = Payroll::where(
            'payroll_period_id',
            $period->id
        )
            ->where(
                'user_id',
                $user->id
            )
            ->exists();

        if ($exists) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Fetch DTR
        |--------------------------------------------------------------------------
        */

        $dtrs = DailyTimeRecord::where(
            'user_id',
            $user->id
        )
            ->whereNull('payroll_id')
            ->whereBetween(
                'work_date',
                [
                    $period->date_from,
                    $period->date_to
                ]
            )
            ->get();

        if ($dtrs->isEmpty()) {
            return;
        }

        /*
        |--------------------------------------------------------------------------
        | Calculate Payroll
        |--------------------------------------------------------------------------
        */

        $result = $this->calculator->calculate(
            $user,
            $dtrs
        );

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
        | Earnings
        |--------------------------------------------------------------------------
        */

        PayrollItemService::earning(
            $payroll,
            'BASIC',
            'Basic Pay',
            $result['earnings']['basic_pay'],
            $result['metrics']['days_worked'],
            $result['metrics']['daily_rate']
        );

        PayrollItemService::earning(
            $payroll,
            'OT',
            'Overtime Pay',
            $result['earnings']['overtime_pay'],
            $result['metrics']['overtime_hours'],
            $result['metrics']['hourly_rate'] * 1.25
        );

        PayrollItemService::earning(
            $payroll,
            'ND',
            'Night Differential',
            $result['earnings']['night_diff_pay'],
            $result['metrics']['night_diff_hours'],
            $result['metrics']['hourly_rate'] * .10
        );

        PayrollItemService::earning(
            $payroll,
            'HOLIDAY',
            'Holiday Pay',
            $result['earnings']['holiday_pay']
        );

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
        | Government Contributions
        |--------------------------------------------------------------------------
        */

        $sss = SSSService::compute(
            2000//$user->monthly_salary
        );

        PayrollItemService::deduction(
            $payroll,
            'SSS',
            'SSS Contribution',
            $sss
        );

        /*

        $monthlySalary =
            $user->branch->rate * 26;

        $sss = SSSService::compute(
            $monthlySalary
        );

        $philhealth =
            PhilHealthService::compute(
                $monthlySalary
            );

        $pagibig =
            PagibigService::compute(
                $monthlySalary
            );

        $tax = TaxService::compute(
            $monthlySalary
        );


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
            $philhealth
        );

        PayrollItemService::deduction(
            $payroll,
            'PAGIBIG',
            'PagIBIG Contribution',
            $pagibig
        );

        PayrollItemService::deduction(
            $payroll,
            'TAX',
            'Withholding Tax',
            $tax
        );

         */

        /*
        |--------------------------------------------------------------------------
        | Recalculate Totals
        |--------------------------------------------------------------------------
        */

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
    }
}
