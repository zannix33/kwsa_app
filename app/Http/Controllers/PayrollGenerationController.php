<?php

namespace App\Http\Controllers;

use App\Models\PayrollPeriod;
use App\Services\Payroll\PayrollGenerationService;
use Illuminate\Support\Facades\Log;
use Throwable;

class PayrollGenerationController extends Controller
{
    protected $generator;

    public function __construct(PayrollGenerationService $generator)
    {
        $this->generator = $generator;
    }

    public function generate(PayrollPeriod $payrollPeriod)
    {
        try {
            Log::info('Generate payroll request received', [
                'payroll_period_id' => $payrollPeriod->id,
                'status' => $payrollPeriod->status,
            ]);

            $this->generator->generate($payrollPeriod);

            return redirect()->back()->with(
                'success',
                'Payroll generated successfully.'
            );
        } catch (Throwable $e) {
            Log::error('Payroll generation controller failed', [
                'payroll_period_id' => $payrollPeriod->id,
                'exception' => get_class($e),
                'message' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
            ]);

            return redirect()->back()->with(
                'error',
                'Payroll generation failed. Check the Laravel log for details.'
            );
        }
    }
}
