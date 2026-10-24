<?php

namespace App\Http\Controllers;

use App\Models\PayrollPeriod;
use App\Services\Billing\BillingExcelPreviewService;
use Illuminate\Http\Request;

class BillingExcelPreviewController extends Controller
{
    public function show(Request $request, PayrollPeriod $period, BillingExcelPreviewService $preview)
    {
        $data = $preview->build($period);
        $areaId = $request->query('area_id');
        $areas = collect($data['areas']);

        if ($areaId !== null) {
            $areas = $areas->filter(fn ($area) => (string) $area['area_id'] === (string) $areaId);
            abort_if($areas->isEmpty(), 404, 'No billable area for this payroll period.');
        }

        return view('pages.billing.excel-preview', [
            'data' => $data,
            'areas' => $areas,
            'period' => $period,
        ]);
    }
}
