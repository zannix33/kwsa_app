<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\User;
use Yajra\DataTables\Facades\DataTables;


class EmployeeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request)
    {
        if ($request->ajax()) {

            $employees = User::all();

            return DataTables::of($employees)
                ->addIndexColumn()
                ->addColumn('fullname', function ($row) {
                    return $row->lastname . ', ' . $row->firstname . ' ' . $row->middlename;
                })
                ->addColumn('age', function ($row) {
                    return $row->birthdate?->age;
                })
                ->addColumn('position', function ($row) {
                    return $row->position ?? '-';
                })
                ->addColumn('action', function ($row) {
                    return '
                    <a href="'.route('hr.employee.show', $row->id).'" class="btn btn-sm btn-info">
                        View
                    </a>

                    <a href="'.route('hr.employee.edit', $row->id).'" class="btn btn-sm btn-primary">
                        Edit
                    </a>
                ';
                })
                ->rawColumns(['action'])
                ->make(true);
        }

        return view('pages.employee.view');



        //return view('pages.employee.view', compact('employees'));

    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'lastname' => 'required|string|max:255',

            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',

            'email' => 'required|email|unique:employees,email',
            'phone' => 'nullable|numeric',

            'religion' => 'nullable|string|max:255',

            'spouse_name' => 'nullable|string|max:255',
            'spouse_birthdate' => 'nullable|date',

            'beneficiary_name' => 'nullable|string|max:255',
            'beneficiary_contact' => 'nullable|numeric',

            'password' => 'nullable|string|min:8',

            'civil_status' => 'nullable|string|max:255',
            'birthdate' => 'nullable|date',

            'height' => 'nullable|string|max:50',
            'weight' => 'nullable|string|max:50',

            'sss' => 'nullable|string|max:255',
            'tin' => 'nullable|string|max:255',
            'pagibig' => 'nullable|string|max:255',
            'philhealth' => 'nullable|string|max:255',

            'bloodtype' => 'nullable|string|max:10',

            'position' => 'nullable|string|max:255',

            'lesp_num' => 'nullable|string|max:255',
            'lesp_issued' => 'nullable|string|max:255',
            'lesp_expiry' => 'nullable|date',

            'date_hired' => 'nullable|date',
            'dt_date' => 'nullable|date',
        ]);

        $validated['password'] = $request->password
            ? Hash::make($request->password)
            : null;

        User::create($validated);

        return redirect()
            ->route('hr.employee.index')
            ->with('success', 'Employee created successfully.');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $employee = User::findOrFail($id); //find($id)->get();

        return view('pages.employee.show', compact('employee'));

    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $employee = User::findOrFail($id);

        return view('pages.employee.edit', compact('employee'));
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
        $employee = User::findOrFail($id);


        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'lastname' => 'required|string|max:255',

            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',

            //'email' => 'required|email|unique:users,email,' . $user->id,

            'phone' => 'nullable',

            'religion' => 'nullable|string|max:255',

            'spouse_name' => 'nullable|string|max:255',
            'spouse_birthdate' => 'nullable|date',

            'beneficiary_name' => 'nullable|string|max:255',
            'beneficiary_contact' => 'nullable',

            'civil_status' => 'nullable|string|max:255',
            'birthdate' => 'nullable|date',

            'height' => 'nullable|string|max:50',
            'weight' => 'nullable|string|max:50',

            'sss' => 'nullable|string|max:255',
            'tin' => 'nullable|string|max:255',
            'pagibig' => 'nullable|string|max:255',
            'philhealth' => 'nullable|string|max:255',

            'bloodtype' => 'nullable|string|max:10',

            'position' => 'nullable|string|max:255',

            'lesp_num' => 'nullable|string|max:255',
            'lesp_issued' => 'nullable|string|max:255',
            'lesp_expiry' => 'nullable|date',

            'date_hired' => 'nullable|date',
            'dt_date' => 'nullable|date',

            'password' => 'nullable|min:8',
        ]);



        // Update password only if entered
        if ($request->filled('password')) {
            $validated['password'] = Hash::make($request->password);
        } else {
            unset($validated['password']);
        }

        $employee->update($validated);



        return redirect()
            ->back()
            ->with('success', 'Employee updated successfully.');

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
}
