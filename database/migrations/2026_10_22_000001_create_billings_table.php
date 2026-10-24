<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillingsTable extends Migration
{
    public function up()
    {
        Schema::create('billings', function (Blueprint $table) {
            $table->id();

            // One billing record per area per payroll period.
            $table->foreignId('client_id')
                ->constrained('clients')
                ->restrictOnDelete();

            $table->foreignId('area_id')
                ->constrained('areas')
                ->restrictOnDelete();

            $table->foreignId('payroll_period_id')
                ->constrained('payroll_periods')
                ->restrictOnDelete();

            $table->string('invoice_number')->nullable()->unique();
            $table->string('status')->default('draft'); // draft, finalized, void

            // Stored totals are snapshots; future rate changes should not
            // silently change a bill that has already been generated.
            $table->decimal('subtotal', 14, 2)->default(0);
            $table->decimal('agency_fee_amount', 14, 2)->default(0);
            $table->decimal('retirement_fund_amount', 14, 2)->default(0);
            $table->decimal('penalties_amount', 14, 2)->default(0);
            $table->decimal('ewt_amount', 14, 2)->default(0);
            $table->decimal('vat_amount', 14, 2)->default(0);
            $table->decimal('total_amount', 14, 2)->default(0);

            $table->string('tax_type')->default('non_vat');
            $table->date('billing_date')->nullable();
            $table->date('due_date')->nullable();
            $table->text('notes')->nullable();
            $table->json('calculation_snapshot')->nullable();

            $table->timestamps();

            $table->unique(
                ['area_id', 'payroll_period_id'],
                'billings_area_period_unique'
            );
            $table->index(['client_id', 'payroll_period_id']);
            $table->index('status');
        });
    }

    public function down()
    {
        Schema::dropIfExists('billings');
    }
}
