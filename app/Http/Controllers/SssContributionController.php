<?php

namespace App\Http\Controllers;

use App\Models\SssContribution;
use Illuminate\Http\Request;

class SssContributionController extends Controller
{
    public function index()
    {
        $records = SssContribution::orderBy(
            'from_salary'
        )->get();

        return view(
            'pages.sss.index',
            compact('records')
        );
    }

    public function create()
    {
        return view('pages.sss.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'from_salary' => 'required|numeric',
            'to_salary' => 'required|numeric',
            'employee_share' => 'required|numeric',
            'employer_share' => 'required|numeric',
            'rate' => 'required|string',
        ]);

        SssContribution::create(
            $request->all()
        );

        return redirect()
            ->route('sss.index')
            ->with(
                'success',
                'SSS contribution created.'
            );
    }

    public function edit(
        SssContribution $sss
    )
    {
        return view(
            'pages.sss.edit',
            compact('sss')
        );
    }

    public function update(
        Request $request,
        SssContribution $sss
    )
    {
        $sss->update(
            $request->all()
        );

        return redirect()
            ->route('sss.index')
            ->with(
                'success',
                'SSS contribution updated.'
            );
    }

    public function destroy(
        SssContribution $ss
    )
    {
        $ss->delete();

        return back()->with(
            'success',
            'Deleted.'
        );
    }
}
