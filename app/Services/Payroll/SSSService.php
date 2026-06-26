<?php

namespace App\Services\Payroll;

use App\Models\SssContribution;

class SSSService
{
    public static function compute(
        float $salary
    ): float {

        $row = SssContribution::where(
            'active',
            1
        )
            ->where(
                'from_salary',
                '<=',
                $salary
            )
            ->where(
                'to_salary',
                '>=',
                $salary
            )
            ->first();

        if (!$row) {
            return 0;
        }

        return (float) $row->employee_share;
    }
}
