<?php

namespace App\Http\Controllers;

use App\Models\PayrollPeriod;
use App\Services\Payroll\PayrollGenerationService;
use Illuminate\Http\Request;

class PayrollPeriodController extends Controller
{
    protected $generator;

    public function __construct(
        PayrollGenerationService $generator
    ) {
        $this->generator = $generator;
    }

    public function index()
    {
        $periods = PayrollPeriod::latest()->get();

        return view(
            'pages.payroll_periods.index',
            compact('periods')
        );
    }

    public function create()
    {
        return view('pages.payroll_periods.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from'
        ]);

        PayrollPeriod::create([
            'name' => date(
                    'F d',
                    strtotime($request->date_from)
                ) . ' - ' .
                date(
                    'd, Y',
                    strtotime($request->date_to)
                ),

            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'status' => 'Open'
        ]);

        return redirect()
            ->route('payroll-periods.index')
            ->with(
                'success',
                'Payroll period created.'
            );
    }

    public function edit(PayrollPeriod $payrollPeriod)
    {
        return view(
            'pages.payroll_periods.edit',
            compact('payrollPeriod')
        );
    }

    public function update(
        Request $request,
        PayrollPeriod $payrollPeriod
    )
    {
        $request->validate([
            'date_from' => 'required|date',
            'date_to' => 'required|date|after_or_equal:date_from',
            'status' => 'required'
        ]);

        $payrollPeriod->update([
            'date_from' => $request->date_from,
            'date_to' => $request->date_to,
            'status' => $request->status,

            'name' =>
                date(
                    'F d',
                    strtotime($request->date_from)
                )
                .' - '.
                date(
                    'd, Y',
                    strtotime($request->date_to)
                )
        ]);

        return redirect()
            ->route('payroll-periods.index')
            ->with(
                'success',
                'Payroll period updated.'
            );
    }

    public function destroy(
        PayrollPeriod $payrollPeriod
    )
    {
        if(
            $payrollPeriod
                ->payrolls()
                ->count()
        ){
            return back()->with(
                'error',
                'Cannot delete payroll period with payroll records.'
            );
        }

        $payrollPeriod->delete();

        return back()->with(
            'success',
            'Payroll period deleted.'
        );
    }

    public function generate(
        PayrollPeriod $payrollPeriod
    ) {
        $this->generator->generate(
            $payrollPeriod
        );

        return back()->with(
            'success',
            'Payroll generated successfully.'
        );
    }
}
