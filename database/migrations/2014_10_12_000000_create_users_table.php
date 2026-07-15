<?php

use Illuminate\Support\Facades\Schema;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Database\Migrations\Migration;

class CreateUsersTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('users', function (Blueprint $table) {
            $table->bigIncrements('id');

            $table->string('name');
            $table->string('firstname');
            $table->string('middlename')->nullable();
            $table->string('lastname');
            $table->string('address')->nullable();
            $table->string('city')->nullable();
            $table->string('province')->nullable();
            $table->string('email')->unique();
            $table->bigInteger('phone')->nullable();
            $table->string('religion')->nullable();
            $table->string('spouse_name')->nullable();
            $table->datetime('spouse_birthdate')->nullable();
            $table->string('beneficiary_name')->nullable();
            $table->bigInteger('beneficiary_contact')->nullable();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password')->nullable();
            $table->string('civil_status')->nullable();
            $table->datetime('birthdate')->nullable();
            $table->string('height')->nullable();
            $table->string('weight')->nullable();
            $table->string('sss')->nullable();
            $table->string('tin')->nullable();
            $table->string('pagibig')->nullable();
            $table->string('philhealth')->nullable();
            $table->string('bloodtype')->nullable();
            $table->string('position')->nullable();
            $table->string('lesp_num')->nullable();
            $table->date('lesp_issued')->nullable();
            $table->date('lesp_expiry')->nullable();
            $table->datetime('date_hired')->nullable();
            $table->datetime('dt_date')->nullable();
            $table->boolean('active')->default(1);

            $table->rememberToken();
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
        Schema::dropIfExists('users');
    }
}
