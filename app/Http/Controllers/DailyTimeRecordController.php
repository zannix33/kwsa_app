<?php

namespace App\Http\Controllers;

use App\Models\DailyTimeRecord;
use App\Models\Payroll;
use App\Services\Payroll\PayrollCalculationService;
use App\Services\PayrollItemService;
use App\Models\PayrollPeriod;
use App\Models\User;
use App\Models\Area;
use App\Models\Branch;
use App\Models\Company;
use Illuminate\Support\Facades\Auth;
use Carbon\Carbon;
use Carbon\CarbonPeriod;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class DailyTimeRecordController extends Controller
{

    /**
     * Browse and filter daily time records.
     */
    public function index(Request $request)
    {
        $query = DailyTimeRecord::with(['user', 'area', 'branch', 'company', 'payroll'])
            ->orderByDesc('work_date')->orderByDesc('id');

        if ($request->filled('employee')) {
            $term = $request->employee;
            $query->whereHas('user', function ($q) use ($term) {
                $q->where('firstname', 'like', "%{$term}%")
                  ->orWhere('lastname', 'like', "%{$term}%");
            });
        }
        if ($request->filled('from_date')) $query->whereDate('work_date', '>=', $request->from_date);
        if ($request->filled('to_date')) $query->whereDate('work_date', '<=', $request->to_date);

        $records = $query->paginate(25)->appends($request->query());
        return view('pages.dtr.index', compact('records'));
    }

    public function edit(DailyTimeRecord $dtr)
    {
        $dtr->load(['user', 'area', 'branch', 'company', 'payroll']);
        if ($dtr->payroll && strtolower($dtr->payroll->status) === 'paid') {
            return redirect()->route('dtr.index')->with('error', 'This DTR belongs to a paid payroll and cannot be edited.');
        }
        $areas = Area::orderBy('name')->get();
        $branches = Branch::when($dtr->area_id, function ($query) use ($dtr) {
            $query->where('area_id', $dtr->area_id);
        })->orderBy('name')->get();
        $companies = DB::table('area_user_company')
            ->join('companies', 'companies.id', '=', 'area_user_company.company_id')
            ->where('area_user_company.user_id', $dtr->user_id)
            ->where('area_user_company.area_id', $dtr->area_id)
            ->where('companies.active', 1)
            ->orderBy('companies.name')
            ->get(['companies.id', 'companies.name']);
        return view('pages.dtr.edit', compact('dtr', 'areas', 'branches', 'companies'));
    }

    public function update(Request $request, DailyTimeRecord $dtr)
    {
        $request->validate([
            'work_date' => 'required|date',
            'area_id' => 'required|exists:areas,id',
            'branch_id' => 'nullable|exists:branches,id',
            'company_id' => 'nullable|exists:companies,id',
            'operation_start' => 'required|date_format:H:i',
            'operation_end' => 'required|date_format:H:i',
            'time_in' => 'required|date_format:H:i',
            'time_out' => 'required|date_format:H:i',
            'break_minutes' => 'nullable|integer|min:0|max:1440',
            'remarks' => 'nullable|string|max:2000',
            'reason' => 'required|string|max:1000',
        ]);

        $employee = $dtr->user;
        $areaId = (int) $request->area_id;
        if (!$employee->areas()->where('areas.id', $areaId)->exists()) {
            return back()->withInput()->withErrors(['area_id' => 'The employee is not assigned to the selected area.']);
        }
        $branchId = $request->filled('branch_id') ? (int) $request->branch_id : null;
        $companyId = $request->filled('company_id') ? (int) $request->company_id : null;
        if ($branchId) {
            $branch = Branch::whereKey($branchId)->where('area_id', $areaId)->first();
            if (!$branch || !$employee->branches()->where('branches.id', $branchId)->exists()) {
                return back()->withInput()->withErrors(['branch_id' => 'The selected branch is not assigned to this employee in the selected area.']);
            }
            $companyId = null; // Branch billing continues to resolve its company through the branch.
        } elseif (!$companyId || !DB::table('area_user_company')->where('user_id', $employee->id)->where('area_id', $areaId)->where('company_id', $companyId)->exists()) {
            return back()->withInput()->withErrors(['company_id' => 'Select a company assigned to this employee in the selected area.']);
        }

        return DB::transaction(function () use ($request, $dtr, $companyId) {
            $dtr = DailyTimeRecord::whereKey($dtr->id)->lockForUpdate()->firstOrFail();
            $payroll = $dtr->payroll_id ? Payroll::find($dtr->payroll_id) : null;
            if ($payroll && strtolower($payroll->status) === 'paid') {
                return back()->withInput()->with('error', 'Paid payroll records cannot be changed.');
            }
            if ($payroll) {
                $period = $payroll->period;
                if ($period && (
                    Carbon::parse($request->work_date)->lt(Carbon::parse($period->date_from)->startOfDay()) ||
                    Carbon::parse($request->work_date)->gt(Carbon::parse($period->date_to)->endOfDay())
                )) {
                    return back()->withInput()->with('error', 'The work date must remain within the linked payroll period. Remove/reassign the DTR through payroll administration if the date truly needs to move.');
                }
            }

            $before = $dtr->only([
                'work_date','area_id','branch_id','company_id','operation_start','operation_end',
                'time_in','time_out','break_minutes','is_rest_day','is_holiday',
                'special_holiday','is_extended_hours','remarks','regular_hours',
                'overtime_hours','extended_hours','night_differential_hours',
                'late_hours','undertime_hours','total_hours'
            ]);

            $record = $request->only([
                'work_date','area_id','branch_id','operation_start','operation_end',
                'time_in','time_out','break_minutes'
            ]);
            $calculated = $this->calculateDtr($record);
            $dtr->fill([
                'company_id' => $companyId,
                'work_date' => $record['work_date'],
                'area_id' => $record['area_id'],
                'branch_id' => $record['branch_id'] ?: null,
                'operation_start' => $calculated['operation_start'],
                'operation_end' => $calculated['operation_end'],
                'time_in' => $calculated['time_in'],
                'time_out' => $calculated['time_out'],
                'break_minutes' => $record['break_minutes'] ?? 60,
                'scheduled_hours' => $calculated['scheduled_hours'],
                'regular_hours' => $calculated['regular_hours'],
                'overtime_hours' => $calculated['overtime_hours'],
                'extended_hours' => $calculated['extended_hours'],
                'night_differential_hours' => $calculated['night_differential_hours'],
                'late_hours' => $calculated['late_hours'],
                'undertime_hours' => $calculated['undertime_hours'],
                'total_hours' => $calculated['worked_hours'],
                'is_rest_day' => $request->boolean('is_rest_day'),
                'is_holiday' => $request->boolean('is_holiday'),
                'special_holiday' => $request->boolean('special_holiday'),
                'is_extended_hours' => $request->boolean('is_extended_hours'),
                'remarks' => $request->remarks,
            ])->save();

            DB::table('dtr_audits')->insert([
                'daily_time_record_id' => $dtr->id,
                'user_id' => Auth::id(),
                'reason' => $request->reason,
                'before_data' => json_encode($before),
                'after_data' => json_encode($dtr->fresh()->only(array_keys($before))),
                'created_at' => now(), 'updated_at' => now(),
            ]);

            return redirect()->route('dtr.index')->with(
                'success',
                $payroll ? 'DTR updated. Reprocess the associated payroll to apply the attendance changes.' : 'DTR updated and recalculated successfully.'
            );
        });
    }

    /**
     * Rebuild attendance-related payroll items only. Existing government,
     * loan, and fixed deductions are deliberately preserved and never re-applied.
     */
    public function reprocessPayroll(Payroll $payroll, PayrollCalculationService $calculator)
    {
        if (strtolower($payroll->status) === 'paid') {
            return back()->with('error', 'Paid payrolls cannot be reprocessed.');
        }

        try {
            DB::transaction(function () use ($payroll, $calculator) {
                $payroll = Payroll::whereKey($payroll->id)->lockForUpdate()->firstOrFail();
                if (strtolower($payroll->status) === 'paid') {
                    throw new \RuntimeException('Paid payrolls cannot be reprocessed.');
                }

                $dtrs = DailyTimeRecord::with(['area.client','branch.area.client'])
                    ->where('payroll_id', $payroll->id)->orderBy('work_date')->get();

                $result = $calculator->calculate($payroll->user, $dtrs);
                $attendanceCodes = [
                    'BASIC','OT','SOT','REGHOLIDAYOT','ND','REGHOLIDAY',
                    'SPHOLIDAY','LATE','UT','CB'
                ];
                $payroll->items()->whereIn('code', $attendanceCodes)->delete();

                foreach ($result['rate_breakdown'] as $b) {
                    $label = 'Daily Rate: ₱' . number_format($b['daily_rate'], 2);
                    if ($b['basic_pay'] > 0) PayrollItemService::earning($payroll,'BASIC','Basic Pay ('.$label.')',$b['basic_pay'],$b['regular_hours'],$b['hourly_rate']);
                    if ($b['regular_overtime_pay'] > 0) PayrollItemService::earning($payroll,'OT','Regular Overtime ('.$label.')',$b['regular_overtime_pay'],$b['overtime_hours'],$b['regular_overtime_rate']);
                    if ($b['special_overtime_pay'] > 0) PayrollItemService::earning($payroll,'SOT','Special Overtime ('.$label.')',$b['special_overtime_pay'],$b['special_overtime_hours'],$b['special_overtime_rate']);
                    if ($b['regular_holiday_overtime_pay'] > 0) PayrollItemService::earning($payroll,'REGHOLIDAYOT','Regular Holiday Overtime ('.$label.')',$b['regular_holiday_overtime_pay'],$b['regular_holiday_overtime_hours'],$b['regular_holiday_overtime_rate']);
                    if ($b['night_diff_pay'] > 0) PayrollItemService::earning($payroll,'ND','Night Differential ('.$label.')',$b['night_diff_pay'],$b['night_diff_hours'],$b['night_diff_rate']);
                    if ($b['holiday_pay'] > 0) PayrollItemService::earning($payroll,'REGHOLIDAY','Regular Holiday Pay ('.$label.')',$b['holiday_pay']);
                    if ($b['sp_holiday_pay'] > 0) PayrollItemService::earning($payroll,'SPHOLIDAY','Special Holiday Pay ('.$label.')',$b['sp_holiday_pay']);
                }
                PayrollItemService::deduction($payroll,'LATE','Late Deduction',$result['deductions']['late']);
                PayrollItemService::deduction($payroll,'UT','Undertime Deduction',$result['deductions']['undertime']);
                PayrollItemService::deduction($payroll,'CB','Cash Bond',$result['deductions']['CB']);
                PayrollItemService::recalculate($payroll);
            });
        } catch (\Throwable $e) {
            return back()->with('error', 'Payroll reprocessing failed: '.$e->getMessage());
        }

        return back()->with('success', 'Payroll attendance items and totals were reprocessed. Existing loan, fixed, and government deductions were preserved.');
    }

    public function createBulk()
    {
        //$users = User::orderBy('name')
        //    ->get();

        $users = User::where(function ($query) {
            $query->whereHas('areas')
                ->orWhereHas('branches');
        })
            ->orderBy('lastname')
            ->orderBy('firstname')
            ->get();

        return view(
            'pages.dtr.bulk-create',
            compact('users')
        );
    }

    public function generateBulk(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'from_date'   => 'required|date',
            'to_date'     => 'required|date|after_or_equal:from_date',
        ]);

        //$user = User::findOrFail(
        //    $request->user_id
        //);

        //$user = User::with([
        //    'areas.branches'
        //])->findOrFail($request->user_id);

        $period = CarbonPeriod::create(
            $request->from_date,
            $request->to_date
        );

        $user = User::findOrFail($request->user_id);

        // Only expose areas and branches explicitly assigned to this guard.
        $areas = $this->getAssignedAreasWithBranches($user);

        //$payrollPeriod = PayrollPeriod::findOrFail(
        //    $period->id
        //);

        $dates = [];

        $current = Carbon::parse(
            $request->from_date,
        );

        $end = Carbon::parse(
            $request->to_date
        );

        while($current->lte($end)){
            $dates[] = $current->copy();
            $current->addDay();
        }

        return view(
            'pages.dtr.bulk-entry',
            [

                'user'=>$user,

                'areas'=>$areas, //$user->areas,

                'dates'=>$dates,

                'period'=>$period

            ]
        );

        //return view(
        //    'pages.dtr.bulk-entry',
        //    compact(
        //        'user',
        //        'period'
        //    )
        //);
    }

    public function bulkStore(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'records' => 'required|array',
        ]);

        // Ignore untouched/absent dates before validating assignment fields.
        // These rows are intentionally not submitted as DTR records.
        $records = collect($request->input('records', []))
            ->filter(function ($record) {
                return !empty($record['time_in']) || !empty($record['time_out']);
            })
            ->all();

        if (empty($records)) {
            return redirect()
                ->route('dtr.bulk.create')
                ->with('warning', 'No DTR records were saved because no attendance times were entered.');
        }

        $request->merge(['records' => $records]);
        $request->validate([
            'records.*.work_date' => 'required|date',
            'records.*.area_id' => 'required|exists:areas,id',
            'records.*.branch_id' => 'nullable|exists:branches,id',
            'records.*.company_id' => 'nullable|exists:companies,id',
            'records.*.operation_start' => 'required|date_format:H:i',
            'records.*.operation_end' => 'required|date_format:H:i',
            'records.*.time_in' => 'required|date_format:H:i',
            'records.*.time_out' => 'required|date_format:H:i',
            'records.*.break_minutes' => 'nullable|numeric|min:0',
        ]);

        $employee = User::findOrFail($request->user_id);
        foreach ($records as $index => &$record) {
            $areaId = (int) $record['area_id'];
            if (!$employee->areas()->where('areas.id', $areaId)->exists()) {
                return back()->withInput()->withErrors(["records.{$index}.area_id" => 'The employee is not assigned to the selected area.']);
            }
            $branchId = !empty($record['branch_id']) ? (int) $record['branch_id'] : null;
            if ($branchId) {
                $branch = Branch::whereKey($branchId)->where('area_id', $areaId)->first();
                if (!$branch || !$employee->branches()->where('branches.id', $branchId)->exists()) {
                    return back()->withInput()->withErrors(["records.{$index}.branch_id" => 'The selected branch is not assigned to this employee in the selected area.']);
                }
                $record['company_id'] = null; // Branch billing resolves the company through the branch.
            } else {
                if (!$employee->areas()->where('areas.id', $areaId)->exists()) {
                    return back()->withInput()->withErrors(["records.{$index}.area_id" => 'The employee is not assigned to the selected area.']);
                }
                $companyId = !empty($record['company_id']) ? (int) $record['company_id'] : null;
                if (!$companyId || !DB::table('area_user_company')->where('user_id', $employee->id)->where('area_id', $areaId)->where('company_id', $companyId)->exists()) {
                    return back()->withInput()->withErrors(["records.{$index}.company_id" => 'Select a company assigned to this employee in the selected area.']);
                }
                $record['company_id'] = $companyId;
            }
        }
        unset($record);
        $request->merge(['records' => $records]);

        DB::transaction(function () use ($request) {

            foreach ($request->records as $record) {
                $calculated =
                    $this->calculateDtr($record);

                DailyTimeRecord::updateOrCreate(
                    [
                        'user_id' => $request->user_id,
                        'work_date'   => $record['work_date'],
                        'area_id'=>$record['area_id'],
                        'branch_id' => $record['branch_id'] ?? null,
                        'company_id' => $record['company_id'] ?? null,
                    ],
                    [
                        'operation_start' =>
                            $calculated['operation_start'],

                        'operation_end' =>
                            $calculated['operation_end'],

                        'time_in' =>
                            $calculated['time_in'],

                        'time_out' =>
                            $calculated['time_out'],

                        'break_minutes' =>
                            $record['break_minutes'] ?? 60,

                        'scheduled_hours' =>
                            $calculated['scheduled_hours'],

                        'regular_hours' =>
                            $calculated['regular_hours'],

                        'overtime_hours' =>
                            $calculated['overtime_hours'],

                        'extended_hours' =>
                            $calculated['extended_hours'],

                        'night_differential_hours' =>
                            $calculated['night_differential_hours'],

                        'late_hours' =>
                            $calculated['late_hours'],

                        'undertime_hours' =>
                            $calculated['undertime_hours'],

                        'total_hours' =>
                            $calculated['worked_hours'],

                        'is_rest_day' =>
                            isset($record['is_rest_day']),

                        'is_holiday' =>
                            isset($record['is_holiday']),

                        'remarks' =>
                            $record['remarks'] ?? null,

                        'special_holiday' =>
                            isset($record['special_holiday']),

                        'is_extended_hours' =>
                            isset($record['is_extended_hours']),
                    ]
                );
            }
        });

        return redirect()
            ->route('dtr.bulk.create')
            ->with(
                'success',
                'DTR records saved successfully.'
            );
    }

    private function calculateDtr(array $record)
    {
        $regularLimit = 8;

        $workDate =
            Carbon::parse(
                $record['work_date']
            );

        /*
        |--------------------------------------------------------------------------
        | Schedule
        |--------------------------------------------------------------------------
        */

        $operationStart =
            Carbon::parse(
                $workDate->format('Y-m-d')
                .' '.
                $record['operation_start']
            );

        $operationEnd =
            Carbon::parse(
                $workDate->format('Y-m-d')
                .' '.
                $record['operation_end']
            );

        if ($operationEnd->lt($operationStart)) {
            $operationEnd->addDay();
        }

        /*
        |--------------------------------------------------------------------------
        | Actual Attendance
        |--------------------------------------------------------------------------
        */

        $timeIn =
            Carbon::parse(
                $workDate->format('Y-m-d')
                .' '.
                $record['time_in']
            );

        $timeOut =
            Carbon::parse(
                $workDate->format('Y-m-d')
                .' '.
                $record['time_out']
            );

        if ($timeOut->lt($timeIn)) {
            $timeOut->addDay();
        }

        $breakMinutes =
            (int)($record['break_minutes'] ?? 60);

        /*
        |--------------------------------------------------------------------------
        | Scheduled Hours
        |--------------------------------------------------------------------------
        */

        $scheduledHours =
            (
                $operationStart->diffInMinutes(
                    $operationEnd
                )
                -
                $breakMinutes
            ) / 60;

        /*
        |--------------------------------------------------------------------------
        | Worked Hours
        |--------------------------------------------------------------------------
        */

        $workedHours =
            (
                $timeIn->diffInMinutes(
                    $timeOut
                )
                -
                $breakMinutes
            ) / 60;

        /*
        |--------------------------------------------------------------------------
        | Late
        |--------------------------------------------------------------------------
        */

        $lateHours = 0;

        if ($timeIn->gt($operationStart)) {

            $lateHours =
                $operationStart
                    ->diffInMinutes(
                        $timeIn
                    ) / 60;
        }

        /*
        |--------------------------------------------------------------------------
        | Undertime
        |--------------------------------------------------------------------------
        */

        $undertimeHours = 0;

        if ($timeOut->lt($operationEnd)) {

            $undertimeHours =
                $timeOut
                    ->diffInMinutes(
                        $operationEnd
                    ) / 60;
        }

        /*
        |--------------------------------------------------------------------------
        | Regular Hours
        |--------------------------------------------------------------------------
        */
        $payableHours = max(
            $workedHours - $lateHours - $undertimeHours,
            0
        );

        $regularHours = min(
            $payableHours,
            $regularLimit
        );


        /*
        |--------------------------------------------------------------------------
        | Extended Hours
        |--------------------------------------------------------------------------
        |
        | Hours worked after operation_end.
        |
        */

        $extendedHours = 0;

        if ($operationEnd && $timeOut->greaterThan($operationEnd)) {

            $extendedHours = round(
                $operationEnd->diffInMinutes($timeOut) / 60,
                2
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Overtime
        |--------------------------------------------------------------------------
        */

        $overtimeHours = max(
            $payableHours - $regularLimit,
            0
        );

        if($extendedHours != 0) {
            $overtimeHours -= $extendedHours;
        }

        /*
        |--------------------------------------------------------------------------
        | Night Differential
        |--------------------------------------------------------------------------
        */

        $nightDifferentialHours =
            $this->calculateND(
                $timeIn,
                $timeOut
            );

        return [

            'operation_start' =>
                $operationStart,

            'operation_end' =>
                $operationEnd,

            'time_in' =>
                $timeIn,

            'time_out' =>
                $timeOut,

            'scheduled_hours' =>
                round(
                    $scheduledHours,
                    2
                ),

            'worked_hours' =>
                round(
                    $workedHours,
                    2
                ),

            'regular_hours' =>
                round(
                    $regularHours,
                    2
                ),

            'overtime_hours' =>
                round(
                    $overtimeHours,
                    2
                ),

            'extended_hours' =>
                round(
                    $extendedHours,
                    2
                ),

            'late_hours' =>
                round(
                    $lateHours,
                    2
                ),

            'undertime_hours' =>
                round(
                    $undertimeHours,
                    2
                ),

            'night_differential_hours' =>
                round(
                    $nightDifferentialHours,
                    2
                ),
        ];
    }

    private function calculateND(
        Carbon $timeIn,
        Carbon $timeOut
    ): float
    {
        $minutes = 0;

        $current = $timeIn->copy();

        while ($current < $timeOut) {

            $hour = $current->hour;

            if (
                $hour >= 22 ||
                $hour < 6
            ) {
                $minutes++;
            }

            $current->addMinute();
        }

        return $minutes / 60;
    }

    public function assignments(User $employee)
    {
        return response()->json([
            'areas' => $this->getAssignedAreasWithBranches($employee),
        ]);
    }

    /**
     * Return only the employee's assigned areas and assigned branches.
     * Branch assignments are stored separately from area assignments, so
     * loading Area::branches directly would expose unassigned branches.
     */
    private function getAssignedAreasWithBranches(User $employee)
    {
        // Branches are assigned through branch_user independently of area_user.
        // Include the parent areas of assigned branches even when the guard was
        // not separately added to the area's user assignment list.
        $assignedBranches = $employee->branches()->get();
        $branchesByArea = $assignedBranches->groupBy('area_id');

        $assignedAreaIds = $employee->areas()->pluck('areas.id');
        $branchAreaIds = $assignedBranches->pluck('area_id')->filter();
        $areaIds = $assignedAreaIds->merge($branchAreaIds)->unique()->values();

        if ($areaIds->isEmpty()) {
            return collect();
        }

        return Area::whereIn('id', $areaIds)->orderBy('name')->get()
            ->map(function ($area) use ($branchesByArea, $employee) {
                $area->setRelation(
                    'branches',
                    $branchesByArea->get($area->id, collect())->values()
                );
                $area->assigned_companies = DB::table('area_user_company')
                    ->join('companies', 'companies.id', '=', 'area_user_company.company_id')
                    ->where('area_user_company.area_id', $area->id)
                    ->where('area_user_company.user_id', $employee->id)
                    ->where('companies.active', 1)
                    ->orderBy('companies.name')
                    ->get(['companies.id', 'companies.name']);

                return $area;
            })->values();
    }
}
