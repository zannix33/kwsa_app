<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\PayrollPeriod;
use App\Services\Billing\BillingGenerationService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BillingController extends Controller
{
    public function index()
    {
        $closedPeriods = PayrollPeriod::whereRaw('LOWER(status) = ?', ['closed'])
            ->orderByDesc('date_from')
            ->get();

        $billings = Billing::with(['client', 'area', 'payrollPeriod'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('pages.billing.index', compact('closedPeriods', 'billings'));
    }

    /**
     * Landing-page action: take the user to the Excel-style preview
     * for a closed period and a selected area.
     */
    public function preview(Request $request)
    {
        $validated = $request->validate([
            'payroll_period_id' => ['required', 'integer', 'exists:payroll_periods,id'],
            'area_id' => ['required', 'integer', 'exists:areas,id'],
        ]);

        $period = PayrollPeriod::findOrFail($validated['payroll_period_id']);
        $this->ensurePeriodClosed($period);

        return redirect()->route('billing.excel-preview', [
            'period' => $period->id,
            'area_id' => $validated['area_id'],
        ]);
    }

    public function generate(Request $request, BillingGenerationService $billingGenerationService)
    {
        $validated = $request->validate([
            'payroll_period_id' => ['required', 'integer', 'exists:payroll_periods,id'],
        ]);

        $period = PayrollPeriod::findOrFail($validated['payroll_period_id']);
        $this->ensurePeriodClosed($period);

        $createdBills = $billingGenerationService->generate($period);

        return redirect()->route('billing.index')
            ->with('success', $createdBills->count() . ' draft bill(s) generated successfully.');
    }

    public function show(Billing $billing)
    {
        $billing->load(['client', 'area', 'payrollPeriod', 'items.branch', 'items.user', 'items.payrollItem']);
        return view('pages.billing.show', compact('billing'));
    }

    /** Show a print-optimized version of a bill. */
    public function print(Billing $billing)
    {
        $billing->load(['client', 'area', 'payrollPeriod', 'items.branch', 'items.user']);
        return view('pages.billing.print', compact('billing'));
    }

    private function ensurePeriodClosed(PayrollPeriod $period): void
    {
        if (strtolower((string) $period->status) !== 'closed') {
            throw ValidationException::withMessages([
                'payroll_period_id' => 'Billing is only available after the payroll period is closed.',
            ]);
        }
    }
}
