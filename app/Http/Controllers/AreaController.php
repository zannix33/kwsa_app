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

            'rate' => 'nullable|string',

        ]);

        $area = Area::create($validated);

        return response()->json([

            'success' => true,

            'id' => $area->id,

            'message' => 'Area created successfully.'

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

            'rate' => 'nullable|numeric|min:0',

        ]);

        $area->update($validated);

        return response()->json([

            'success' => true,

            'message' => 'Area updated successfully.'

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
                ->get([
                    'id',
                    'name',
                    'address',
                    'province',
                    'baranggay',
                    'operation_start',
                    'operation_end'
                ])

        );
    }

    /**
     * Load Guards assigned to Area.
     */
    public function guards(Area $area)
    {
        return response()->json(

            $area->users()
                ->with('position')
                ->orderBy('name')
                ->get()

        );
    }

    /**
     * Assign Guard to Area.
     */
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

    public function indexByCompany(Client $company)
    {
        return response()->json(

            $company->areas()
                ->orderBy('name')
                ->get([
                    'id',
                    'name',
                    'description',
                    'rate'
                ])

        );
    }
}
