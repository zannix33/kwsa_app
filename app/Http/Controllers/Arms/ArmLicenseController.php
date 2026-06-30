<?php

namespace App\Http\Controllers\Arms;

use App\Http\Controllers\Controller;
use App\Models\Arm;
use App\Models\ArmLicense;
use App\Services\Arms\ArmLicenseService;
use Illuminate\Http\Request;

class ArmLicenseController extends Controller
{
    protected ArmLicenseService $service;

    public function __construct(ArmLicenseService $service)
    {
        $this->service = $service;
    }

    /**
     * License History
     */
    public function index()
    {
        return view('arms.licenses.index');
    }

    /**
     * DataTable
     */
    public function datatable(Request $request)
    {
        return $this->service->datatable($request);
    }

    /**
     * Create License
     */
    public function create()
    {
        return view('arms.licenses.create', [

            'arms' => Arm::orderBy('property_no')->get()

        ]);
    }

    /**
     * Store License
     */
    public function store(Request $request)
    {
        $license = $this->service->store($request);

        return redirect()

            ->route('arms.licenses.show', $license)

            ->with(
                'success',
                'License successfully created.'
            );
    }

    /**
     * View License
     */
    public function show(
        ArmLicense $license
    ) {

        $license->load('arm');

        return view(
            'arms.licenses.show',
            compact('license')
        );

    }

    /**
     * Edit License
     */
    public function edit(
        ArmLicense $license
    ) {

        return view(
            'arms.licenses.edit',
            [

                'license' => $license,

                'arms' => Arm::orderBy('property_no')->get()

            ]
        );

    }

    /**
     * Update License
     */
    public function update(
        Request $request,
        ArmLicense $license
    ) {

        $this->service->update(
            $license,
            $request
        );

        return redirect()

            ->route(
                'arms.licenses.index'
            )

            ->with(
                'success',
                'License updated.'
            );

    }

    /**
     * Renew License
     */
    public function renew(
        Request $request,
        ArmLicense $license
    ) {

        $this->service->renew(
            $license,
            $request
        );

        return redirect()

            ->route(
                'arms.licenses.index'
            )

            ->with(
                'success',
                'License renewed successfully.'
            );

    }

    /**
     * Delete License
     */
    public function destroy(
        ArmLicense $license
    ) {

        $this->service->destroy(
            $license
        );

        return back()->with(
            'success',
            'License deleted.'
        );

    }

    /**
     * Expired Licenses
     */
    public function expired()
    {
        return view(
            'arms.licenses.expired'
        );
    }

    /**
     * Expiring Licenses
     */
    public function expiring()
    {
        return view(
            'arms.licenses.expiring'
        );
    }

    /**
     * Firearm License History
     */
    public function firearm(
        Arm $arm
    ) {

        return view(
            'arms.licenses.firearm',
            compact('arm')
        );

    }

}
