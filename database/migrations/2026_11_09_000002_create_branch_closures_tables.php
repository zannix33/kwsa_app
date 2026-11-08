<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchClosuresTables extends Migration
{
    public function up()
    {
        Schema::create('branch_closures', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->string('name');
            $table->string('closure_type', 80);
            $table->date('start_date');
            $table->date('end_date');
            $table->enum('assignment_mode', ['all', 'areas', 'branches', 'all_except'])->default('all');
            $table->text('description')->nullable();
            $table->timestamps();
            $table->index(['client_id', 'start_date', 'end_date']);
        });

        Schema::create('branch_closure_areas', function (Blueprint $table) {
            $table->foreignId('branch_closure_id')->constrained('branch_closures')->cascadeOnDelete();
            $table->foreignId('area_id')->constrained()->cascadeOnDelete();
            $table->primary(['branch_closure_id', 'area_id']);
        });

        Schema::create('branch_closure_branches', function (Blueprint $table) {
            $table->foreignId('branch_closure_id')->constrained('branch_closures')->cascadeOnDelete();
            $table->foreignId('branch_id')->constrained()->cascadeOnDelete();
            $table->primary(['branch_closure_id', 'branch_id']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('branch_closure_branches');
        Schema::dropIfExists('branch_closure_areas');
        Schema::dropIfExists('branch_closures');
    }
}
