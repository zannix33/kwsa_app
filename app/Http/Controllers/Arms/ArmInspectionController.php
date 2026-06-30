<?php

namespace App\Http\Controllers\Arms;

use App\Http\Controllers\Controller;
use App\Models\Arm;
use App\Models\ArmInspection;
use App\Services\Arms\ArmInspectionService;
use Illuminate\Http\Request;

class ArmInspectionController extends Controller
{
    protected ArmInspectionService $service;

    public function __construct(ArmInspectionService $service)
    {
        $this->service = $service;
    }

    /**
     * Inspection History
     */
    public function index()
    {
        return view('arms.inspections.index');
    }

    /**
     * DataTable
     */
    public function datatable(Request $request)
    {
        return $this->service->datatable($request);
    }

    /**
     * Create Inspection
     */
    public function create()
    {
        return view('arms.inspections.create', [

            'arms' => Arm::orderBy('make')
                ->orderBy('model')
                ->get()

        ]);
    }

    /**
     * Store Inspection
     */
    public function store(Request $request)
    {
        $inspection = $this->service->store($request);

        return redirect()

            ->route(
                'arms.inspections.show',
                $inspection
            )

            ->with(
                'success',
                'Inspection successfully recorded.'
            );
    }

    /**
     * Inspection Details
     */
    public function show(
        ArmInspection $inspection
    )
    {
        $inspection->load('arm');

        return view(
            'arms.inspections.show',
            compact('inspection')
        );
    }

    /**
     * Edit Inspection
     */
    public function edit(
        ArmInspection $inspection
    )
    {
        return view(
            'arms.inspections.edit',
            [

                'inspection' => $inspection,

                'arms' => Arm::orderBy('make')
                    ->orderBy('model')
                    ->get()

            ]
        );
    }

    /**
     * Update Inspection
     */
    public function update(
        Request $request,
        ArmInspection $inspection
    )
    {
        $this->service->update(
            $inspection,
            $request
        );

        return redirect()

            ->route(
                'arms.inspections.index'
            )

            ->with(
                'success',
                'Inspection updated.'
            );
    }

    /**
     * Delete Inspection
     */
    public function destroy(
        ArmInspection $inspection
    )
    {
        $this->service->destroy(
            $inspection
        );

        return back()->with(
            'success',
            'Inspection deleted.'
        );
    }

    /**
     * Due Inspections
     */
    public function due()
    {
        return view(
            'arms.inspections.due'
        );
    }

    /**
     * Firearm Inspection History
     */
    public function firearm(
        Arm $arm
    )
    {
        return view(
            'arms.inspections.firearm',
            compact('arm')
        );
    }

    /**
     * Failed Inspections
     */
    public function failed()
    {
        return view(
            'arms.inspections.failed'
        );
    }

    /**
     * Passed Inspections
     */
    public function passed()
    {
        return view(
            'arms.inspections.passed'
        );
    }
}
