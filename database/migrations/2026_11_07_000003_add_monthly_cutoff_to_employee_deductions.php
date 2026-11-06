<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddMonthlyCutoffToEmployeeDeductions extends Migration
{
    public function up()
    {
        Schema::table('employee_deductions', function (Blueprint $table) {
            $table->enum('monthly_cutoff', ['first', 'second'])->nullable()->after('frequency');
        });
    }

    public function down()
    {
        Schema::table('employee_deductions', function (Blueprint $table) {
            $table->dropColumn('monthly_cutoff');
        });
    }
}
