<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArmAssignmentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('arm_assignments', function (Blueprint $table) {

            $table->id();

            // Firearm
            $table->foreignId('arm_id')
                ->constrained('arms')
                ->cascadeOnDelete();

            // Assigned Guard / Employee
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Assigned Branch
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();

            // Assignment Information
            $table->dateTime('issued_at');

            $table->dateTime('returned_at')->nullable();

            // Accountability
            $table->string('issued_by');

            $table->string('received_by')->nullable();

            // Optional issue slip / acknowledgment receipt
            $table->string('reference_no')->nullable();

            // Firearm condition before issue
            $table->enum('condition_before', [
                'Excellent',
                'Good',
                'Fair',
                'Needs Repair'
            ])->default('Good');

            // Firearm condition after return
            $table->enum('condition_after', [
                'Excellent',
                'Good',
                'Fair',
                'Needs Repair'
            ])->nullable();

            // Round count issued with firearm
            $table->integer('ammo_issued')->default(0);

            // Round count returned
            $table->integer('ammo_returned')->default(0);

            // Missing rounds explanation
            $table->text('ammo_remarks')->nullable();

            // General Remarks
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index('issued_at');
            $table->index('returned_at');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('arm_assignments');
    }
}
