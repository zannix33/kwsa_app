<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmmunitionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ammunitions', function (Blueprint $table) {

            $table->id();

            // Inventory
            $table->string('batch_no')->unique();

            $table->string('lot_no')->nullable();

            // Ammunition Details
            $table->string('caliber');

            $table->string('manufacturer')->nullable();

            $table->string('brand')->nullable();

            $table->string('bullet_type')->nullable();
            // FMJ, Hollow Point, Soft Point, etc.

            $table->integer('grain')->nullable();

            // Inventory
            $table->integer('quantity')->default(0);

            $table->integer('minimum_stock')->default(100);

            $table->decimal('unit_cost',12,2)->default(0);

            // Dates
            $table->date('received_date')->nullable();

            $table->date('expiry_date')->nullable();

            // Storage
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();

            // Supplier
            $table->string('supplier')->nullable();

            // Status
            $table->enum('status',[
                'Available',
                'Consumed',
                'Expired',
                'Disposed'
            ])->default('Available');

            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index('caliber');
            $table->index('status');
            $table->index('received_date');
            $table->index('expiry_date');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ammunitions');
    }
}
