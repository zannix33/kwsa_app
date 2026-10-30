<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddCompanyToBillingsTable extends Migration
{
    public function up()
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->foreignId('company_id')->nullable()->after('client_id')->constrained('companies')->nullOnDelete();
            $table->dropUnique('billings_area_period_unique');
            $table->unique(['area_id', 'company_id', 'payroll_period_id'], 'billings_area_company_period_unique');
        });
    }

    public function down()
    {
        Schema::table('billings', function (Blueprint $table) {
            $table->dropUnique('billings_area_company_period_unique');
            $table->dropForeign(['company_id']);
            $table->dropColumn('company_id');
            // Legacy area-only uniqueness cannot be restored if multiple company bills exist.
        });
    }
}
