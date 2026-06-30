<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArmInspectionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('arm_inspections', function (Blueprint $table) {

            $table->id();

            // Firearm
            $table->foreignId('arm_id')
                ->constrained('arms')
                ->cascadeOnDelete();

            // Inspection Information
            $table->date('inspection_date');

            $table->string('inspector');

            $table->enum('inspection_type',[
                'Daily',
                'Weekly',
                'Monthly',
                'Quarterly',
                'Annual',
                'Spot Inspection'
            ])->default('Monthly');

            // Individual Component Ratings
            $table->enum('barrel_condition',[
                'Excellent',
                'Good',
                'Fair',
                'Poor'
            ]);

            $table->enum('slide_condition',[
                'Excellent',
                'Good',
                'Fair',
                'Poor'
            ])->nullable();

            $table->enum('frame_condition',[
                'Excellent',
                'Good',
                'Fair',
                'Poor'
            ])->nullable();

            $table->enum('trigger_condition',[
                'Excellent',
                'Good',
                'Fair',
                'Poor'
            ]);

            $table->enum('magazine_condition',[
                'Excellent',
                'Good',
                'Fair',
                'Poor'
            ])->nullable();

            $table->enum('sight_condition',[
                'Excellent',
                'Good',
                'Fair',
                'Poor'
            ])->nullable();

            // Overall Result
            $table->enum('overall_condition',[
                'Excellent',
                'Good',
                'Fair',
                'Needs Repair',
                'Unserviceable'
            ]);

            $table->enum('result',[
                'Passed',
                'Failed'
            ]);

            // Recommendations
            $table->text('findings')->nullable();

            $table->text('recommendation')->nullable();

            // Should this firearm be sent for maintenance?
            $table->boolean('requires_maintenance')->default(false);

            // Next inspection
            $table->date('next_inspection')->nullable();

            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index('inspection_date');
            $table->index('next_inspection');
            $table->index('result');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('arm_inspections');
    }
}
