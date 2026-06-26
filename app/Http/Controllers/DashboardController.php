<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Carbon\Carbon;

class DashboardController extends Controller
{


    public function index()
    {

        $employees = User::all();

        $lesp_expiry = User::whereNotNull('lesp_expiry')
            ->whereDate('lesp_expiry', '<=', Carbon::now()->addMonths(3))
            ->whereDate('lesp_expiry', '>=', Carbon::now())
            ->orderBy('lesp_expiry')
            ->get();

        $emp_age = User::nearAgeRestriction()
            ->orderBy('birthdate')
            ->get();

       return view('dashboard', compact('employees', 'lesp_expiry', 'emp_age'));

    }
}
