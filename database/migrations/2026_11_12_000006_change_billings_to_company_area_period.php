<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    private const OLD_INDEX = 'billings_area_company_branch_period_unique';
    private const NEW_INDEX = 'billings_area_company_period_unique';

    public function up(): void
    {
        // Consolidate old branch-level draft bills before enforcing the new
        // company + area + payroll-period billing boundary.
        $duplicates = DB::table('billings')
            ->select('area_id', 'company_id', 'payroll_period_id')
            ->whereNotNull('company_id')
            ->groupBy('area_id', 'company_id', 'payroll_period_id')
            ->havingRaw('COUNT(*) > 1')
            ->get();

        foreach ($duplicates as $group) {
            $bills = DB::table('billings')
                ->where('area_id', $group->area_id)
                ->where('company_id', $group->company_id)
                ->where('payroll_period_id', $group->payroll_period_id)
                ->orderByRaw('branch_id IS NOT NULL')
                ->orderBy('id')
                ->get();

            if ($bills->contains(fn ($bill) => strtolower(trim((string) $bill->status)) !== 'draft')) {
                throw new \RuntimeException(
                    "Cannot consolidate billing records for area {$group->area_id}, company {$group->company_id}, period {$group->payroll_period_id}: one or more bills are not drafts. Resolve these records before rerunning migrations."
                );
            }

            $keep = $bills->first();
            $duplicatesToRemove = $bills->slice(1);
            $amountColumns = [
                'subtotal', 'agency_fee_amount', 'retirement_fund_amount',
                'penalties_amount', 'ewt_amount', 'vat_amount', 'total_amount',
            ];
            $totals = [];
            foreach ($amountColumns as $column) {
                $totals[$column] = round($bills->sum(fn ($bill) => (float) $bill->{$column}), 2);
            }

            foreach ($duplicatesToRemove as $duplicate) {
                DB::table('billing_items')
                    ->where('billing_id', $duplicate->id)
                    ->update(['billing_id' => $keep->id]);
                DB::table('billings')->where('id', $duplicate->id)->delete();
            }

            DB::table('billings')->where('id', $keep->id)->update(array_merge($totals, [
                'branch_id' => null,
                'updated_at' => now(),
            ]));
        }

        // Install the replacement index BEFORE dropping the old one.
        // InnoDB may be relying on the old composite index to support the
        // area_id foreign key. Adding the new index first keeps a valid
        // leftmost-prefix index available throughout the schema change.
        if (!$this->indexExists(self::NEW_INDEX)) {
            Schema::table('billings', function (Blueprint $table) {
                $table->unique(
                    ['area_id', 'company_id', 'payroll_period_id'],
                    self::NEW_INDEX
                );
            });
        }

        // Every bill header is area/company-level; individual branches remain
        // recorded on billing_items.branch_id. The replacement index now
        // supports area_id, so it is safe to remove the branch-level index.
        if ($this->indexExists(self::OLD_INDEX)) {
            Schema::table('billings', function (Blueprint $table) {
                $table->dropUnique(self::OLD_INDEX);
            });
        }

        DB::table('billings')->whereNotNull('branch_id')->update([
            'branch_id' => null,
            'updated_at' => now(),
        ]);
    }

    public function down(): void
    {
        if ($this->indexExists(self::NEW_INDEX)) {
            Schema::table('billings', function (Blueprint $table) {
                $table->dropUnique(self::NEW_INDEX);
            });
        }

        // A branch-level unique index cannot be safely restored because
        // area-level bills intentionally contain items for multiple branches.
    }

    private function indexExists(string $name): bool
    {
        return DB::table('information_schema.statistics')
            ->where('table_schema', DB::getDatabaseName())
            ->where('table_name', 'billings')
            ->where('index_name', $name)
            ->exists();
    }
};
