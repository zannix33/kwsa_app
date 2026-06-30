<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateAmmunitionReleasesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('ammunition_releases', function (Blueprint $table) {

            $table->id();

            // Ammunition Batch
            $table->foreignId('ammunition_id')
                ->constrained('ammunitions')
                ->cascadeOnDelete();

            // Firearm Used
            $table->foreignId('arm_id')
                ->nullable()
                ->constrained('arms')
                ->nullOnDelete();

            // Employee / Guard
            $table->foreignId('user_id')
                ->constrained('users')
                ->cascadeOnDelete();

            // Branch
            $table->foreignId('branch_id')
                ->nullable()
                ->constrained('branches')
                ->nullOnDelete();

            // Issue Slip Number
            $table->string('reference_no')->nullable();

            // Purpose
            $table->enum('purpose',[
                'Duty',
                'Training',
                'Firing',
                'Testing',
                'Replacement',
                'Other'
            ])->default('Duty');

            // Quantity Released
            $table->integer('quantity');

            // Quantity Returned (unused)
            $table->integer('returned_quantity')
                ->default(0);

            // Quantity Consumed
            $table->integer('consumed_quantity')
                ->default(0);

            // Release Information
            $table->dateTime('released_at');

            $table->string('released_by');

            // Returned Information
            $table->dateTime('returned_at')
                ->nullable();

            $table->string('received_by')
                ->nullable();

            // Remarks
            $table->text('remarks')
                ->nullable();

            $table->timestamps();

            $table->index('released_at');
            $table->index('purpose');
        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('ammunition_releases');
    }
}
