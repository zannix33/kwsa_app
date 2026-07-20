<?php

namespace App\Http\Controllers;

use App\Models\Branch;
use App\Models\User;
use Illuminate\Http\Request;

class BranchController extends Controller
{
    /**
     * Store a newly created Branch.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([

            'area_id' => 'required|exists:areas,id',

            'name' => 'required|string|max:255',

            'address' => 'nullable|string',

            'province' => 'nullable|string|max:255',

            'baranggay' => 'nullable|string|max:255',

            'operation_start' => 'nullable|date_format:H:i',

            'operation_end' => 'nullable|date_format:H:i|after:operation_start',

        ]);

        $branch = Branch::create($validated);

        return response()->json([

            'success' => true,

            'id' => $branch->id,

            'message' => 'Branch created successfully.'

        ]);
    }

    /**
     * Update Branch.
     */
    public function update(Request $request, Branch $branch)
    {
        $validated = $request->validate([

            'name' => 'required|string|max:255',

            'address' => 'nullable|string',

            'province' => 'nullable|string|max:255',

            'baranggay' => 'nullable|string|max:255',

            'operation_start' => 'nullable|date_format:H:i',

            'operation_end' => 'nullable|date_format:H:i|after:operation_start',

        ]);

        $branch->update($validated);

        return response()->json([

            'success' => true,

            'message' => 'Branch updated successfully.'

        ]);
    }

    /**
     * Delete Branch.
     */
    public function destroy(Branch $branch)
    {
        if ($branch->users()->count()) {

            return response()->json([

                'success' => false,

                'message' => 'Cannot delete a Branch with assigned guards.'

            ], 422);

        }

        $branch->delete();

        return response()->json([

            'success' => true,

            'message' => 'Branch deleted successfully.'

        ]);
    }

    /**
     * Load Guards assigned to Branch.
     */
    public function guards(Branch $branch)
    {
        return response()->json(

            $branch->users()
                ->with('position')
                ->orderBy('name')
                ->get()

        );
    }

    /**
     * Assign Guard to Branch.
     */
    public function assignGuard(Request $request)
    {
        $validated = $request->validate([

            'branch_id' => 'required|exists:branches,id',

            'user_id' => 'required|exists:users,id',

        ]);

        $branch = Branch::findOrFail($validated['branch_id']);

        $branch->users()->syncWithoutDetaching([

            $validated['user_id']

        ]);

        return response()->json([

            'success' => true,

            'message' => 'Guard assigned successfully.'

        ]);
    }

    /**
     * Remove Guard from Branch.
     */
    public function removeGuard(Request $request)
    {
        $validated = $request->validate([

            'branch_id' => 'required|exists:branches,id',

            'user_id' => 'required|exists:users,id',

        ]);

        $branch = Branch::findOrFail($validated['branch_id']);

        $branch->users()->detach($validated['user_id']);

        return response()->json([

            'success' => true,

            'message' => 'Guard removed successfully.'

        ]);
    }

    /**
     * Return guards not yet assigned to this Branch.
     */
    public function availableGuards(Branch $branch)
    {
        $assigned = $branch->users()->pluck('users.id');

        return response()->json(

            User::with('position')
                ->whereNotIn('id', $assigned)
                ->where('employee_type', 'Operations')
                ->orderBy('name')
                ->get([
                    'id',
                    'employee_no',
                    'name',
                    'position_id'
                ])

        );
    }
}
