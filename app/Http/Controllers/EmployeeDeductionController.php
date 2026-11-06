<?php

namespace App\Http\Controllers;

use App\Models\EmployeeDeduction;
use App\Models\User;
use Illuminate\Http\Request;

class EmployeeDeductionController extends Controller
{
    public function index()
    {
        $deductions = EmployeeDeduction::with(['user', 'periods.period'])->latest()->paginate(20);
        return view('pages.employee_deductions.index', compact('deductions'));
    }

    public function create()
    {
        $users = User::orderBy('lastname')->orderBy('firstname')->get();
        return view('pages.employee_deductions.create', compact('users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'frequency' => 'required|in:one_time,every_payroll,monthly',
            'start_date' => 'required|date',
            'end_date' => 'nullable|date|after_or_equal:start_date',
            'installment_count' => 'required|integer|min:0',
            'monthly_cutoff' => 'required_if:frequency,monthly|nullable|in:first,second',
        ]);

        if ($data['frequency'] === 'one_time') {
            $data['installment_count'] = 1;
            $data['end_date'] = $data['start_date'];
            $data['monthly_cutoff'] = null;
        } elseif ($data['frequency'] === 'every_payroll') {
            $data['monthly_cutoff'] = null;
        }

        EmployeeDeduction::create([
            'user_id' => $data['user_id'],
            'description' => $data['description'],
            'amount' => $data['amount'],
            'frequency' => $data['frequency'],
            'start_date' => $data['start_date'],
            'end_date' => $data['end_date'] ?? null,
            'installment_count' => $data['installment_count'],
            'monthly_cutoff' => $data['monthly_cutoff'] ?? null,
            'status' => 'Active',
            'payment_status' => 'Pending',
        ]);

        return redirect()->route('employee-deductions.index')->with('success', 'Fixed deduction created. It will be applied automatically to matching future payrolls.');
    }

    public function show(EmployeeDeduction $employeeDeduction)
    {
        $employeeDeduction->load(['user', 'periods.period', 'periods.payroll']);
        return view('pages.employee_deductions.show', compact('employeeDeduction'));
    }

    public function toggle(EmployeeDeduction $employeeDeduction)
    {
        if ($employeeDeduction->payment_status === 'Fully Paid') {
            return back()->with('error', 'A fully paid deduction cannot be reactivated.');
        }
        $employeeDeduction->update(['status' => $employeeDeduction->status === 'Active' ? 'Inactive' : 'Active']);
        return back()->with('success', 'Deduction status updated.');
    }

    public function markPaid(EmployeeDeduction $employeeDeduction)
    {
        $employeeDeduction->update(['payment_status' => 'Fully Paid', 'status' => 'Inactive']);
        return back()->with('success', 'Deduction manually marked Fully Paid and Inactive.');
    }
}
