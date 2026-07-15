<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\Client;
use App\Models\Area;

class AreaController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {

        $companies = Client::latest()->paginate(10);

        return view('pages.client.view', compact('companies'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('pages.client.create');
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
            'client_id' => 'required|exists:clients,id',
            'name' => 'required',
            'description' => 'nullable',
            'rate' => 'required|in:ncr,provincial',
        ]);

        $area = Area::create($request->all());

        return response()->json($area);
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $company = Client::findOrFail($id); //find($id)->get();

        return view('pages.client.show', compact('company'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $company = Client::findOrFail($id);

        return view('pages.client.edit', compact('company'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'category' => 'required|in:company,property,individual',
            'active' => 'required|in:0,1',
            'age_limit' => 'nullable|integer|min:0',
        ]);

        $client = Client::findOrFail($id);

        $client->update($validated);

        return redirect()
            ->route('clients.companies.index')
            ->with('success', 'Company updated successfully.');

    }

    public function branches(Area $area)
    {
        return response()->json(
            $area->branches()->get()
        );
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        //
    }

    public function assignGuard(Request $request)
    {
        $validated = $request->validate([
            'area_id' => 'required|exists:areas,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $area = Area::findOrFail($validated['area_id']);

        $area->users()->syncWithoutDetaching([
            $validated['user_id']
        ]);

        return response()->json([
            'success' => true
        ]);
    }

    public function guards(Area $area)
    {
        return response()->json(
            $area->users()
                ->select(
                    'users.id',
                    'users.name',
                    'users.employee_no',
                    'users.position_id'
                )
                ->with('position')
                ->orderBy('users.name')
                ->get()
        );
    }


}
