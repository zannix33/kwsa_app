<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArmMaintenancesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('arm_maintenances', function (Blueprint $table) {

            $table->id();

            // Firearm
            $table->foreignId('arm_id')
                ->constrained('arms')
                ->cascadeOnDelete();

            // Maintenance Details
            $table->string('maintenance_no')->unique();
            $table->date('maintenance_date');

            $table->enum('maintenance_type', [
                'Preventive',
                'Corrective',
                'Cleaning',
                'Repair',
                'Overhaul'
            ]);

            // Gunsmith / Armorer
            $table->string('performed_by');

            // External service provider (optional)
            $table->string('service_provider')->nullable();

            // Work performed
            $table->text('description');

            // Parts replaced
            $table->text('parts_replaced')->nullable();

            // Cost breakdown
            $table->decimal('labor_cost',12,2)->default(0);
            $table->decimal('parts_cost',12,2)->default(0);
            $table->decimal('total_cost',12,2)->default(0);

            // Firearm condition after maintenance
            $table->enum('condition_after',[
                'Excellent',
                'Good',
                'Fair',
                'Needs Repair',
                'Unserviceable'
            ])->default('Good');

            // Next schedule
            $table->date('next_due')->nullable();

            // Maintenance completion
            $table->boolean('completed')->default(true);

            // Additional Notes
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index('maintenance_date');
            $table->index('next_due');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('arm_maintenances');
    }
}
