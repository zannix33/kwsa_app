<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

class AddCompanyToBillingsTable extends Migration
{
    /**
     * Check an index by name. This also makes the migration safe to retry if
     * MySQL added company_id before the previous attempt failed at dropUnique.
     */
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
        // The previous attempt may have added this column before failing.
        if (!Schema::hasColumn('billings', 'company_id')) {
            Schema::table('billings', function (Blueprint $table) {
                $table->unsignedBigInteger('company_id')->nullable()->after('client_id');
            });
        }

        // Add the replacement index FIRST. It starts with area_id, so MySQL
        // can continue using it for the existing area_id foreign key when the
        // old area/period unique index is removed.
        if (!$this->indexExists('billings_area_company_period_unique')) {
            Schema::table('billings', function (Blueprint $table) {
                $table->unique(
                    ['area_id', 'company_id', 'payroll_period_id'],
                    'billings_area_company_period_unique'
                );
            });
        }

        // Drop the old index only after its replacement is in place.
        if ($this->indexExists('billings_area_period_unique')) {
            Schema::table('billings', function (Blueprint $table) {
                $table->dropUnique('billings_area_period_unique');
            });
        }

        // Add the FK last; do not duplicate it if a prior partial run created it.
        $foreignKeyExists = DB::table('information_schema.key_column_usage')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', 'billings')
            ->where('column_name', 'company_id')
            ->whereNotNull('referenced_table_name')
            ->exists();

        if (!$foreignKeyExists) {
            Schema::table('billings', function (Blueprint $table) {
                $table->foreign('company_id')
                    ->references('id')->on('companies')
                    ->nullOnDelete();
            });
        }
    }

    public function down()
    {
        // Restore the old index before removing the replacement, so area_id's
        // foreign key always has a supporting index. This rollback is only
        // possible when no area/period pair has multiple company bills.
        if (!$this->indexExists('billings_area_period_unique')) {
            Schema::table('billings', function (Blueprint $table) {
                $table->unique(['area_id', 'payroll_period_id'], 'billings_area_period_unique');
            });
        }

        if ($this->indexExists('billings_area_company_period_unique')) {
            Schema::table('billings', function (Blueprint $table) {
                $table->dropUnique('billings_area_company_period_unique');
            });
        }

        $foreignKey = DB::table('information_schema.key_column_usage')
            ->select('constraint_name')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', 'billings')
            ->where('column_name', 'company_id')
            ->whereNotNull('referenced_table_name')
            ->value('constraint_name');

        if ($foreignKey) {
            Schema::table('billings', function (Blueprint $table) use ($foreignKey) {
                $table->dropForeign($foreignKey);
            });
        }

        if (Schema::hasColumn('billings', 'company_id')) {
            Schema::table('billings', function (Blueprint $table) {
                $table->dropColumn('company_id');
            });
        }
    }
}
