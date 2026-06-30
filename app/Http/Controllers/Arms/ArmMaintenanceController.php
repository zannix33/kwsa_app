<?php

namespace App\Http\Controllers\Arms;

use App\Http\Controllers\Controller;
use App\Models\Arm;
use App\Models\ArmMaintenance;
use App\Services\Arms\ArmMaintenanceService;
use Illuminate\Http\Request;

class ArmMaintenanceController extends Controller
{
    protected ArmMaintenanceService $service;

    public function __construct(ArmMaintenanceService $service)
    {
        $this->service = $service;
    }

    /**
     * Maintenance History
     */
    public function index()
    {
        return view('arms.maintenances.index');
    }

    /**
     * DataTable
     */
    public function datatable(Request $request)
    {
        return $this->service->datatable($request);
    }

    /**
     * Create Form
     */
    public function create()
    {
        return view('arms.maintenances.create', [

            'arms' => Arm::orderBy('make')
                ->orderBy('model')
                ->get()

        ]);
    }

    /**
     * Store
     */
    public function store(Request $request)
    {
        $maintenance = $this->service->store($request);

        return redirect()

            ->route(
                'arms.maintenances.show',
                $maintenance
            )

            ->with(
                'success',
                'Maintenance record created.'
            );
    }

    /**
     * Details
     */
    public function show(
        ArmMaintenance $maintenance
    ) {

        $maintenance->load('arm');

        return view(
            'arms.maintenances.show',
            compact('maintenance')
        );

    }

    /**
     * Edit Form
     */
    public function edit(
        ArmMaintenance $maintenance
    ) {

        return view(
            'arms.maintenances.edit',
            [

                'maintenance' => $maintenance,

                'arms' => Arm::orderBy('make')
                    ->orderBy('model')
                    ->get()

            ]
        );

    }

    /**
     * Update
     */
    public function update(
        Request $request,
        ArmMaintenance $maintenance
    ) {

        $this->service->update(
            $maintenance,
            $request
        );

        return redirect()

            ->route(
                'arms.maintenances.index'
            )

            ->with(
                'success',
                'Maintenance updated.'
            );

    }

    /**
     * Delete
     */
    public function destroy(
        ArmMaintenance $maintenance
    ) {

        $this->service->destroy($maintenance);

        return back()->with(
            'success',
            'Maintenance deleted.'
        );

    }

    /**
     * Complete Maintenance
     */
    public function complete(
        ArmMaintenance $maintenance
    ) {

        $this->service->complete(
            $maintenance
        );

        return back()->with(
            'success',
            'Maintenance completed.'
        );

    }

    /**
     * Due Maintenance
     */
    public function due()
    {
        return view(
            'arms.maintenances.due'
        );
    }

    /**
     * Firearm History
     */
    public function firearm(
        Arm $arm
    ) {

        return view(
            'arms.maintenances.firearm',
            compact('arm')
        );

    }

}
