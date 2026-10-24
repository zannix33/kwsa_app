<?php

namespace App\Http\Controllers;

use App\Models\PayrollPeriod;
use App\Services\Billing\BillingGuardBillingDataService;
use Illuminate\Http\Request;

class BillingPreviewController extends Controller
{
    public function show(Request $request, PayrollPeriod $period, BillingGuardBillingDataService $service)
    {
        $data = $service->buildForPeriod($period);

        $areaId = $request->query('area_id');
        $areas = collect($data['areas']);
        if ($areaId !== null) {
            $areas = $areas->filter(fn ($area) => (string) $area['area_id'] === (string) $areaId);
        }

        abort_if($areas->isEmpty(), 404, 'No billing data for this area.');

        // One printable statement per area, as in the supplied workbook.
        return view('pages.billing.excel-preview', [
            'period' => $data['period'],
            'areas' => $areas->values()->all(),
        ]);
    }
}
