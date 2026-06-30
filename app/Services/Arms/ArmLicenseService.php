<?php

namespace App\Services\Arms;

use App\Models\Arm;
use App\Models\ArmLicense;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ArmLicenseService
{
    /**
     * DataTable
     */
    public function datatable(Request $request)
    {
        $query = ArmLicense::with('arm');

        if ($request->filled('arm_id')) {
            $query->where('arm_id', $request->arm_id);
        }

        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        if ($request->filled('license_type')) {
            $query->where(
                'license_type',
                $request->license_type
            );
        }

        return DataTables::eloquent($query)

            ->addColumn('firearm', function ($row) {
                return optional($row->arm)->full_name;
            })

            ->addColumn('days_remaining', function ($row) {
                return $row->days_remaining;
            })

            ->addColumn('actions', function ($row) {
                return view(
                    'arms.licenses.datatables.actions',
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

            return ArmLicense::create($data);

        });
    }

    /**
     * Update
     */
    public function update(
        ArmLicense $license,
        Request $request
    ) {

        $data = $this->validate($request);

        return DB::transaction(function () use (
            $license,
            $data
        ) {

            $license->update($data);

            return $license->fresh();

        });

    }

    /**
     * Renew
     */
    public function renew(
        ArmLicense $license,
        Request $request
    ) {

        $validated = Validator::make(
            $request->all(),
            [

                'license_number' => 'required',

                'registration_number' => 'required',

                'permit_number' => 'required',

                'issue_date' => 'required|date',

                'expiry_date' => 'required|date|after:issue_date',

                'issued_by' => 'required',

                'document' => 'nullable',

                'remarks' => 'nullable'

            ]
        )->validate();

        return DB::transaction(function () use (
            $license,
            $validated
        ) {

            return $license->renew(
                $validated
            );

        });

    }

    /**
     * Delete
     */
    public function destroy(
        ArmLicense $license
    ) {

        if (
            $license->arm
            &&
            $license->arm->activeLicense?->id == $license->id
        ) {

            abort(
                422,
                'Cannot delete the active license.'
            );

        }

        $license->delete();

    }

    /**
     * Dashboard
     */
    public function dashboard()
    {
        return [

            'total' => ArmLicense::count(),

            'active' => ArmLicense::activeCount(),

            'expired' => ArmLicense::expiredCount(),

            'expiring_30' => ArmLicense::expiring30Days(),

            'expiring_60' => ArmLicense::expiring60Days(),

            'expiring_90' => ArmLicense::expiring90Days(),

        ];
    }

    /**
     * Firearm License History
     */
    public function firearmHistory(
        Arm $arm
    ) {

        return $arm->licenses()

            ->latest('issue_date')

            ->get();

    }

    /**
     * Expired Licenses
     */
    public function expiredLicenses()
    {
        return ArmLicense::expired()

            ->with('arm')

            ->orderBy('expiry_date')

            ->get();

    }

    /**
     * Expiring Licenses
     */
    public function expiringLicenses(
        $days = 30
    ) {

        return ArmLicense::expiring($days)

            ->with('arm')

            ->orderBy('expiry_date')

            ->get();

    }

    /**
     * Annual Renewal Report
     */
    public function annualRenewalReport(
        $year
    ) {

        return ArmLicense::selectRaw(
            '
                MONTH(issue_date) as month,
                COUNT(*) as renewals
                '
        )

            ->whereYear(
                'issue_date',
                $year
            )

            ->groupByRaw(
                'MONTH(issue_date)'
            )

            ->orderByRaw(
                'MONTH(issue_date)'
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

                'license_number' => [
                    'required',
                    'max:255'
                ],

                'registration_number' => [
                    'required',
                    'max:255'
                ],

                'permit_number' => [
                    'required',
                    'max:255'
                ],

                'license_type' => [
                    'required',
                    'max:100'
                ],

                'issue_date' => [
                    'required',
                    'date'
                ],

                'expiry_date' => [
                    'required',
                    'date',
                    'after:issue_date'
                ],

                'issued_by' => [
                    'required',
                    'max:255'
                ],

                'document' => [
                    'nullable'
                ],

                'remarks' => [
                    'nullable'
                ]

            ]
        )->validate();

    }
}
