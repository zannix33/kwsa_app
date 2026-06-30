<?php

namespace App\Http\Controllers\Arms;

use App\Http\Controllers\Controller;
use App\Models\Arm;
use App\Models\Branch;
use App\Services\Arms\ArmService;
use Illuminate\Http\Request;

class ArmController extends Controller
{
    protected $service;

    public function __construct(ArmService $service)
    {
        $this->service = $service;
    }

    public function index()
    {
        $branches = Branch::all();
        return view('pages.arms.arms.index', compact('branches'));
    }

    public function datatable(Request $request)
    {
        return $this->service->datatable($request);
    }

    public function create()
    {
        return view('pages.arms.arms.create',[
            'branches'=>Branch::orderBy('name')->get()
        ]);
    }

    public function store(Request $request)
    {
        $this->service->store($request);

        return redirect()
            ->route('arms.index')
            ->with('success','Firearm successfully added.');
    }

    public function show(Arm $arm)
    {
        //$arm->load([
        //    'branch',
        //    'currentAssignment.user',
        //    'latestInspection',
        //    'latestMaintenance',
        //    'activeLicense'
        //]);

        $arm->load([
            'branch',
            'activeAssignment.user',
            'assignments.user',
            'maintenances',
            'inspections',
            'licenses'
        ]);

        return view('pages.arms.arms.show',compact('arm'));
    }

    public function edit(Arm $arm)
    {
        return view('pages.arms.arms.edit',[

            'arm'=>$arm,

            'branches'=>Branch::orderBy('name')->get()

        ]);
    }

    public function update(Request $request, Arm $arm)
    {
        $this->service->update($arm,$request);

        return redirect()
            ->route('arms.index')
            ->with('success','Firearm successfully updated.');
    }

    public function destroy(Arm $arm)
    {
        $this->service->destroy($arm);

        return back()
            ->with('success','Firearm deleted.');
    }

    /**
     * Mark firearm as retired.
     */
    public function retire(Arm $arm)
    {
        $arm->retire();

        return back()->with(
            'success',
            'Firearm retired.'
        );
    }

    /**
     * Mark firearm as lost.
     */
    public function lost(Arm $arm)
    {
        $arm->markLost();

        return back()->with(
            'success',
            'Firearm marked as lost.'
        );
    }

    /**
     * Mark firearm as available.
     */
    public function available(Arm $arm)
    {
        $arm->markAvailable();

        return back()->with(
            'success',
            'Firearm is now available.'
        );
    }
}
