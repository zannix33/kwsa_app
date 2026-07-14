<?php

namespace App\Services\Arms;

use App\Models\Arm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ArmService
{
    /**
     * DataTable
     */
    public function datatable(Request $request)
    {
        $query = Arm::with([
            'branch',
            'currentAssignment.user',
            'activeLicense',
            'latestInspection'
        ]);

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('type')) {
            $query->where('type', $request->type);
        }

        if ($request->filled('caliber')) {
            $query->where('caliber', $request->caliber);
        }

        return DataTables::eloquent($query)

            ->addColumn('photo', function ($arm) {

                if ($arm->photo && Storage::disk('public')->exists($arm->photo)) {
                    return '<img src="'.asset('storage/'.$arm->photo).'" width="60" class="img-thumbnail">';
                }

                return '<span class="text-muted">No Photo</span>';
            })

            ->rawColumns(['photo'])

            ->addColumn('branch', function ($arm) {
                return optional($arm->branch)->name;
            })

            ->addColumn('holder', function ($arm) {
                return optional($arm->currentHolder)->name ?? '-';
            })

            /*->addColumn('license', function ($arm) {
                return optional($arm->activeLicense)->license_number;
            })

            ->addColumn('license_expiry', function ($arm) {
                return optional($arm->activeLicense?->expiry_date)
                    ? $arm->activeLicense->expiry_date->format('M d, Y')
                    : '-';
            })*/

            //->addColumn('actions', function ($arm) {
            //    return view('pages.arms.datatables.actions', compact('arm'));
            //})

            ->rawColumns(['actions'])

            ->make(true);
    }

    /**
     * Store
     */
    public function store(Request $request)
    {
        $data = $this->validate($request);

        return Arm::create($data);
    }

    /**
     * Update
     */
    public function update(Arm $arm, Request $request)
    {
        $data = $this->validate($request, $arm->id);

        $arm->update($data);

        return $arm;
    }

    /**
     * Delete
     */
    public function destroy(Arm $arm)
    {
        if ($arm->currentAssignment()->exists()) {
            abort(422, 'Firearm is currently issued.');
        }

        $arm->delete();
    }

    /**
     * Bulk retire
     */
    public function bulkRetire(array $ids)
    {
        Arm::whereIn('id', $ids)
            ->update([
                'status' => 'Retired'
            ]);
    }

    /**
     * Bulk mark available
     */
    public function bulkAvailable(array $ids)
    {
        Arm::whereIn('id', $ids)
            ->update([
                'status' => 'Available'
            ]);
    }

    /**
     * Bulk transfer
     */
    public function bulkTransfer(array $ids, $branchId)
    {
        Arm::whereIn('id', $ids)
            ->update([
                'branch_id' => $branchId
            ]);
    }

    /**
     * Dashboard
     */
    public function dashboard()
    {
        return [

            'total' => Arm::count(),

            'available' => Arm::available()->count(),

            'issued' => Arm::issued()->count(),

            'maintenance' => Arm::maintenance()->count(),

            'lost' => Arm::lost()->count(),

            'retired' => Arm::retired()->count(),

        ];
    }

    /**
     * Validation
     */
    protected function validate(Request $request, $id = null)
    {
        return Validator::make(
            $request->all(),
            [

                'property_no' => [
                    'required',
                    'max:255',
                    'unique:arms,property_no,' . $id
                ],

                'serial_no' => [
                    'required',
                    'max:255',
                    'unique:arms,serial_no,' . $id
                ],

                'model' => 'required|max:255',

                'caliber' => 'required|max:255',

                'type' => 'required',

                'color' => 'nullable|max:255',

                'purchase_date' => 'nullable|date',

                'purchase_cost' => 'nullable|numeric|min:0',

                'supplier' => 'nullable|max:255',

                'manufacturer' => 'nullable|max:255',

                'branch_id' => 'nullable|exists:branches,id',

                'status' => 'required',

                'remarks' => 'nullable'

            ]
        )->validate();
    }
}
