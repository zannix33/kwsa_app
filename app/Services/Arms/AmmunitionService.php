<?php

namespace App\Services\Arms;

use App\Models\Ammunition;
use App\Models\AmmunitionTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class AmmunitionService
{
    /**
     * Datatable
     */
    public function datatable(Request $request)
    {
        $query = Ammunition::query();

        if ($request->filled('branch_id')) {
            $query->where('branch_id', $request->branch_id);
        }

        if ($request->filled('caliber')) {
            $query->where('caliber', $request->caliber);
        }

        if ($request->filled('manufacturer')) {
            $query->where('manufacturer', $request->manufacturer);
        }

        return DataTables::eloquent($query)

            ->addColumn('available', function ($row) {
                return number_format($row->quantity_on_hand);
            })

            ->addColumn('inventory_value', function ($row) {
                return number_format(
                    $row->quantity_on_hand * $row->unit_cost,
                    2
                );
            })

            ->addColumn('actions', function ($row) {
                return view(
                    'arms.ammunition.datatables.actions',
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

            $ammo = Ammunition::create($data);

            AmmunitionTransaction::create([
                'ammunition_id' => $ammo->id,
                'transaction_type' => 'Opening Balance',
                'quantity' => $ammo->quantity_on_hand,
                'balance' => $ammo->quantity_on_hand,
                'reference_no' => $data['reference_no'] ?? null,
                'remarks' => 'Initial inventory',
                'created_by' => auth()->id()
            ]);

            return $ammo;
        });
    }

    /**
     * Update
     */
    public function update(Ammunition $ammo, Request $request)
    {
        $data = $this->validate($request);

        $ammo->update($data);

        return $ammo->fresh();
    }

    /**
     * Receive Stock
     */
    public function receive(Ammunition $ammo, Request $request)
    {
        $validated = Validator::make($request->all(), [

            'quantity' => 'required|integer|min:1',
            'unit_cost' => 'required|numeric|min:0',
            'reference_no' => 'nullable|max:100',
            'remarks' => 'nullable'

        ])->validate();

        DB::transaction(function () use ($ammo, $validated) {

            $ammo->increment(
                'quantity_on_hand',
                $validated['quantity']
            );

            $ammo->update([
                'unit_cost' => $validated['unit_cost']
            ]);

            AmmunitionTransaction::create([

                'ammunition_id' => $ammo->id,

                'transaction_type' => 'Receipt',

                'quantity' => $validated['quantity'],

                'balance' => $ammo->fresh()->quantity_on_hand,

                'reference_no' => $validated['reference_no'] ?? null,

                'remarks' => $validated['remarks'] ?? null,

                'created_by' => auth()->id()

            ]);

        });

        return $ammo->fresh();
    }

    /**
     * Adjust Stock
     */
    public function adjust(Ammunition $ammo, Request $request)
    {
        $validated = Validator::make($request->all(), [

            'quantity' => 'required|integer',
            'reason' => 'required|max:255',
            'remarks' => 'nullable'

        ])->validate();

        DB::transaction(function () use ($ammo, $validated) {

            $newBalance = $ammo->quantity_on_hand + $validated['quantity'];

            if ($newBalance < 0) {
                abort(422, 'Insufficient inventory.');
            }

            $ammo->update([
                'quantity_on_hand' => $newBalance
            ]);

            AmmunitionTransaction::create([

                'ammunition_id' => $ammo->id,

                'transaction_type' => 'Adjustment',

                'quantity' => $validated['quantity'],

                'balance' => $newBalance,

                'remarks' => $validated['reason'],

                'created_by' => auth()->id()

            ]);

        });

        return $ammo->fresh();
    }

    /**
     * Delete
     */
    public function destroy(Ammunition $ammo)
    {
        if ($ammo->quantity_on_hand > 0) {
            abort(422, 'Inventory still has stock.');
        }

        $ammo->delete();
    }

    /**
     * Dashboard
     */
    public function dashboard()
    {
        return [

            'items' => Ammunition::count(),

            'total_rounds' => Ammunition::sum('quantity_on_hand'),

            'inventory_value' => Ammunition::selectRaw(
                'SUM(quantity_on_hand * unit_cost) as total'
            )->value('total'),

            'low_stock' => Ammunition::lowStock()->count(),

            'expired' => Ammunition::expired()->count()

        ];
    }

    /**
     * Low Stock
     */
    public function lowStock()
    {
        return Ammunition::lowStock()
            ->orderBy('quantity_on_hand')
            ->get();
    }

    /**
     * Expired Stock
     */
    public function expired()
    {
        return Ammunition::expired()
            ->orderBy('expiry_date')
            ->get();
    }

    /**
     * Inventory Valuation
     */
    public function valuation()
    {
        return Ammunition::selectRaw('
            *,
            quantity_on_hand * unit_cost as inventory_value
        ')->get();
    }

    /**
     * Validation
     */
    protected function validate(Request $request)
    {
        return Validator::make($request->all(), [

            'branch_id' => 'required|exists:branches,id',

            'caliber' => 'required|max:100',

            'manufacturer' => 'required|max:255',

            'lot_number' => 'nullable|max:100',

            'quantity_on_hand' => 'required|integer|min:0',

            'minimum_stock' => 'required|integer|min:0',

            'unit_cost' => 'required|numeric|min:0',

            'expiry_date' => 'nullable|date',

            'remarks' => 'nullable'

        ])->validate();
    }
}
