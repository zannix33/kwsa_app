<?php

namespace App\Http\Controllers;

use App\Models\SssContri;
use Illuminate\Http\Request;

class SssContriController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $sssContributions = SssContri::orderBy('from_salary')
            ->paginate(20);

        return view('pages.sss_contributions.index', compact('sssContributions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.sss_contributions.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $request->validate([
            'from_salary' => 'required|numeric|min:0',
            'to_salary'   => 'required|numeric|min:0|gte:from_salary',
            'premium'     => 'required|numeric|min:0',
            'active'      => 'required|boolean',
        ]);

        SssContri::create([
            'from_salary' => $request->from_salary,
            'to_salary'   => $request->to_salary,
            'premium'     => $request->premium,
            'active'      => $request->active,
        ]);

        return redirect()
            ->route('sss-contributions.index')
            ->with('success', 'SSS contribution created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\SssContri  $sssContri
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        //
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\SssContri  $sssContri
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $sssContribution = SssContri::findOrFail($id);

        return view(
            'pages.sss_contributions.edit',
            compact('sssContribution')
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\SssContri  $sssContri
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $request->validate([
            'from_salary' => 'required|numeric|min:0',
            'to_salary'   => 'required|numeric|min:0',
            'premium'     => 'required|numeric|min:0',
            'active'      => 'required|boolean',
        ]);

        $sssContribution = SssContri::findOrFail($id);

        $sssContribution->update([
            'from_salary' => $request->from_salary,
            'to_salary'   => $request->to_salary,
            'premium'     => $request->premium,
            'active'      => $request->active,
        ]);

        return redirect()
            ->route('sss-contributions.index')
            ->with('success', 'SSS contribution updated successfully.');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\SssContri  $sssContri
     * @return \Illuminate\Http\Response
     */
    public function destroy(SssContri $sssContri)
    {
        //
    }
}
