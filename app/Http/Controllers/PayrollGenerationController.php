<?php

namespace App\Http\Controllers;


use App\Models\PayrollPeriod;
use App\Services\Payroll\PayrollGenerationService;


class PayrollGenerationController extends Controller
{
    protected $generator;

    public function __construct(
        PayrollGenerationService $generator
    ) {
        $this->generator = $generator;
    }

    public function generate(
        PayrollPeriod $payrollPeriod
    ) {
        $this->generator->generate(
            $payrollPeriod
        );

        return back()->with(
            'success',
            'Payroll generated.'
        );
    }
}
