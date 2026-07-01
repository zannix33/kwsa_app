<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class RemoveMakeFromArmsTable extends Migration
{
    public function up()
    {
        Schema::table('arms', function (Blueprint $table) {
            $table->dropColumn('make');
        });
    }

    public function down()
    {
        Schema::table('arms', function (Blueprint $table) {
            $table->string('make')->nullable();
        });
    }
}
