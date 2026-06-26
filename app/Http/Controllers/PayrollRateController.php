<?php

namespace App\Http\Controllers;

use App\Models\PayrollRate;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class PayrollRateController extends Controller
{
    public function index()
    {
        $rates = PayrollRate::latest()->get();
        return view('pages.payroll_rates.index', compact('rates'));
    }

    public function create()
    {
        return view('pages.payroll_rates.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name'  => 'required',
            'rate'  => 'required|numeric',
            'active'=> 'boolean'
        ]);

        PayrollRate::create([
            'name'   => $request->name,
            'slug'   => Str::slug($request->name),
            'rate'   => $request->rate,
            'active' => $request->active ?? 1,
        ]);

        return redirect()->route('payroll-rates.index')
            ->with('success', 'Payroll rate created successfully.');
    }

    public function edit($id)
    {
        $rate = PayrollRate::findOrFail($id);
        return view('pages.payroll_rates.edit', compact('rate'));
    }

    public function update(Request $request, $id)
    {
        $rate = PayrollRate::findOrFail($id);

        $request->validate([
            'name'  => 'required',
            'rate'  => 'required|numeric',
            'active'=> 'boolean'
        ]);

        $rate->update([
            'name'   => $request->name,
            'slug'   => Str::slug($request->name),
            'rate'   => $request->rate,
            'active' => $request->active ?? 1,
        ]);

        return redirect()->route('payroll-rates.index')
            ->with('success', 'Payroll rate updated successfully.');
    }

    public function destroy($id)
    {
        PayrollRate::destroy($id);

        return redirect()->route('payroll-rates.index')
            ->with('success', 'Payroll rate deleted successfully.');
    }
}
