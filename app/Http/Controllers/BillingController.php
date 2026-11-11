<?php

namespace App\Http\Controllers;

use App\Models\Billing;
use App\Models\Client;
use App\Models\PayrollPeriod;
use App\Services\Billing\BillingGenerationService;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;

class BillingController extends Controller
{
    public function index()
    {
        $clients = Client::orderBy('name')->get();
        $closedPeriods = PayrollPeriod::whereRaw('LOWER(status) = ?', ['closed'])
            ->orderByDesc('date_from')
            ->get();

        $billings = Billing::with(['client', 'company', 'area', 'payrollPeriod', 'items'])
            ->orderByDesc('created_at')
            ->paginate(20);

        return view('pages.billing.index', compact('closedPeriods', 'billings', 'clients'));
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
            'client_id' => ['required', 'integer', 'exists:clients,id'],
        ]);

        $period = PayrollPeriod::findOrFail($validated['payroll_period_id']);
        $this->ensurePeriodClosed($period);

        $result = $billingGenerationService->generate($period, (int) $validated['client_id']);
        $createdCount = $result['created']->count();
        $updatedCount = $result['updated']->count();
        $skippedCount = $result['skipped']->count();
        $redirect = redirect()->route('billing.index')
            ->with('success', "Billing reprocessed. {$createdCount} bill(s) created, {$updatedCount} draft bill(s) updated, and {$skippedCount} non-draft bill(s) protected.");
        if (!empty($result['unassigned_branches'])) {
            $redirect->with('unassigned_branches', $result['unassigned_branches']);
        }
        return $redirect;
    }

    public function show(Billing $billing)
    {
        $billing->load(['client', 'company', 'area', 'payrollPeriod', 'branch', 'items.branch', 'items.user', 'items.payrollItem']);
        return view('pages.billing.show', compact('billing'));
    }

    /** Show a print-optimized version of a bill. */
    public function print(Billing $billing)
    {
        $billing->load(['client', 'company', 'area', 'payrollPeriod', 'branch', 'items.branch', 'items.user']);
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
