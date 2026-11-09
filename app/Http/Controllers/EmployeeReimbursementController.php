<?php

namespace App\Http\Controllers;

use App\Models\EmployeeReimbursement;
use App\Models\User;
use App\Services\PayrollItemService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class EmployeeReimbursementController extends Controller
{
    public function index()
    {
        $reimbursements = EmployeeReimbursement::with(['user', 'payrollPeriod', 'payroll'])
            ->orderByDesc('filed_date')->orderByDesc('id')->paginate(25);
        $users = User::where('active', 1)->orderBy('lastname')->orderBy('firstname')->get();
        return view('pages.reimbursements.index', compact('reimbursements', 'users'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'user_id' => 'required|exists:users,id',
            'filed_date' => 'required|date',
            'category' => 'required|string|max:120',
            'description' => 'required|string|max:255',
            'amount' => 'required|numeric|min:0.01',
            'receipt' => 'nullable|file|mimes:jpg,jpeg,png,pdf|max:5120',
        ]);

        $filedDate = date('Y-m-d', strtotime($data['filed_date']));

        $receiptPath = $request->hasFile('receipt') ? $request->file('receipt')->store('reimbursements', 'public') : null;

        EmployeeReimbursement::create([
            'user_id' => $data['user_id'],
            'payroll_period_id' => null,
            'filed_date' => $filedDate,
            'category' => $data['category'],
            'description' => $data['description'],
            'amount' => $data['amount'],
            'receipt_path' => $receiptPath,
            'status' => 'Filed',
        ]);

        return redirect()->route('reimbursements.index')->with('success', 'Reimbursement filed. It will be matched to the payroll period containing its filing date when payroll is processed.');
    }

    public function destroy(EmployeeReimbursement $reimbursement)
    {
        if ($reimbursement->payroll_id) {
            return back()->withErrors(['reimbursement' => 'This reimbursement is already included in payroll and cannot be deleted.']);
        }
        if ($reimbursement->status !== 'Filed') {
            return back()->withErrors(['reimbursement' => 'Only reimbursements not yet included in payroll can be deleted.']);
        }
        $reimbursement->delete();
        return back()->with('success', 'Reimbursement deleted.');
    }

    public static function addToPayroll(EmployeeReimbursement $reimbursement, Payroll $payroll)
    {
        if ($reimbursement->payroll_id) return;
        PayrollItemService::earning(
            $payroll,
            'REIMBURSEMENT',
            'Reimbursement - ' . $reimbursement->category . ': ' . $reimbursement->description,
            (float) $reimbursement->amount
        );
        PayrollItemService::recalculate($payroll);
        $reimbursement->update([
            'payroll_period_id' => $payroll->payroll_period_id,
            'payroll_id' => $payroll->id,
            'status' => 'Included',
        ]);
    }
}
