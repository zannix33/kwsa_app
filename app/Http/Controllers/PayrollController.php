<?php

namespace App\Http\Controllers;

use Illuminate\Support\Facades\DB;

use App\Models\Payroll;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollController extends Controller
{
    public function index()
    {
        $payrolls = Payroll::with([
            'user',
            'period'
        ])
            ->latest()
            ->paginate(20);

        return view(
            'pages.payrolls.index',
            compact('payrolls')
        );
    }

    public function show(
        Payroll $payroll
    )
    {
        $payroll->load([
            'user',
            'period',
            'items'
        ]);

        return view(
            'pages.payrolls.show',
            compact('payroll')
        );
    }

    public function markPaid(
        Payroll $payroll
    )
    {
        DB::transaction(function () use ($payroll) {
            $payroll->update(['status' => 'Paid']);

            $applications = \App\Models\EmployeeDeductionPeriod::where('payroll_id', $payroll->id)
                ->where('status', 'Scheduled')->get();

            foreach ($applications as $application) {
                $application->update(['status' => 'Processed', 'processed_at' => now()]);
                $schedule = $application->deduction;
                if (!$schedule || $schedule->payment_status === 'Fully Paid') {
                    continue;
                }

                $limit = (int) $schedule->installment_count;
                $processedCount = $schedule->periods()->where('status', 'Processed')->count();

                // A zero count means ongoing: only manual Paid/Inactive can stop it.
                if ($limit > 0 && $processedCount >= $limit) {
                    $schedule->update(['payment_status' => 'Fully Paid', 'status' => 'Inactive']);
                }
            }
        });

        return back()->with(
            'success',
            'Payroll marked as paid.'
        );
    }

    public function destroy(
        Payroll $payroll
    )
    {
        if(
            $payroll->status === 'Paid'
        ){
            return back()->with(
                'error',
                'Paid payroll cannot be deleted.'
            );
        }

        $payroll->delete();

        return back()->with(
            'success',
            'Payroll deleted.'
        );
    }

    public function pdf(Payroll $payroll)
    {
        $payroll->load([
            'user',
            'period',
            'items'
        ]);

        $pdf = PDF::loadView(
            'pages.payrolls.pdf',
            compact('payroll')
        );

        return $pdf->download(
            'Payslip-'.$payroll->id.'.pdf'
        );
    }
}
