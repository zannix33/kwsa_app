<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use Illuminate\Http\Request;

use App\Models\User;
use Illuminate\Support\Facades\Storage;
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

            $query = User::query();

            if ($request->filled('position')) {
                $query->where('position', $request->position);
            }

            if ($request->filled('branch_id')) {
                $query->where('branch_id', $request->branch_id);
            }

            if ($request->filled('age_from')) {
                $query->whereDate(
                    'birthdate',
                    '<=',
                    now()->subYears($request->age_from)
                );
            }

            if ($request->filled('age_to')) {
                $query->whereDate(
                    'birthdate',
                    '>=',
                    now()->subYears($request->age_to + 1)->addDay()
                );
            }

            return DataTables::eloquent($query)

                ->addIndexColumn()

                ->addColumn('photo', function ($row) {

                    if ($row->photo) {

                        return '<img src="'.asset('storage/'.$row->photo).'"
                    width="50"
                    height="50"
                    class="rounded-circle">';

                    }

                    $initials = strtoupper(
                        substr($row->firstname, 0, 1) .
                        substr($row->lastname, 0, 1)
                    );

                    return '
        <div class="rounded-circle bg-primary text-white d-flex align-items-center justify-content-center"
             style="
                width:50px;
                height:50px;
                font-weight:bold;
                font-size:18px;
                margin:auto;
             ">
            '.$initials.'
        </div>
    ';
                })

                ->addColumn('fullname', function ($row) {
                    return $row->lastname.', '.$row->firstname;
                })

                ->addColumn('age', function ($row) {
                    return $row->birthdate?->age;
                })

                ->addColumn('action', function ($row) {

                    return '
                    <a href="'.route('hr.employee.show',$row).'" class="btn btn-info btn-sm">
                        View
                    </a>

                    <a href="'.route('hr.employee.edit',$row).'" class="btn btn-primary btn-sm">
                        Edit
                    </a>
                ';
                })

                ->rawColumns(['photo','action'])

                ->make(true);
        }

        $branches = Branch::orderBy('name')->get();

        return view('pages.employee.view', compact('branches'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        //$employee = User::findOrFail($id);

        return view('pages.employee.create');
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
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
            'name' => 'required|string|max:255',
            'firstname' => 'required|string|max:255',
            'middlename' => 'nullable|string|max:255',
            'lastname' => 'required|string|max:255',

            'address' => 'nullable|string|max:255',
            'city' => 'nullable|string|max:255',
            'province' => 'nullable|string|max:255',

            'email' => 'required|email|unique:users,email',
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

        if ($request->hasFile('photo')) {

            $validated['photo'] = $request
                ->file('photo')
                ->store('employees', 'public');

        }

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
            'photo' => 'nullable|image|mimes:jpg,jpeg,png|max:2048',
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

        if ($request->hasFile('photo')) {

            if ($employee->photo) {

                Storage::disk('public')->delete($employee->photo);

            }

            $validated['photo'] = $request
                ->file('photo')
                ->store('employees', 'public');
        }



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

    public function assignBranch(Request $request, User $user)
    {
        $request->validate([
            'branch_id' => 'required|exists:branches,id',
        ]);

        $user->update([
            'branch_id' => $request->branch_id,
        ]);

        return redirect()
            ->back()
            ->with('success', 'Branch assigned successfully.');
    }

    public function assignArea($id, User $user) {

    }
}
