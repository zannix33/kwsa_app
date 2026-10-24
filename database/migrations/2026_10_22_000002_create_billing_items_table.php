<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBillingItemsTable extends Migration
{
    public function up()
    {
        Schema::create('billing_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('billing_id')
                ->constrained('billings')
                ->cascadeOnDelete();

            // Optional source links for traceability. Billing amounts remain
            // snapshots even if source records are later removed.
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();

            $table->foreignId('user_id')
                ->nullable()
                ->constrained('users')
                ->nullOnDelete();

            $table->foreignId('payroll_item_id')
                ->nullable()
                ->constrained('payroll_items')
                ->nullOnDelete();

            $table->string('code')->nullable();
            $table->string('type')->default('charge');
            $table->string('description');
            $table->decimal('quantity', 12, 2)->default(0);
            $table->string('unit')->nullable(); // hours, days, guards, posts, etc.
            $table->decimal('rate', 14, 4)->default(0);
            $table->decimal('amount', 14, 2)->default(0);
            $table->json('calculation_details')->nullable();

            $table->timestamps();

            $table->index(['billing_id', 'code']);
            $table->index('branch_id');
            $table->index('payroll_item_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('billing_items');
    }
}
