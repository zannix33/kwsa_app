<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArmsTable extends Migration
{
    public function up()
    {
        Schema::create('arms', function (Blueprint $table) {

            $table->id();

            // Inventory Information
            $table->string('property_no')->unique();
            $table->string('serial_no')->unique();

            // Firearm Details
            $table->string('make');
            $table->string('model');
            $table->string('caliber');

            $table->enum('type', [
                'Handgun',
                'Shotgun',
                'Rifle',
                'SMG',
                'Others'
            ]);

            $table->string('color')->nullable();

            // Purchase
            $table->date('purchase_date')->nullable();
            $table->decimal('purchase_cost', 12, 2)->nullable();

            // Optional supplier
            $table->string('supplier')->nullable();

            // Optional manufacturer
            $table->string('manufacturer')->nullable();

            // Branch Assignment
            $table->unsignedBigInteger('branch_id')->nullable();

            // Current Status
            $table->enum('status', [
                'Available',
                'Issued',
                'Maintenance',
                'Lost',
                'Retired'
            ])->default('Available');

            // Additional Details
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->foreign('branch_id')
                ->references('id')
                ->on('branches')
                ->nullOnDelete();
        });
    }

    public function down()
    {
        Schema::dropIfExists('arms');
    }
}
