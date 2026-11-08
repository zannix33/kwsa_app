<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class ReplaceBranchWeeklySchedulesWithWeekendClosures extends Migration
{
    public function up()
    {
        Schema::table('branches', function (Blueprint $table) {
            if (!Schema::hasColumn('branches', 'is_closed_saturday')) {
                $table->boolean('is_closed_saturday')->default(false);
            }
            if (!Schema::hasColumn('branches', 'is_closed_sunday')) {
                $table->boolean('is_closed_sunday')->default(false);
            }
        });

        Schema::dropIfExists('branch_operating_schedules');
    }

    public function down()
    {
        Schema::table('branches', function (Blueprint $table) {
            if (Schema::hasColumn('branches', 'is_closed_saturday')) {
                $table->dropColumn('is_closed_saturday');
            }
            if (Schema::hasColumn('branches', 'is_closed_sunday')) {
                $table->dropColumn('is_closed_sunday');
            }
        });

        Schema::create('branch_operating_schedules', function (Blueprint $table) {
            $table->id();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->unsignedTinyInteger('day_of_week');
            $table->boolean('is_open')->default(true);
            $table->time('opens_at')->nullable();
            $table->time('closes_at')->nullable();
            $table->timestamps();
            $table->unique(['branch_id', 'day_of_week']);
        });
    }
}
