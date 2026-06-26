<?php

namespace App\Http\Controllers;

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
        $payroll->update([
            'status' => 'Paid'
        ]);

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
