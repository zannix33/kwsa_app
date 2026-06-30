<?php

namespace App\Services\Arms;

use App\Models\Ammunition;
use App\Models\AmmunitionRelease;
use App\Models\AmmunitionTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class AmmunitionReleaseService
{
    /**
     * DataTable
     */
    public function datatable(Request $request)
    {
        $query = AmmunitionRelease::with([
            'ammunition',
            'user'
        ]);

        if ($request->filled('user_id')) {
            $query->where('user_id', $request->user_id);
        }

        if ($request->filled('ammunition_id')) {
            $query->where('ammunition_id', $request->ammunition_id);
        }

        if ($request->filled('status')) {

            if ($request->status == 'Outstanding') {
                $query->whereNull('returned_at');
            }

            if ($request->status == 'Completed') {
                $query->whereNotNull('returned_at');
            }
        }

        return DataTables::eloquent($query)

            ->addColumn('employee', fn($row) => optional($row->user)->name)

            ->addColumn('ammunition', fn($row) => optional($row->ammunition)->caliber)

            ->addColumn('status', function ($row) {

                return $row->returned_at
                    ? 'Completed'
                    : 'Outstanding';

            })

            ->addColumn('actions', function ($row) {

                return view(
                    'arms.ammunition_releases.datatables.actions',
                    compact('row')
                );

            })

            ->rawColumns(['actions'])

            ->make(true);
    }

    /**
     * Release Ammunition
     */
    public function store(Request $request)
    {
        $data = $this->validate($request);

        return DB::transaction(function () use ($data) {

            $ammo = Ammunition::lockForUpdate()
                ->findOrFail($data['ammunition_id']);

            if ($ammo->quantity_on_hand < $data['quantity']) {

                abort(422, 'Insufficient ammunition stock.');

            }

            $ammo->decrement(
                'quantity_on_hand',
                $data['quantity']
            );

            $release = AmmunitionRelease::create([

                'ammunition_id' => $ammo->id,

                'user_id' => $data['user_id'],

                'quantity' => $data['quantity'],

                'released_at' => now(),

                'purpose' => $data['purpose'],

                'reference_no' => $data['reference_no'] ?? null,

                'remarks' => $data['remarks'] ?? null,

                'released_by' => auth()->id()

            ]);

            AmmunitionTransaction::create([

                'ammunition_id' => $ammo->id,

                'transaction_type' => 'Release',

                'reference_type' => AmmunitionRelease::class,

                'reference_id' => $release->id,

                'reference_no' => $release->reference_no,

                'quantity' => -$release->quantity,

                'balance' => $ammo->fresh()->quantity_on_hand,

                'remarks' => $release->purpose,

                'created_by' => auth()->id()

            ]);

            return $release;

        });
    }

    /**
     * Return Unused Ammunition
     */
    public function return(
        AmmunitionRelease $release,
        Request $request
    ) {

        $validated = Validator::make(
            $request->all(),
            [

                'returned_quantity' => 'required|integer|min:0',

                'remarks' => 'nullable'

            ]
        )->validate();

        DB::transaction(function () use (
            $release,
            $validated
        ) {

            if ($release->returned_at) {

                abort(
                    422,
                    'This release has already been returned.'
                );

            }

            if (
                $validated['returned_quantity']
                > $release->quantity
            ) {

                abort(
                    422,
                    'Returned quantity exceeds released quantity.'
                );

            }

            $ammo = Ammunition::lockForUpdate()
                ->findOrFail($release->ammunition_id);

            $ammo->increment(
                'quantity_on_hand',
                $validated['returned_quantity']
            );

            $release->update([

                'returned_quantity' => $validated['returned_quantity'],

                'consumed_quantity' =>
                    $release->quantity - $validated['returned_quantity'],

                'returned_at' => now(),

                'returned_by' => auth()->id(),

                'remarks' => $validated['remarks']

            ]);

            AmmunitionTransaction::create([

                'ammunition_id' => $ammo->id,

                'transaction_type' => 'Return',

                'reference_type' => AmmunitionRelease::class,

                'reference_id' => $release->id,

                'reference_no' => $release->reference_no,

                'quantity' => $validated['returned_quantity'],

                'balance' => $ammo->fresh()->quantity_on_hand,

                'remarks' => 'Unused ammunition returned',

                'created_by' => auth()->id()

            ]);

        });

        return $release->fresh();
    }

    /**
     * Delete
     */
    public function destroy(AmmunitionRelease $release)
    {
        if (!$release->returned_at) {

            abort(
                422,
                'Outstanding releases cannot be deleted.'
            );

        }

        $release->delete();
    }

    /**
     * Dashboard
     */
    public function dashboard()
    {
        return [

            'total_releases' =>
                AmmunitionRelease::count(),

            'outstanding' =>
                AmmunitionRelease::whereNull('returned_at')->count(),

            'completed' =>
                AmmunitionRelease::whereNotNull('returned_at')->count(),

            'released_this_month' =>
                AmmunitionRelease::whereMonth(
                    'released_at',
                    now()->month
                )->sum('quantity'),

            'consumed_this_month' =>
                AmmunitionRelease::whereMonth(
                    'released_at',
                    now()->month
                )->sum('consumed_quantity')

        ];
    }

    /**
     * Validation
     */
    protected function validate(Request $request)
    {
        return Validator::make(
            $request->all(),
            [

                'ammunition_id' => 'required|exists:ammunitions,id',

                'user_id' => 'required|exists:users,id',

                'quantity' => 'required|integer|min:1',

                'purpose' => 'required|max:255',

                'reference_no' => 'nullable|max:100',

                'remarks' => 'nullable'

            ]
        )->validate();
    }
}
