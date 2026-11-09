<?php

namespace App\Http\Controllers;

use App\Models\AllowanceType;
use App\Models\EmployeeAllowance;
use App\Models\PayrollItem;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class AllowanceController extends Controller
{
    public function index()
    {
        $types = AllowanceType::withCount('employeeAllowances')->orderBy('name')->get();
        $assignments = EmployeeAllowance::with(['user', 'allowanceType'])->latest()->paginate(20);
        $history = PayrollItem::where('type', 'earning')->where('code', 'like', 'ALW-%')
            ->with(['payroll.user', 'payroll.period'])->latest()->limit(30)->get();
        return view('pages.allowances.index', compact('types', 'assignments', 'history'));
    }

    public function storeType(Request $request)
    {
        $data = $request->validate([
            'name' => 'required|string|max:120|unique:allowance_types,name',
            'description' => 'nullable|string|max:255',
            'default_amount' => 'required|numeric|min:0.01',
        ]);
        AllowanceType::create($data + ['is_active' => true]);
        return back()->with('success', 'Allowance type created.');
    }

    public function toggleType(AllowanceType $allowanceType)
    {
        $allowanceType->update(['is_active' => !$allowanceType->is_active]);
        return back()->with('success', 'Allowance type status updated.');
    }

    public function create()
    {
        $users = User::where('active', 1)->orderBy('lastname')->orderBy('firstname')->get();
        $types = AllowanceType::where('is_active', true)->orderBy('name')->get();
        return view('pages.allowances.create', compact('users', 'types'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'allowance_type_id' => 'required|exists:allowance_types,id',
            'amount' => 'required|numeric|min:0.01',
            'effective_from' => 'required|date',
            'effective_to' => 'nullable|date|after_or_equal:effective_from',
        ]);
        $type = AllowanceType::findOrFail($data['allowance_type_id']);
        if (!$type->is_active) {
            return back()->withInput()->withErrors(['allowance_type_id' => 'This allowance type is inactive.']);
        }
        EmployeeAllowance::create($data + ['status' => 'Active']);
        return redirect()->route('allowances.index')->with('success', 'Employee allowance assigned. It will be included in matching future payrolls.');
    }

    public function toggle(EmployeeAllowance $employeeAllowance)
    {
        $employeeAllowance->update(['status' => $employeeAllowance->status === 'Active' ? 'Inactive' : 'Active']);
        return back()->with('success', 'Employee allowance status updated.');
    }
}
