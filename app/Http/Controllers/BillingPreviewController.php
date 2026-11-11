<?php

namespace App\Http\Controllers;

use App\Models\PayrollPeriod;
use App\Services\Billing\BillingExcelPreviewService;
use Illuminate\Http\Request;

class BillingPreviewController extends Controller
{
    public function show(Request $request, PayrollPeriod $period, BillingExcelPreviewService $service)
    {
        $data = $service->build($period);
        $areaId = $request->query('area_id');
        $areas = collect($data['areas']);

        if ($areaId !== null) {
            $areas = $areas->filter(function ($area) use ($areaId) {
                return (string) $area['area_id'] === (string) $areaId;
            });
        }

        abort_if($areas->isEmpty(), 404, 'No billing data for this area.');

        // The preview renders one independent printable bill per branch.
        return view('pages.billing.excel-preview', [
            'data' => $data,
            'period' => $data['period'],
            'areas' => $areas->values()->all(),
            'company' => null,
        ]);
    }
}
