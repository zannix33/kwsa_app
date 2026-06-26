<?php

namespace App\Services;

use App\Models\Payroll;
use App\Models\PayrollItem;

class PayrollItemService
{
    const BASIC = 'BASIC';
    const OT = 'OT';
    const ND = 'ND';
    const HOLIDAY = 'HOLIDAY';
    const REGHOLIDAY = 'REGHOLIDAY';
    const SPHOLIDAY = 'SPHOLIDAY';

    const SSS = 'SSS';
    const PHIC = 'PHIC';
    const PAGIBIG = 'PAGIBIG';
    const TAX = 'TAX';
    const CB = 'CB';

    /**
     * Add payroll item
     */
    public static function add(
        Payroll $payroll,
        string $type,
        string $code,
        string $description,
        float $amount,
        float $quantity = 1,
        float $rate = 0,
        ?string $remarks = null
    ) {
        return PayrollItem::create([
            'payroll_id' => $payroll->id,
            'type' => $type,
            'code' => $code,
            'description' => $description,
            'quantity' => $quantity,
            'rate' => $rate,
            'amount' => $amount,
            'remarks' => $remarks,
        ]);
    }

    /**
     * Add earning
     */
    public static function earning(
        Payroll $payroll,
        string $code,
        string $description,
        float $amount,
        float $quantity = 1,
        float $rate = 0
    ) {
        return self::add(
            $payroll,
            'earning',
            $code,
            $description,
            $amount,
            $quantity,
            $rate
        );
    }

    /**
     * Add deduction
     */
    public static function deduction(
        Payroll $payroll,
        string $code,
        string $description,
        float $amount,
        float $quantity = 1,
        float $rate = 0
    ) {
        return self::add(
            $payroll,
            'deduction',
            $code,
            $description,
            $amount,
            $quantity,
            $rate
        );
    }

    /**
     * Recalculate payroll totals
     */
    public static function recalculate(
        Payroll $payroll
    ) {
        $earnings = $payroll->items()
            ->where('type', 'earning')
            ->sum('amount');

        $deductions = $payroll->items()
            ->where('type', 'deduction')
            ->sum('amount');

        $payroll->update([
            'gross_pay' => $earnings,
            'total_earnings' => $earnings,
            'total_deductions' => $deductions,
            'net_pay' => $earnings - $deductions,
        ]);

        return $payroll->fresh();
    }

    /**
     * Remove all items
     */
    public static function clear(
        Payroll $payroll
    ) {
        return $payroll->items()->delete();
    }
}
