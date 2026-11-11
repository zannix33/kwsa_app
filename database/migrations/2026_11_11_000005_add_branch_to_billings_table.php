<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddBranchToBillingsTable extends Migration
{
    protected function indexExists($indexName)
    {
        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', 'billings')
            ->where('index_name', $indexName)
            ->exists();
    }

    public function up()
    {
        if (!Schema::hasColumn('billings', 'branch_id')) {
            Schema::table('billings', function (Blueprint $table) {
                $table->unsignedBigInteger('branch_id')->nullable()->after('area_id');
            });
        }

        // Add the branch-level key before dropping the area/company-level key.
        if (!$this->indexExists('billings_area_company_branch_period_unique')) {
            Schema::table('billings', function (Blueprint $table) {
                $table->unique(
                    ['area_id', 'company_id', 'branch_id', 'payroll_period_id'],
                    'billings_area_company_branch_period_unique'
                );
            });
        }

        if ($this->indexExists('billings_area_company_period_unique')) {
            Schema::table('billings', function (Blueprint $table) {
                $table->dropUnique('billings_area_company_period_unique');
            });
        }

        $foreignKeyExists = DB::table('information_schema.key_column_usage')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', 'billings')
            ->where('column_name', 'branch_id')
            ->whereNotNull('referenced_table_name')
            ->exists();

        if (!$foreignKeyExists) {
            Schema::table('billings', function (Blueprint $table) {
                $table->foreign('branch_id')
                    ->references('id')->on('branches')
                    ->nullOnDelete();
            });
        }
    }

    public function down()
    {
        $foreignKey = DB::table('information_schema.key_column_usage')
            ->select('constraint_name')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', 'billings')
            ->where('column_name', 'branch_id')
            ->whereNotNull('referenced_table_name')
            ->value('constraint_name');

        if ($foreignKey) {
            Schema::table('billings', function (Blueprint $table) use ($foreignKey) {
                $table->dropForeign($foreignKey);
            });
        }

        if ($this->indexExists('billings_area_company_branch_period_unique')) {
            Schema::table('billings', function (Blueprint $table) {
                $table->dropUnique('billings_area_company_branch_period_unique');
            });
        }

        // Restoring the previous key is only valid if there is at most one
        // branch bill for each area/company/period combination.
        $hasDuplicates = DB::table('billings')
            ->select('area_id', 'company_id', 'payroll_period_id')
            ->whereNotNull('branch_id')
            ->groupBy('area_id', 'company_id', 'payroll_period_id')
            ->havingRaw('COUNT(*) > 1')
            ->exists();

        if (!$hasDuplicates && !$this->indexExists('billings_area_company_period_unique')) {
            Schema::table('billings', function (Blueprint $table) {
                $table->unique(
                    ['area_id', 'company_id', 'payroll_period_id'],
                    'billings_area_company_period_unique'
                );
            });
        }

        if (Schema::hasColumn('billings', 'branch_id')) {
            Schema::table('billings', function (Blueprint $table) {
                $table->dropColumn('branch_id');
            });
        }
    }
}
