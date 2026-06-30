<?php

namespace App\Http\Controllers;

use App\Services\Payroll\PayrollGenerationService;
use Illuminate\Http\Request;

use App\Models\Area;

class ThirteenthMonthController extends Controller
{
    public function index()
    {
        return view('pages.reports.thirteenth-month.index',[
            'areas'=>Area::orderBy('name')->get()
        ]);
    }

    public function generate(Request $request, PayrollGenerationService $service)
    {
        $request->validate([
            'area_id'=>'required',
            'from'=>'required|date',
            'to'=>'required|date'
        ]);

        $report = $service->generate13thMonth(
            $request->area_id,
            $request->from,
            $request->to
        );

        return view(
            'pages.reports.thirteenth-month.report',
            compact('report')
        );
    }

}
