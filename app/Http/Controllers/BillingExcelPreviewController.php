<?php
namespace App\Http\Controllers;
use App\Models\Company;
use App\Models\PayrollPeriod;
use App\Services\Billing\BillingExcelPreviewService;
use Illuminate\Http\Request;
class BillingExcelPreviewController extends Controller
{
    public function show(Request $request, PayrollPeriod $period, BillingExcelPreviewService $preview)
    {
        $data = $preview->build($period);
        $areaId = $request->query('area_id');
        $companyId = $request->query('company_id');
        $branchId = $request->query('branch_id');
        $areas = collect($data['areas'])->map(function ($area) use ($companyId, $branchId) {
            if ($companyId !== null) {
                $area['branches'] = array_values(array_filter($area['branches'], function ($branch) use ($companyId) {
                    return (string) ($branch['company_id'] ?? '') === (string) $companyId;
                }));
            }
            if ($branchId !== null) {
                $area['branches'] = array_values(array_filter($area['branches'], function ($branch) use ($branchId) {
                    return (string) ($branch['branch_id'] ?? '') === (string) $branchId;
                }));
            }
            $area['totals']['guard_count'] = array_sum(array_map(function ($branch) {
                return count($branch['guards']);
            }, $area['branches']));
            return $area;
        })->filter(function ($area) { return !empty($area['branches']); });
        if ($areaId !== null) {
            $areas = $areas->filter(function ($area) use ($areaId) {
                return (string) $area['area_id'] === (string) $areaId;
            });
            abort_if($areas->isEmpty(), 404, 'No billable company/area combination for this payroll period.');
        }
        $company = $companyId ? Company::findOrFail($companyId) : null;
        $branchCompanyIds = collect($areas)->flatMap(function ($area) {
            return collect($area['branches'])->pluck('company_id');
        })->filter()->unique()->values();
        $companies = Company::whereIn('id', $branchCompanyIds)->get()->keyBy('id');
        return view('pages.billing.excel-preview', compact('data', 'areas', 'period', 'company', 'companies'));
    }
}
