<?php

namespace App\Http\Controllers\Arms;

use App\Http\Controllers\Controller;
use App\Models\Arm;
use App\Models\ArmLicense;
use App\Services\Arms\ArmLicenseService;
use Illuminate\Http\Request;

class ArmLicenseController extends Controller
{
    protected $service;

    public function __construct(ArmLicenseService $service) { $this->service = $service; }

    public function index()
    {
        return view('arms.licenses.index', ['summary' => $this->service->dashboard()]);
    }

    public function datatable(Request $request) { return $this->service->datatable($request); }

    public function create()
    {
        return view('arms.licenses.create', ['arms' => Arm::orderBy('property_no')->get()]);
    }

    public function store(Request $request)
    {
        $license = $this->service->store($request);
        return redirect()->route('arms.licenses.index')->with('success', 'License successfully created.');
    }

    public function show(ArmLicense $license)
    {
        $license->load('arm');
        return view('arms.licenses.show', compact('license'));
    }

    public function edit(ArmLicense $license)
    {
        return view('arms.licenses.edit', ['license' => $license, 'arms' => Arm::orderBy('property_no')->get()]);
    }

    public function update(Request $request, ArmLicense $license)
    {
        $this->service->update($license, $request);
        return redirect()->route('arms.licenses.index')->with('success', 'License updated.');
    }

    public function renew(Request $request, ArmLicense $license)
    {
        $this->service->renew($license, $request);
        return redirect()->route('arms.licenses.index')->with('success', 'License renewed successfully.');
    }

    public function destroy(ArmLicense $license)
    {
        $this->service->destroy($license);
        return back()->with('success', 'License deleted.');
    }

    public function expired() { return view('arms.licenses.expired'); }
    public function expiring() { return view('arms.licenses.expiring'); }
    public function firearm(Arm $arm) { return view('arms.licenses.firearm', compact('arm')); }
}
