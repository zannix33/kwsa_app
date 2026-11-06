<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddInstallmentRulesToEmployeeDeductions extends Migration
{
    public function up()
    {
        Schema::table('employee_deductions', function (Blueprint $table) {
            $table->unsignedInteger('installment_count')->default(0)->after('end_date');
        });
    }

    public function down()
    {
        Schema::table('employee_deductions', function (Blueprint $table) {
            $table->dropColumn('installment_count');
        });
    }
}
