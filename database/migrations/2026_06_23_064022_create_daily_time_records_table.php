<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDailyTimeRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('daily_time_records', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('payroll_id')
                ->nullable();

            $table->date('work_date');

            $table->dateTime('operation_start')->nullable();
            $table->dateTime('operation_end')->nullable();

            $table->dateTime('time_in')->nullable();
            $table->dateTime('time_out')->nullable();

            $table->integer('break_minutes')->default(60);

            $table->decimal('scheduled_hours',8,2)->default(0);

            $table->decimal('regular_hours',8,2)->default(0);
            $table->decimal('overtime_hours',8,2)->default(0);
            $table->decimal('night_differential_hours',8,2)->default(0);

            $table->decimal('late_hours',8,2)->default(0);
            $table->decimal('undertime_hours',8,2)->default(0);

            $table->decimal('total_hours',8,2)->default(0);

            $table->boolean('is_rest_day')->default(0);
            $table->boolean('is_holiday')->default(0);
            $table->boolean('is_extend_hours')->default(0);

            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('daily_time_records');
    }
}
