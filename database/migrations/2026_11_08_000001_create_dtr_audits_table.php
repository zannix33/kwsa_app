<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateDtrAuditsTable extends Migration
{
    public function up()
    {
        Schema::create('dtr_audits', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('daily_time_record_id');
            $table->unsignedBigInteger('user_id')->nullable();
            $table->string('reason', 1000);
            $table->longText('before_data')->nullable();
            $table->longText('after_data')->nullable();
            $table->timestamps();
            $table->index('daily_time_record_id');
            $table->index('user_id');
        });
    }

    public function down()
    {
        Schema::dropIfExists('dtr_audits');
    }
}
