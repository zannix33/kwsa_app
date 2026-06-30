<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateArmLicensesTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('arm_licenses', function (Blueprint $table) {

            $table->id();

            // Firearm
            $table->foreignId('arm_id')
                ->constrained('arms')
                ->cascadeOnDelete();

            // Registration / License Information
            $table->string('license_number')->unique();

            $table->string('registration_number')->nullable();

            $table->string('permit_number')->nullable();

            $table->enum('license_type',[
                'Firearm Registration',
                'Authority to Carry',
                'Permit to Transport',
                'Other'
            ])->default('Firearm Registration');

            // Validity
            $table->date('issue_date');

            $table->date('expiry_date');

            // Renewal
            $table->date('renewed_at')->nullable();

            $table->date('next_renewal')->nullable();

            // Issuing Office
            $table->string('issued_by')->nullable();

            // Status
            $table->enum('status',[
                'Active',
                'Expired',
                'Suspended',
                'Cancelled'
            ])->default('Active');

            // Optional document upload
            $table->string('document')->nullable();

            // Notes
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->index('expiry_date');
            $table->index('status');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::dropIfExists('arm_licenses');
    }
}
