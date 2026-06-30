<?php

namespace App\Http\Controllers\Arms;

use App\Http\Controllers\Controller;
use App\Services\Arms\ArmAssignmentService;
use App\Services\Arms\ArmInspectionService;
use App\Services\Arms\ArmLicenseService;
use App\Services\Arms\ArmMaintenanceService;
use App\Services\Arms\ArmService;
use App\Services\Arms\AmmunitionReleaseService;
use App\Services\Arms\AmmunitionService;

class ArmDashboardController extends Controller
{
    protected ArmService $armService;
    protected ArmAssignmentService $assignmentService;
    protected ArmMaintenanceService $maintenanceService;
    protected ArmInspectionService $inspectionService;
    protected ArmLicenseService $licenseService;
    protected AmmunitionService $ammunitionService;
    protected AmmunitionReleaseService $releaseService;

    public function __construct(
        ArmService $armService,
        ArmAssignmentService $assignmentService,
        ArmMaintenanceService $maintenanceService,
        ArmInspectionService $inspectionService,
        ArmLicenseService $licenseService,
        AmmunitionService $ammunitionService,
        AmmunitionReleaseService $releaseService
    ) {
        $this->armService = $armService;
        $this->assignmentService = $assignmentService;
        $this->maintenanceService = $maintenanceService;
        $this->inspectionService = $inspectionService;
        $this->licenseService = $licenseService;
        $this->ammunitionService = $ammunitionService;
        $this->releaseService = $releaseService;
    }

    /**
     * Dashboard
     */
    public function index()
    {
        return view('arms.dashboard', [

            'firearms' => $this->armService->dashboard(),

            'assignments' => $this->assignmentService->dashboard(),

            'maintenance' => $this->maintenanceService->dashboard(),

            'inspections' => $this->inspectionService->dashboard(),

            'licenses' => $this->licenseService->dashboard(),

            'ammunition' => $this->ammunitionService->dashboard(),

            'releases' => $this->releaseService->dashboard(),

            'lowStock' => $this->ammunitionService->lowStock(),

            'expiredLicenses' =>
                $this->licenseService->expiringLicenses(30),

            'dueMaintenance' =>
                $this->maintenanceService->dueMaintenances(),

            'dueInspections' =>
                $this->inspectionService->dueInspections(),

        ]);
    }

    /**
     * Dashboard Charts (AJAX)
     */
    public function charts()
    {
        return response()->json([

            'maintenance' =>
                $this->maintenanceService
                    ->monthlyReport(now()->year),

            'inspections' =>
                $this->inspectionService
                    ->annualReport(now()->year),

            'licenses' =>
                $this->licenseService
                    ->annualRenewalReport(now()->year),

        ]);
    }

    /**
     * Dashboard Summary API
     */
    public function summary()
    {
        return response()->json([

            'firearms' =>
                $this->armService->dashboard(),

            'assignments' =>
                $this->assignmentService->dashboard(),

            'maintenance' =>
                $this->maintenanceService->dashboard(),

            'inspections' =>
                $this->inspectionService->dashboard(),

            'licenses' =>
                $this->licenseService->dashboard(),

            'ammunition' =>
                $this->ammunitionService->dashboard(),

            'releases' =>
                $this->releaseService->dashboard(),

        ]);
    }
}
