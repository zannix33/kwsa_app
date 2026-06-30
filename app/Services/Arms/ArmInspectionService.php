<?php

namespace App\Services\Arms;

use App\Models\Arm;
use App\Models\ArmInspection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ArmInspectionService
{
    /**
     * DataTable
     */
    public function datatable(Request $request)
    {
        $query = ArmInspection::with('arm');

        if ($request->filled('arm_id')) {
            $query->where('arm_id', $request->arm_id);
        }

        if ($request->filled('inspection_type')) {
            $query->where(
                'inspection_type',
                $request->inspection_type
            );
        }

        if ($request->filled('result')) {
            $query->where(
                'result',
                $request->result
            );
        }

        if ($request->filled('requires_maintenance')) {
            $query->where(
                'requires_maintenance',
                $request->requires_maintenance
            );
        }

        return DataTables::eloquent($query)

            ->addColumn('firearm', function ($row) {
                return optional($row->arm)->full_name;
            })

            ->addColumn('status', function ($row) {
                return $row->result;
            })

            ->addColumn('maintenance', function ($row) {
                return $row->requires_maintenance
                    ? 'Required'
                    : 'Not Required';
            })

            ->addColumn('actions', function ($row) {
                return view(
                    'arms.inspections.datatables.actions',
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

            $inspection = ArmInspection::create($data);

            return $inspection->fresh();

        });
    }

    /**
     * Update
     */
    public function update(
        ArmInspection $inspection,
        Request $request
    ) {

        $data = $this->validate($request);

        return DB::transaction(function () use (
            $inspection,
            $data
        ) {

            $inspection->update($data);

            return $inspection->fresh();

        });

    }

    /**
     * Delete
     */
    public function destroy(
        ArmInspection $inspection
    ) {

        $inspection->delete();

    }

    /**
     * Dashboard
     */
    public function dashboard()
    {
        return [

            'total' => ArmInspection::count(),

            'passed' => ArmInspection::passedCount(),

            'failed' => ArmInspection::failedCount(),

            'maintenance_required' =>
                ArmInspection::maintenanceRequiredCount(),

            'due' => ArmInspection::dueCount()

        ];
    }

    /**
     * Firearm Inspection History
     */
    public function firearmHistory(
        Arm $arm
    ) {

        return $arm->inspections()

            ->latest('inspection_date')

            ->get();

    }

    /**
     * Due Inspections
     */
    public function dueInspections()
    {
        return ArmInspection::due()

            ->with('arm')

            ->orderBy('next_inspection')

            ->get();
    }

    /**
     * Failed Inspections
     */
    public function failedInspections()
    {
        return ArmInspection::failed()

            ->with('arm')

            ->latest('inspection_date')

            ->get();
    }

    /**
     * Passed Inspections
     */
    public function passedInspections()
    {
        return ArmInspection::passed()

            ->with('arm')

            ->latest('inspection_date')

            ->get();
    }

    /**
     * Bulk Schedule Next Inspection
     */
    public function scheduleNextInspection(
        array $inspectionIds,
              $nextInspectionDate
    ) {

        ArmInspection::whereIn(
            'id',
            $inspectionIds
        )->update([

            'next_inspection' => $nextInspectionDate

        ]);

    }

    /**
     * Annual Inspection Report
     */
    public function annualReport(
        $year
    ) {

        return ArmInspection::selectRaw(
            "
                MONTH(inspection_date) as month,
                COUNT(*) as inspections,
                SUM(CASE WHEN result='Passed' THEN 1 ELSE 0 END) as passed,
                SUM(CASE WHEN result='Failed' THEN 1 ELSE 0 END) as failed
                "
        )

            ->whereYear(
                'inspection_date',
                $year
            )

            ->groupByRaw(
                'MONTH(inspection_date)'
            )

            ->orderByRaw(
                'MONTH(inspection_date)'
            )

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

                'inspection_date' => [
                    'required',
                    'date'
                ],

                'inspection_type' => [
                    'required',
                    'max:100'
                ],

                'inspector' => [
                    'required',
                    'max:255'
                ],

                'barrel_condition' => [
                    'required'
                ],

                'slide_condition' => [
                    'required'
                ],

                'frame_condition' => [
                    'required'
                ],

                'trigger_condition' => [
                    'required'
                ],

                'magazine_condition' => [
                    'required'
                ],

                'sight_condition' => [
                    'required'
                ],

                'overall_condition' => [
                    'required'
                ],

                'findings' => [
                    'nullable'
                ],

                'recommendation' => [
                    'nullable'
                ],

                'next_inspection' => [
                    'nullable',
                    'date'
                ],

                'remarks' => [
                    'nullable'
                ]

            ]
        )->validate();

    }
}
