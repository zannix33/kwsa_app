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
            $table->string('address');
            $table->string('city');
            $table->string('province');
            $table->string('email')->unique();
            $table->timestamp('email_verified_at')->nullable();
            $table->string('password');
            $table->string('civil_status');
            $table->datetime('birthdate');
            $table->string('height');
            $table->string('weight');
            $table->string('sss');
            $table->string('tin');
            $table->string('philhealth');
            $table->string('bloodtype');
            $table->string('position');
            $table->string('so_license');
            $table->string('so_issued');
            $table->string('so_expiry');
            $table->datetime('date_hired');
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
