<?php

namespace App\Http\Controllers;

use App\Models\Loan;
use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class LoanController extends Controller
{
    /**
     * Display all loans.
     */
    public function index(Request $request)
    {
        // Summary cards use all loan records.
        $totalLoanAmount = Loan::sum('total_loan_amount');

        $totalOutstanding = Loan::where('status', 'Active')
            ->sum('outstanding_balance');

        $activeLoans = Loan::where('status', 'Active')->count();

        $fullyPaidLoans = Loan::where('status', 'Fully Paid')->count();

        // Main loan query.
        $query = Loan::with('user');

        // Search employee number, employee name, or loan reference.
        if ($request->filled('search')) {
            $search = trim($request->search);

            $query->where(function ($q) use ($search) {
                $q->where('loan_reference', 'like', '%' . $search . '%')
                    ->orWhereHas('user', function ($userQuery) use ($search) {
                        $userQuery->where('employee_no', 'like', '%' . $search . '%')
                            ->orWhere('firstname', 'like', '%' . $search . '%')
                            ->orWhere('lastname', 'like', '%' . $search . '%');
                    });
            });
        }

        // Filter by loan provider.
        if ($request->filled('loan_provider')) {
            $query->where(
                'loan_provider',
                $request->loan_provider
            );
        }

        // Filter by status.
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        $loans = $query
            ->orderBy('created_at', 'desc')
            ->paginate(15)
            ->appends($request->query());

        return view('pages.loans.index', compact(
            'loans',
            'totalLoanAmount',
            'totalOutstanding',
            'activeLoans',
            'fullyPaidLoans'
        ));
    }

    /**
     * Show the create loan form.
     */
    public function create()
    {
        $users = User::orderBy('firstname')
            ->orderBy('lastname')
            ->get();

        return view('pages.loans.create', compact('users'));
    }

    /**
     * Store a new loan.
     */
    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
            ],

            'loan_reference' => [
                'required',
                'string',
                'max:100',
                'unique:loans,loan_reference',
            ],

            'loan_provider' => [
                'required',
                Rule::in(['SSS', 'Pag-IBIG', 'Company']),
            ],

            'loan_type' => [
                'required',
                'string',
                'max:100',
            ],

            'loan_date' => [
                'required',
                'date',
            ],

            'principal_amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'interest_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'total_loan_amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'monthly_amortization' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'deduction_start_date' => [
                'required',
                'date',
            ],

            'deduction_end_date' => [
                'nullable',
                'date',
                'after_or_equal:deduction_start_date',
            ],

            'remarks' => [
                'nullable',
                'string',
            ],
        ]);

        $principal = (float) $validated['principal_amount'];
        $interest = (float) ($validated['interest_amount'] ?? 0);

        // Calculate the total on the server, not from the browser.
        $total = round($principal + $interest, 2);

        if (round((float) $validated['total_loan_amount'], 2) !== $total) {
            return back()
                ->withInput()
                ->withErrors([
                    'total_loan_amount' =>
                        'The total loan amount must equal the principal plus interest.',
                ]);
        }

        if ((float) $validated['monthly_amortization'] > $total) {
            return back()
                ->withInput()
                ->withErrors([
                    'monthly_amortization' =>
                        'Monthly amortization cannot exceed the total loan amount.',
                ]);
        }

        $loan = Loan::create([
            'user_id' => $validated['user_id'],
            'loan_reference' => $validated['loan_reference'],
            'loan_provider' => $validated['loan_provider'],
            'loan_type' => $validated['loan_type'],
            'loan_date' => $validated['loan_date'],
            'principal_amount' => $principal,
            'interest_amount' => $interest,
            'total_loan_amount' => $total,
            'monthly_amortization' => $validated['monthly_amortization'],
            'total_paid' => 0,
            'outstanding_balance' => $total,
            'deduction_start_date' => $validated['deduction_start_date'],
            'deduction_end_date' => $validated['deduction_end_date'] ?? null,
            'status' => 'Active',
            'remarks' => $validated['remarks'] ?? null,
        ]);

        return redirect()
            ->route('loans.show', $loan->id)
            ->with('success', 'Loan created successfully.');
    }

    /**
     * Display a specific loan.
     */
    public function show(Loan $loan)
    {
        $loan->load([
            'user',
            'payments',
        ]);

        return view('pages.loans.show', compact('loan'));
    }

    /**
     * Show the edit loan form.
     */
    public function edit(Loan $loan)
    {
        $users = User::orderBy('firstname')
            ->orderBy('lastname')
            ->get();

        $loan->load('payments');

        return view('pages.loans.edit', compact('loan', 'users'));
    }

    /**
     * Update an existing loan.
     */
    public function update(Request $request, Loan $loan)
    {
        $validated = $request->validate([
            'user_id' => [
                'required',
                'exists:users,id',
            ],

            'loan_reference' => [
                'required',
                'string',
                'max:100',
                Rule::unique('loans', 'loan_reference')->ignore($loan->id),
            ],

            'loan_provider' => [
                'required',
                Rule::in(['SSS', 'Pag-IBIG', 'Company']),
            ],

            'loan_type' => [
                'required',
                'string',
                'max:100',
            ],

            'loan_date' => [
                'required',
                'date',
            ],

            'principal_amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'interest_amount' => [
                'nullable',
                'numeric',
                'min:0',
            ],

            'total_loan_amount' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'monthly_amortization' => [
                'required',
                'numeric',
                'min:0.01',
            ],

            'deduction_start_date' => [
                'required',
                'date',
            ],

            'deduction_end_date' => [
                'nullable',
                'date',
                'after_or_equal:deduction_start_date',
            ],

            'status' => [
                'required',
                Rule::in(['Active', 'Fully Paid', 'Cancelled']),
            ],

            'remarks' => [
                'nullable',
                'string',
            ],
        ]);

        $principal = (float) $validated['principal_amount'];
        $interest = (float) ($validated['interest_amount'] ?? 0);
        $total = round($principal + $interest, 2);

        if (round((float) $validated['total_loan_amount'], 2) !== $total) {
            return back()
                ->withInput()
                ->withErrors([
                    'total_loan_amount' =>
                        'The total loan amount must equal the principal plus interest.',
                ]);
        }

        if ((float) $validated['monthly_amortization'] > $total) {
            return back()
                ->withInput()
                ->withErrors([
                    'monthly_amortization' =>
                        'Monthly amortization cannot exceed the total loan amount.',
                ]);
        }

        DB::transaction(function () use (
            $loan,
            $validated,
            $principal,
            $interest,
            $total
        ) {
            // Recalculate the outstanding balance using existing payments.
            $totalPaid = (float) $loan->payments()->sum('amount');
            $outstanding = round($total - $totalPaid, 2);

            if ($outstanding < 0) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'total_loan_amount' =>
                        'The new loan amount cannot be less than payments already recorded.',
                ]);
            }

            $status = $validated['status'];

            // Prevent an inconsistent fully-paid status.
            if ($status === 'Fully Paid' && $outstanding > 0) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'status' =>
                        'A loan can only be marked Fully Paid when its outstanding balance is zero.',
                ]);
            }

            // Do not reactivate or cancel a fully paid loan through this form.
            if (
                $loan->status === 'Fully Paid' &&
                $status !== 'Fully Paid'
            ) {
                throw \Illuminate\Validation\ValidationException::withMessages([
                    'status' =>
                        'A fully paid loan cannot be reactivated or cancelled through this form.',
                ]);
            }

            $loan->update([
                'user_id' => $validated['user_id'],
                'loan_reference' => $validated['loan_reference'],
                'loan_provider' => $validated['loan_provider'],
                'loan_type' => $validated['loan_type'],
                'loan_date' => $validated['loan_date'],
                'principal_amount' => $principal,
                'interest_amount' => $interest,
                'total_loan_amount' => $total,
                'monthly_amortization' => $validated['monthly_amortization'],
                'total_paid' => $totalPaid,
                'outstanding_balance' => $outstanding,
                'deduction_start_date' => $validated['deduction_start_date'],
                'deduction_end_date' => $validated['deduction_end_date'] ?? null,
                'status' => $outstanding == 0 ? 'Fully Paid' : $status,
                'remarks' => $validated['remarks'] ?? null,
            ]);
        });

        return redirect()
            ->route('loans.show', $loan->id)
            ->with('success', 'Loan updated successfully.');
    }

    /**
     * Delete a loan only if no payments have been recorded.
     */
    public function destroy(Loan $loan)
    {
        if ($loan->payments()->exists()) {
            return redirect()
                ->route('loans.show', $loan->id)
                ->with('error', 'This loan cannot be deleted because it has recorded payments.');
        }

        $loan->delete();

        return redirect()
            ->route('loans.index')
            ->with('success', 'Loan deleted successfully.');
    }
}

