<?php

namespace App\Http\Controllers;

use App\Models\PayRate;
use App\Models\Region;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\ValidationException;
use Carbon\Carbon;

class PayRateController extends Controller
{
    /**
     * Display regions and their rate histories.
     */
    public function index()
    {
        $regions = Region::with([
            'payRates' => function ($query) {
                $query->orderBy('effective_from', 'desc');
            }
        ])
            ->orderBy('name')
            ->get();

        return view('pages.pay_rates.index', compact('regions'));
    }

    /**
     * Store a new effective-dated regional pay rate.
     */

    public function store(Request $request)
    {
        $validated = $request->validate([
            'region_id' => 'required|integer|exists:regions,id',
            'daily_rate' => 'required|numeric|min:0.01|max:9999999999.99',
            'effective_from' => 'required|date',
            'wage_order_reference' => 'nullable|string|max:255',
            'remarks' => 'nullable|string',
        ]);

        $effectiveFrom = \Carbon\Carbon::parse(
            $validated['effective_from']
        )->startOfDay();

        DB::transaction(function () use ($validated, $effectiveFrom) {
            $region = Region::whereKey($validated['region_id'])
                ->lockForUpdate()
                ->firstOrFail();

            $rates = PayRate::where('region_id', $region->id)
                ->orderBy('effective_from')
                ->lockForUpdate()
                ->get();

            // Do not allow duplicate effective dates.
            if ($rates->contains(function ($rate) use ($effectiveFrom) {
                return $rate->effective_from->toDateString()
                    === $effectiveFrom->toDateString();
            })) {
                throw ValidationException::withMessages([
                    'effective_from' =>
                        'A rate already exists for this region '
                        . 'with that effective date.',
                ]);
            }

            // Find the closest rate before the new effective date.
            $previousRate = $rates->filter(function ($rate) use ($effectiveFrom) {
                return $rate->effective_from->lt($effectiveFrom);
            })->last();

            // Find the closest rate after the new effective date.
            $nextRate = $rates->first(function ($rate) use ($effectiveFrom) {
                return $rate->effective_from->gt($effectiveFrom);
            });

            // The new rate must end before the next rate starts.
            $effectiveTo = $nextRate
                ? $nextRate->effective_from->copy()->subDay()
                : null;

            // Close the previous rate only if it overlaps the new period.
            if (
                $previousRate &&
                (
                    is_null($previousRate->effective_to) ||
                    $previousRate->effective_to->gte($effectiveFrom)
                )
            ) {
                $previousRate->effective_to = $effectiveFrom
                    ->copy()
                    ->subDay()
                    ->toDateString();

                $previousRate->save();
            }

            PayRate::create([
                'region_id' => $region->id,
                'daily_rate' => $validated['daily_rate'],
                'effective_from' => $effectiveFrom->toDateString(),
                'effective_to' => $effectiveTo
                    ? $effectiveTo->toDateString()
                    : null,
                'wage_order_reference' =>
                    $validated['wage_order_reference'] ?? null,
                'remarks' => $validated['remarks'] ?? null,
            ]);
        });

        return redirect()
            ->route('pay-rates.index')
            ->with('success', 'Regional pay rate saved successfully.');
    }

}
