<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPhotoToArmsTable extends Migration
{
    public function up()
    {
        if (!Schema::hasColumn('arms', 'photo')) {
            Schema::table('arms', function (Blueprint $table) {
                $table->string('photo')->nullable()->after('manufacturer');
            });
        }
    }

    public function down()
    {
        if (Schema::hasColumn('arms', 'photo')) {
            Schema::table('arms', function (Blueprint $table) {
                $table->dropColumn('photo');
            });
        }
    }
}
