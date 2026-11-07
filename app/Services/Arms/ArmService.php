<?php

namespace App\Services\Arms;

use App\Models\Arm;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Validator;
use Yajra\DataTables\Facades\DataTables;

class ArmService
{
    public function datatable(Request $request)
    {
        $query = Arm::with(['branch', 'currentAssignment.user']);

        if ($request->filled('status')) {
            $query->where('status', $request->input('status'));
        }
        $branch = $request->input('branch_id', $request->input('branch'));
        if ($branch) {
            $query->where('branch_id', $branch);
        }
        if ($request->filled('caliber')) {
            $query->where('caliber', 'like', '%' . $request->input('caliber') . '%');
        }
        $manufacturer = $request->input('manufacturer', $request->input('make'));
        if ($manufacturer) {
            $query->where('manufacturer', 'like', '%' . $manufacturer . '%');
        }
        if ($request->filled('type')) {
            $query->where('type', $request->input('type'));
        }

        return DataTables::eloquent($query)
            ->addColumn('photo', function (Arm $arm) {
                if ($arm->photo && Storage::disk('public')->exists($arm->photo)) {
                    return '<img src="' . e(Storage::disk('public')->url($arm->photo)) . '" width="58" height="58" style="object-fit:cover" class="img-thumbnail" alt="Firearm photo">';
                }
                return '<span class="text-muted">No Photo</span>';
            })
            ->addColumn('branch', function (Arm $arm) {
                return optional($arm->branch)->name ?: '-';
            })
            ->addColumn('status_badge', function (Arm $arm) {
                return $arm->status_badge;
            })
            ->addColumn('assigned_to', function (Arm $arm) {
                return optional(optional($arm->currentAssignment)->user)->name ?: '—';
            })
            ->addColumn('actions', function (Arm $arm) {
                $view = route('arms.show', $arm);
                $edit = route('arms.edit', $arm);
                return '<a class="btn btn-sm btn-info" href="' . e($view) . '">View</a> '
                    . '<a class="btn btn-sm btn-primary" href="' . e($edit) . '">Edit</a>';
            })
            ->rawColumns(['photo', 'status_badge', 'actions'])
            ->make(true);
    }

    public function store(Request $request)
    {
        $data = $this->validate($request);
        if ($request->hasFile('photo')) {
            $data['photo'] = $request->file('photo')->store('arms', 'public');
        }
        return Arm::create($data);
    }

    public function update(Arm $arm, Request $request)
    {
        $data = $this->validate($request, $arm->id);
        if ($request->hasFile('photo')) {
            if ($arm->photo) {
                Storage::disk('public')->delete($arm->photo);
            }
            $data['photo'] = $request->file('photo')->store('arms', 'public');
        }
        $arm->update($data);
        return $arm->fresh();
    }

    public function destroy(Arm $arm)
    {
        if ($arm->currentAssignment()->exists()) {
            abort(422, 'Firearm is currently issued. Return it before deleting the record.');
        }
        if ($arm->photo) {
            Storage::disk('public')->delete($arm->photo);
        }
        $arm->delete();
    }

    public function bulkRetire(array $ids)
    {
        Arm::whereIn('id', $ids)->update(['status' => 'Retired']);
    }

    public function bulkAvailable(array $ids)
    {
        Arm::whereIn('id', $ids)->whereDoesntHave('currentAssignment')->update(['status' => 'Available']);
    }

    public function bulkTransfer(array $ids, $branchId)
    {
        Arm::whereIn('id', $ids)->update(['branch_id' => $branchId]);
    }

    public function dashboard()
    {
        return [
            'total' => Arm::count(),
            'available' => Arm::available()->count(),
            'issued' => Arm::issued()->count(),
            'maintenance' => Arm::maintenance()->count(),
            'lost' => Arm::lost()->count(),
            'retired' => Arm::retired()->count(),
        ];
    }

    protected function validate(Request $request, $id = null)
    {
        if (in_array($request->input('type'), ['Pistol', 'Revolver'], true)) {
            $request->merge(['type' => 'Handgun']);
        }

        return Validator::make($request->all(), [
            'property_no' => 'required|string|max:255|unique:arms,property_no,' . $id,
            'serial_no' => 'required|string|max:255|unique:arms,serial_no,' . $id,
            'model' => 'required|string|max:255',
            'caliber' => 'required|string|max:255',
            'type' => 'required|in:Handgun,Shotgun,Rifle,SMG,Others',
            'manufacturer' => 'nullable|string|max:255',
            'color' => 'nullable|string|max:255',
            'purchase_date' => 'nullable|date',
            'purchase_cost' => 'nullable|numeric|min:0',
            'supplier' => 'nullable|string|max:255',
            'branch_id' => 'nullable|exists:branches,id',
            'status' => 'required|in:Available,Issued,Maintenance,Lost,Retired',
            'remarks' => 'nullable|string',
            'photo' => 'nullable|image|mimes:jpg,jpeg,png,webp|max:5120',
        ])->validate();
    }
}
