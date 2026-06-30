<?php

namespace App\Http\Controllers\Arms;

use App\Http\Controllers\Controller;
use App\Models\Ammunition;
use App\Models\AmmunitionRelease;
use App\Models\User;
use App\Services\Arms\AmmunitionReleaseService;
use Illuminate\Http\Request;

class AmmunitionReleaseController extends Controller
{
    protected AmmunitionReleaseService $service;

    public function __construct(AmmunitionReleaseService $service)
    {
        $this->service = $service;
    }

    /**
     * Release History
     */
    public function index()
    {
        return view('arms.ammunition_releases.index');
    }

    /**
     * DataTable
     */
    public function datatable(Request $request)
    {
        return $this->service->datatable($request);
    }

    /**
     * Release Form
     */
    public function create()
    {
        return view('arms.ammunition_releases.create', [

            'ammunitions' => Ammunition::where('quantity_on_hand', '>', 0)
                ->orderBy('caliber')
                ->orderBy('manufacturer')
                ->get(),

            'users' => User::orderBy('name')->get()

        ]);
    }

    /**
     * Store Release
     */
    public function store(Request $request)
    {
        $release = $this->service->store($request);

        return redirect()

            ->route(
                'arms.ammunition-releases.show',
                $release
            )

            ->with(
                'success',
                'Ammunition released successfully.'
            );
    }

    /**
     * View Release
     */
    public function show(
        AmmunitionRelease $ammunitionRelease
    )
    {
        $ammunitionRelease->load([
            'ammunition',
            'user'
        ]);

        return view(
            'arms.ammunition_releases.show',
            compact('ammunitionRelease')
        );
    }

    /**
     * Return Form
     */
    public function edit(
        AmmunitionRelease $ammunitionRelease
    )
    {
        return view(
            'arms.ammunition_releases.return',
            compact('ammunitionRelease')
        );
    }

    /**
     * Return Unused Ammunition
     */
    public function update(
        Request $request,
        AmmunitionRelease $ammunitionRelease
    )
    {
        $this->service->return(
            $ammunitionRelease,
            $request
        );

        return redirect()

            ->route(
                'arms.ammunition-releases.index'
            )

            ->with(
                'success',
                'Unused ammunition returned.'
            );
    }

    /**
     * Delete Release
     */
    public function destroy(
        AmmunitionRelease $ammunitionRelease
    )
    {
        $this->service->destroy(
            $ammunitionRelease
        );

        return back()->with(
            'success',
            'Release deleted.'
        );
    }

    /**
     * Outstanding Releases
     */
    public function outstanding()
    {
        return view(
            'arms.ammunition_releases.outstanding'
        );
    }

    /**
     * Employee History
     */
    public function employee(
        User $user
    )
    {
        return view(
            'arms.ammunition_releases.employee',
            compact('user')
        );
    }

    /**
     * Ammunition History
     */
    public function ammunition(
        Ammunition $ammunition
    )
    {
        return view(
            'arms.ammunition_releases.ammunition',
            compact('ammunition')
        );
    }
}
