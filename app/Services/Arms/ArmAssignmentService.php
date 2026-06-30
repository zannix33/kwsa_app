<?php

namespace App\Services\Arms;

use App\Models\Arm;
use App\Models\ArmAssignment;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ArmAssignmentService
{
    /**
     * DataTable
     */
    public function datatable(Request $request)
    {
        $query = ArmAssignment::with([
            'arm',
            'user',
            'branch'
        ]);

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('status')) {

            if ($request->status == 'Current') {
                $query->current();
            }

            if ($request->status == 'Returned') {
                $query->returned();
            }
        }

        return DataTables::eloquent($query)

            ->addColumn('firearm', function ($row) {
                return $row->arm?->full_name;
            })

            ->addColumn('employee', function ($row) {
                return $row->user?->name;
            })

            ->addColumn('branch', function ($row) {
                return $row->branch?->name;
            })

            ->addColumn('status', function ($row) {
                return $row->is_returned
                    ? 'Returned'
                    : 'Issued';
            })

            ->addColumn('actions', function ($row) {
                return view(
                    'arms.assignments.datatables.actions',
                    compact('row')
                );
            })

            ->rawColumns(['actions'])

            ->make(true);
    }

    /**
     * Issue firearm
     */
    public function store(Request $request)
    {
        $data = $this->validate($request);

        return DB::transaction(function () use ($data) {

            $arm = Arm::lockForUpdate()->findOrFail($data['arm_id']);

            if ($arm->status != 'Available') {
                abort(422, 'Firearm is not available.');
            }

            if ($arm->currentAssignment()->exists()) {
                abort(422, 'Firearm is already assigned.');
            }

            $assignment = ArmAssignment::create([

                'arm_id' => $data['arm_id'],

                'user_id' => $data['user_id'],

                'branch_id' => $data['branch_id'],

                'issued_at' => now(),

                'issued_by' => auth()->user()->name,

                'reference_no' => $data['reference_no'] ?? null,

                'condition_before' => $data['condition_before'],

                'ammo_issued' => $data['ammo_issued'],

                'remarks' => $data['remarks'] ?? null,

            ]);

            $arm->markIssued();

            return $assignment;

        });
    }

    /**
     * Return firearm
     */
    public function returnFirearm(
        ArmAssignment $assignment,
        Request $request
    ) {

        $validated = Validator::make(
            $request->all(),
            [

                'condition_after' => 'required',

                'ammo_returned' => 'required|integer|min:0',

                'ammo_remarks' => 'nullable',

                'remarks' => 'nullable'

            ]
        )->validate();

        DB::transaction(function () use (
            $assignment,
            $validated
        ) {

            $assignment->returnFirearm($validated);

        });

        return $assignment->fresh();
    }

    /**
     * Delete history
     */
    public function destroy(
        ArmAssignment $assignment
    ) {

        if (!$assignment->is_returned) {

            abort(
                422,
                'Cannot delete an active assignment.'
            );

        }

        $assignment->delete();
    }

    /**
     * Dashboard
     */
    public function dashboard()
    {
        return [

            'issued' => ArmAssignment::current()->count(),

            'returned_today' => ArmAssignment::returnedToday()->count(),

            'issued_today' => ArmAssignment::issuedToday()->count(),

            'total_history' => ArmAssignment::count(),

        ];
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

                'user_id' => [
                    'required',
                    'exists:users,id'
                ],

                'branch_id' => [
                    'nullable',
                    'exists:branches,id'
                ],

                'reference_no' => 'nullable|max:100',

                'condition_before' => 'required',

                'ammo_issued' => 'required|integer|min:0',

                'remarks' => 'nullable'

            ]
        )->validate();

    }

    /**
     * Bulk return
     */
    public function bulkReturn(
        array $assignmentIds,
        array $data
    ) {

        DB::transaction(function () use (
            $assignmentIds,
            $data
        ) {

            $assignments = ArmAssignment::whereIn(
                'id',
                $assignmentIds
            )->current()->get();

            foreach ($assignments as $assignment) {

                $assignment->returnFirearm($data);

            }

        });

    }

    /**
     * Employee assignment history
     */
    public function employeeHistory(
        $userId
    ) {

        return ArmAssignment::with('arm')

            ->where(
                'user_id',
                $userId
            )

            ->latest('issued_at')

            ->get();

    }

    /**
     * Firearm assignment history
     */
    public function firearmHistory(
        $armId
    ) {

        return ArmAssignment::with('user')

            ->where(
                'arm_id',
                $armId
            )

            ->latest('issued_at')

            ->get();

    }
}
