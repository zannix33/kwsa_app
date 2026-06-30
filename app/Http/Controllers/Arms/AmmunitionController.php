<?php

namespace App\Http\Controllers\Arms;

use App\Http\Controllers\Controller;
use App\Models\Ammunition;
use App\Models\Branch;
use App\Services\Arms\AmmunitionService;
use Illuminate\Http\Request;

class AmmunitionController extends Controller
{
    protected AmmunitionService $service;

    public function __construct(AmmunitionService $service)
    {
        $this->service = $service;
    }

    /**
     * Inventory
     */
    public function index()
    {
        return view('arms.ammunition.index');
    }

    /**
     * DataTable
     */
    public function datatable(Request $request)
    {
        return $this->service->datatable($request);
    }

    /**
     * Create
     */
    public function create()
    {
        return view('arms.ammunition.create', [

            'branches' => Branch::orderBy('name')->get()

        ]);
    }

    /**
     * Store
     */
    public function store(Request $request)
    {
        $ammo = $this->service->store($request);

        return redirect()

            ->route('arms.ammunition.show', $ammo)

            ->with(
                'success',
                'Ammunition successfully added.'
            );
    }

    /**
     * Details
     */
    public function show(
        Ammunition $ammunition
    ) {

        return view(
            'arms.ammunition.show',
            compact('ammunition')
        );

    }

    /**
     * Edit
     */
    public function edit(
        Ammunition $ammunition
    ) {

        return view(
            'arms.ammunition.edit',
            [

                'ammunition' => $ammunition,

                'branches' => Branch::orderBy('name')->get()

            ]
        );

    }

    /**
     * Update
     */
    public function update(
        Request $request,
        Ammunition $ammunition
    ) {

        $this->service->update(
            $ammunition,
            $request
        );

        return redirect()

            ->route(
                'arms.ammunition.index'
            )

            ->with(
                'success',
                'Ammunition updated.'
            );

    }

    /**
     * Delete
     */
    public function destroy(
        Ammunition $ammunition
    ) {

        $this->service->destroy(
            $ammunition
        );

        return back()->with(
            'success',
            'Ammunition deleted.'
        );

    }

    /**
     * Receive Stock
     */
    public function receive(
        Request $request,
        Ammunition $ammunition
    ) {

        $this->service->receive(
            $ammunition,
            $request
        );

        return back()->with(
            'success',
            'Stock received successfully.'
        );

    }

    /**
     * Adjust Stock
     */
    public function adjust(
        Request $request,
        Ammunition $ammunition
    ) {

        $this->service->adjust(
            $ammunition,
            $request
        );

        return back()->with(
            'success',
            'Stock adjusted.'
        );

    }

    /**
     * Low Stock
     */
    public function lowStock()
    {
        return view(
            'arms.ammunition.low-stock'
        );
    }

    /**
     * Expired Stock
     */
    public function expired()
    {
        return view(
            'arms.ammunition.expired'
        );
    }

    /**
     * Inventory Valuation
     */
    public function valuation()
    {
        return view(
            'arms.ammunition.valuation'
        );
    }
}
