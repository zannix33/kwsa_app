<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Branch;
use App\Models\Company;
use App\Models\EmployeeCompanyAreaAssignment;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Validation\Rule;

class EmployeeCompanyAreaAssignmentController extends Controller
{
    public function index(User $employee)
    {
        $assignments = $employee->companyAreaAssignments()->with(['company', 'area', 'branch'])->latest()->get();
        $companies = Company::where('active', 1)->orderBy('name')->get();
        $areas = Area::orderBy('name')->get();
        $branches = Branch::orderBy('name')->get();

        return view('pages.employee.company-area-assignments', compact('employee', 'assignments', 'companies', 'areas', 'branches'));
    }

    public function store(Request $request, User $employee)
    {
        $data = $this->validated($request);
        $this->validateCompanyAreaBranch($data);
        $data['user_id'] = $employee->id;
        EmployeeCompanyAreaAssignment::create($data);

        return back()->with('success', 'Company and area assignment added.');
    }

    public function update(Request $request, User $employee, EmployeeCompanyAreaAssignment $assignment)
    {
        abort_unless($assignment->user_id === $employee->id, 404);
        $data = $this->validated($request);
        $this->validateCompanyAreaBranch($data);
        $assignment->update($data);

        return back()->with('success', 'Assignment updated.');
    }

    public function destroy(User $employee, EmployeeCompanyAreaAssignment $assignment)
    {
        abort_unless($assignment->user_id === $employee->id, 404);
        $assignment->update(['status' => 'Inactive', 'effective_to' => $assignment->effective_to ?: now()->toDateString()]);

        return back()->with('success', 'Assignment deactivated. Historical assignment data was retained.');
    }

    private function validated(Request $request): array
    {
        return $request->validate([
            'company_id' => ['required', 'exists:companies,id'],
            'area_id' => ['required', 'exists:areas,id'],
            'branch_id' => ['nullable', 'exists:branches,id'],
            'position' => ['required', Rule::in(['Area Supervisor', 'Security Officer', 'Roving Guard', 'Branch Guard', 'Other'])],
            'effective_from' => ['nullable', 'date'],
            'effective_to' => ['nullable', 'date', 'after_or_equal:effective_from'],
            'status' => ['required', Rule::in(['Active', 'Inactive'])],
        ]);
    }

    private function validateCompanyAreaBranch(array $data): void
    {
        $company = Company::findOrFail($data['company_id']);
        $area = Area::findOrFail($data['area_id']);
        if ((int) $company->client_id !== (int) $area->client_id) {
            throw \Illuminate\Validation\ValidationException::withMessages(['area_id' => 'The selected area does not belong to the selected company client.']);
        }
        if (!empty($data['branch_id'])) {
            $branch = Branch::findOrFail($data['branch_id']);
            if ((int) $branch->company_id !== (int) $company->id || (int) $branch->area_id !== (int) $area->id) {
                throw \Illuminate\Validation\ValidationException::withMessages(['branch_id' => 'The selected branch must belong to the selected company and area.']);
            }
        }
    }
}
