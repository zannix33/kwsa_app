<?php

namespace App\Services\Payroll;

use App\Models\Branch;
use App\Models\PayrollItem;
use App\Models\User;
use App\Models\Payroll;
use App\Models\PayrollPeriod;
use App\Models\DailyTimeRecord;
use App\Models\PayrollRate;
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
            $result['earnings']['regular_overtime_pay'],
            $result['metrics']['overtime_hours'],
            $result['metrics']['hourly_rate'] * 1.25
        );

        PayrollItemService::earning(
            $payroll,
            'SOT',
            'Special/Sunday Overtime Pay',
            $result['earnings']['special_overtime_pay'],
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

        PayrollItemService::earning(
            $payroll,
            'HOLIDAY',
            'Special Holiday Pay',
            $result['earnings']['sp_holiday_pay']
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
            $result['earnings']['overall_pay']
        );

        $pagibigRate = PayrollRate::where('slug', 'pagibig')->first()->rate;
        $philhealthRate = PayrollRate::where('slug', 'philhealth')->first()->rate;

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

        /*
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
