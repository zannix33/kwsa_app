<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchOperatingSchedulesTable extends Migration
{
    public function up()
    {
        Schema::create('branch_operating_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week'); // ISO-8601: Monday=1, Sunday=7
            $table->boolean('is_open')->default(true);
            $table->time('opens_at')->nullable();
            $table->time('closes_at')->nullable();
            $table->timestamps();
            $table->unique(['branch_id', 'day_of_week']);
        });
    }
    public function down() { Schema::dropIfExists('branch_operating_schedules'); }
}
