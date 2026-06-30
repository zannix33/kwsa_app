<?php

namespace App\Services\Arms;

use App\Models\Arm;
use App\Models\ArmMaintenance;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ArmMaintenanceService
{
    /**
     * DataTable
     */
    public function datatable(Request $request)
    {
        $query = ArmMaintenance::with('arm');

        if ($request->filled('arm_id')) {
            $query->where('arm_id', $request->arm_id);
        }

        if ($request->filled('maintenance_type')) {
            $query->where(
                'maintenance_type',
                $request->maintenance_type
            );
        }

        if ($request->filled('completed')) {
            $query->where(
                'completed',
                $request->completed
            );
        }

        return DataTables::eloquent($query)

            ->addColumn('firearm', function ($row) {
                return optional($row->arm)->full_name;
            })

            ->addColumn('total_cost', function ($row) {
                return number_format(
                    $row->total_cost,
                    2
                );
            })

            ->addColumn('status', function ($row) {
                return $row->completed
                    ? 'Completed'
                    : 'Pending';
            })

            ->addColumn('actions', function ($row) {
                return view(
                    'arms.maintenances.datatables.actions',
                    compact('row')
                );
            })

            ->rawColumns(['actions'])

            ->make(true);
    }

    /**
     * Store
     */
    public function store(Request $request)
    {
        $data = $this->validate($request);

        return DB::transaction(function () use ($data) {

            $maintenance = ArmMaintenance::start($data);

            return $maintenance->fresh();

        });
    }

    /**
     * Update
     */
    public function update(
        ArmMaintenance $maintenance,
        Request $request
    ) {

        $data = $this->validate($request);

        return DB::transaction(function () use (
            $maintenance,
            $data
        ) {

            $maintenance->update($data);

            return $maintenance->fresh();

        });

    }

    /**
     * Complete
     */
    public function complete(
        ArmMaintenance $maintenance
    ) {

        DB::transaction(function () use (
            $maintenance
        ) {

            $maintenance->complete();

        });

        return $maintenance->fresh();

    }

    /**
     * Delete
     */
    public function destroy(
        ArmMaintenance $maintenance
    ) {

        if (!$maintenance->completed) {

            abort(
                422,
                'Pending maintenance cannot be deleted.'
            );

        }

        $maintenance->delete();

    }

    /**
     * Dashboard
     */
    public function dashboard()
    {
        return [

            'total' => ArmMaintenance::count(),

            'pending' => ArmMaintenance::pendingCount(),

            'completed' => ArmMaintenance::completedCount(),

            'due' => ArmMaintenance::dueCount(),

            'total_cost' => ArmMaintenance::totalMaintenanceCost(),

            'monthly_cost' => ArmMaintenance::monthlyMaintenanceCost()

        ];
    }

    /**
     * Firearm Maintenance History
     */
    public function firearmHistory(
        Arm $arm
    ) {

        return $arm->maintenances()
            ->latest('maintenance_date')
            ->get();

    }

    /**
     * Due Maintenance
     */
    public function dueMaintenances()
    {
        return ArmMaintenance::due()

            ->with('arm')

            ->orderBy('next_due')

            ->get();
    }

    /**
     * Validation
     */
    protected function validate(
        Request $request
    ) {

        return Validator::make(
            $request->all(),
            [

                'arm_id' => [
                    'required',
                    'exists:arms,id'
                ],

                'maintenance_date' => [
                    'required',
                    'date'
                ],

                'maintenance_type' => [
                    'required'
                ],

                'performed_by' => [
                    'required',
                    'max:255'
                ],

                'service_provider' => [
                    'nullable',
                    'max:255'
                ],

                'description' => [
                    'required'
                ],

                'parts_replaced' => [
                    'nullable'
                ],

                'labor_cost' => [
                    'nullable',
                    'numeric',
                    'min:0'
                ],

                'parts_cost' => [
                    'nullable',
                    'numeric',
                    'min:0'
                ],

                'condition_after' => [
                    'required'
                ],

                'next_due' => [
                    'nullable',
                    'date'
                ],

                'completed' => [
                    'boolean'
                ],

                'remarks' => [
                    'nullable'
                ]

            ]
        )->validate();

    }

    /**
     * Bulk Complete
     */
    public function bulkComplete(
        array $ids
    ) {

        DB::transaction(function () use (
            $ids
        ) {

            ArmMaintenance::whereIn(
                'id',
                $ids
            )->get()->each(function ($maintenance) {

                $maintenance->complete();

            });

        });

    }

    /**
     * Monthly Cost Report
     */
    public function monthlyReport(
        $year
    ) {

        return ArmMaintenance::selectRaw(
            'MONTH(maintenance_date) as month,
                 SUM(total_cost) as total_cost'
        )

            ->whereYear(
                'maintenance_date',
                $year
            )

            ->groupByRaw(
                'MONTH(maintenance_date)'
            )

            ->orderByRaw(
                'MONTH(maintenance_date)'
            )

            ->get();

    }
}
