<?php

namespace App\Http\Controllers\Arms;

use App\Http\Controllers\Controller;
use App\Models\Arm;
use App\Models\ArmAssignment;
use App\Models\Branch;
use App\Models\User;
use App\Services\Arms\ArmAssignmentService;
use Illuminate\Http\Request;

class ArmAssignmentController extends Controller
{
    protected ArmAssignmentService $service;

    public function __construct(ArmAssignmentService $service)
    {
        $this->service = $service;
    }

    /**
     * Assignment history
     */
    public function index()
    {
        return view('arms.assignments.index');
    }

    /**
     * Datatable
     */
    public function datatable(Request $request)
    {
        return $this->service->datatable($request);
    }

    /**
     * Issue firearm form
     */
    public function create()
    {
        return view('arms.assignments.create', [

            'arms' => Arm::available()
                ->orderBy('make')
                ->orderBy('model')
                ->get(),

            'users' => User::orderBy('name')->get(),

            'branches' => Branch::orderBy('name')->get()

        ]);
    }

    /**
     * Issue firearm
     */
    public function store(Request $request)
    {
        $assignment = $this->service->store($request);

        return redirect()
            ->route('arms.assignments.show', $assignment)
            ->with('success', 'Firearm successfully issued.');
    }

    /**
     * Assignment details
     */
    public function show(ArmAssignment $assignment)
    {
        $assignment->load([
            'arm',
            'user',
            'branch'
        ]);

        return view(
            'arms.assignments.show',
            compact('assignment')
        );
    }

    /**
     * Return firearm form
     */
    public function edit(ArmAssignment $assignment)
    {
        abort_if(
            $assignment->is_returned,
            404
        );

        return view(
            'arms.assignments.return',
            compact('assignment')
        );
    }

    /**
     * Return firearm
     */
    public function update(
        Request $request,
        ArmAssignment $assignment
    ) {

        $this->service->returnFirearm(
            $assignment,
            $request
        );

        return redirect()
            ->route('arms.assignments.index')
            ->with(
                'success',
                'Firearm successfully returned.'
            );
    }

    /**
     * Delete history
     */
    public function destroy(ArmAssignment $assignment)
    {
        $this->service->destroy($assignment);

        return back()->with(
            'success',
            'Assignment deleted.'
        );
    }

    /**
     * Current issued firearms
     */
    public function current()
    {
        return view(
            'arms.assignments.current'
        );
    }

    /**
     * Overdue assignments
     */
    public function overdue()
    {
        return view(
            'arms.assignments.overdue'
        );
    }

    /**
     * Employee firearm history
     */
    public function employee(User $user)
    {
        return view(
            'arms.assignments.employee',
            compact('user')
        );
    }

    /**
     * Firearm assignment history
     */
    public function firearm(Arm $arm)
    {
        return view(
            'arms.assignments.firearm',
            compact('arm')
        );
    }
}
