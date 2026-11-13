<?php

namespace App\Http\Controllers;

use App\Models\Area;
use App\Models\Client;
//use App\Models\Company;
use Illuminate\Http\Request;

class AreaController extends Controller
{
    /**
     * Store a newly created Area.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'client_id' => 'required|exists:clients,id',
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'region_id' => 'nullable|exists:regions,id',

            // Temporary compatibility with the existing column.
            'rate' => 'nullable|string|max:255',
        ]);

        $area = Area::create($validated);

        return response()->json([
            'success' => true,
            'id' => $area->id,
            'message' => 'Area created successfully.',
        ]);
    }

    /**
     * Update Area.
     */
    public function update(Request $request, Area $area)
    {
        $validated = $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
            'region_id' => 'nullable|exists:regions,id',

            // Temporary compatibility with the existing column.
            'rate' => 'nullable|string|max:255',
        ]);

        $area->update($validated);

        return response()->json([
            'success' => true,
            'message' => 'Area updated successfully.',
        ]);
    }

    /**
     * Delete Area.
     */
    public function destroy(Area $area)
    {
        if ($area->branches()->count()) {

            return response()->json([

                'success' => false,

                'message' => 'Cannot delete Area with existing Branches.'

            ], 422);

        }

        $area->delete();

        return response()->json([

            'success' => true,

            'message' => 'Area deleted successfully.'

        ]);
    }

    /**
     * Load Branches for Area.
     */
    public function branches(Area $area)
    {
        return response()->json(

            $area->branches()
                ->orderBy('name')
                ->with('company:id,name')
                ->get([
                    'id',
                    'name',
                    'address',
                    'province',
                    'baranggay',
                    'operation_start',
                    'operation_end',
                    'operation_saturday_start',
                    'operation_saturday_end',
                    'operation_sunday_start',
                    'operation_sunday_end',
                    'is_closed_saturday',
                    'is_closed_sunday',
                    'company_id',
                    'assigned_guard_count'
                ])->map(function ($branch) {
                    $branch->company_name = optional($branch->company)->name;
                    return $branch;
                })

        );
    }

    /**
     * Load Guards assigned to Area.
     */
    public function guards(Area $area)
    {
        $guards = $area->users()->with('position')->orderBy('name')->get();
        $companyAssignments = \Illuminate\Support\Facades\DB::table('area_user_company')
            ->join('companies', 'companies.id', '=', 'area_user_company.company_id')
            ->where('area_user_company.area_id', $area->id)
            ->select('area_user_company.user_id', 'companies.name')
            ->orderBy('companies.name')->get()->groupBy('user_id');

        return response()->json($guards->map(function ($guard) use ($companyAssignments) {
            $guard->assigned_companies = ($companyAssignments->get($guard->id) ?? collect())->pluck('name')->values();
            return $guard;
        }));
    }

    /**
     * Assign Guard to Area.
     */
    public function assignGuard(Request $request)
    {
        $validated = $request->validate([
            'area_id' => 'required|exists:areas,id',
            'user_id' => 'required|exists:users,id',
            'company_ids' => 'required|array|min:1',
            'company_ids.*' => 'required|integer|distinct|exists:companies,id',
        ]);

        $guard = \App\Models\User::with('position')->findOrFail($validated['user_id']);
        if (!$this->isEligibleAreaEmployee($guard)) {
            return response()->json([
                'message' => 'Only Area Supervisors, Security Officers, Roving Guards, and Security Guards with LESP category SO or SM can be assigned to an area.',
                'errors' => ['user_id' => ['Select an employee with an eligible area-based position.']],
            ], 422);
        }

        $area = Area::findOrFail($validated['area_id']);
        $companyIds = \App\Models\Company::where('client_id', $area->client_id)
            ->where('active', 1)->whereIn('id', $validated['company_ids'])->pluck('id')->all();
        if (count($companyIds) !== count($validated['company_ids'])) {
            return response()->json(['message' => 'One or more selected companies do not belong to this client.', 'errors' => ['company_ids' => ['Select companies belonging to this client.']]], 422);
        }

        $existingAreaIds = $guard->areas()->pluck('areas.id');
        if ($existingAreaIds->isNotEmpty() && !$existingAreaIds->contains((int) $area->id)) {
            return response()->json(['message' => 'This employee is already assigned to another area. Remove that area assignment first.', 'errors' => ['user_id' => ['An employee can only be assigned to one area.']]], 422);
        }

        $area->users()->syncWithoutDetaching([$guard->id]);
        $area->users()->updateExistingPivot($guard->id, []);
        foreach ($companyIds as $companyId) {
            \Illuminate\Support\Facades\DB::table('area_user_company')->updateOrInsert(
                ['area_id' => $area->id, 'user_id' => $guard->id, 'company_id' => $companyId],
                ['updated_at' => now(), 'created_at' => now()]
            );
        }

        return response()->json([

            'success' => true,

            'message' => 'Guard assigned successfully.'

        ]);
    }

    /**
     * Remove Guard from Area.
     */
    public function removeGuard(Request $request)
    {
        $validated = $request->validate([
            'area_id' => 'required|exists:areas,id',
            'user_id' => 'required|exists:users,id',
        ]);

        $guard = \App\Models\User::with('position')->findOrFail($validated['user_id']);
        if (!$this->isEligibleAreaEmployee($guard)) {
            return response()->json([
                'message' => 'Only Security Guard, Security Officer, and Roving Guard employees can be assigned.',
                'errors' => ['user_id' => ['Select an employee with an eligible security position.']],
            ], 422);
        }

        $area = Area::findOrFail($validated['area_id']);

        $area->users()->detach($validated['user_id']);

        return response()->json([

            'success' => true,

            'message' => 'Guard removed successfully.'

        ]);
    }

    /**
     * Return guards not yet assigned to this Area.
     */
    public function availableGuards(Area $area)
    {
        $assigned = $area->users()->pluck('users.id');

        return response()->json(

            \App\Models\User::with('position')
                ->whereNotIn('id', $assigned)
                ->where(function ($query) {
                    $query->whereHas('position', function ($position) {
                        $position->whereIn('name', ['Area Supervisor', 'Security Officer', 'Roving Guard']);
                    })->orWhere(function ($securityGuard) {
                        $securityGuard->whereHas('position', function ($position) {
                            $position->where('name', 'Security Guard');
                        })->whereIn('lesp_category', ['SO', 'SM']);
                    });
                })
                ->orderBy('name')
                ->get([
                    'id',
                    'employee_no',
                    'name',
                    'position_id'
                ])

        );
    }

    private function isEligibleAreaEmployee($user): bool
    {
        $position = optional($user->position)->name;
        return in_array($position, ['Area Supervisor', 'Security Officer', 'Roving Guard'], true)
            || ($position === 'Security Guard' && in_array($user->lesp_category, ['SO', 'SM'], true));
    }

    public function indexByCompany(Client $company)
    {
        return response()->json(
            $company->areas()
                ->with('region:id,code,name')
                ->orderBy('name')
                ->get()
                ->map(function ($area) {
                    return [
                        'id' => $area->id,
                        'name' => $area->name,
                        'description' => $area->description,
                        'region_id' => $area->region_id,
                        'region_code' => optional($area->region)->code,
                        'region_name' => optional($area->region)->name,
                    ];
                })
        );
    }

    public function regions()
    {
        return response()->json(
            \App\Models\Region::where('is_active', true)
                ->orderBy('name')
                ->get(['id', 'code', 'name'])
        );
    }
}
