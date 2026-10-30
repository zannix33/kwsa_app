<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddWeekendOperationHoursToBranchesTable extends Migration
{
    public function up()
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->time('operation_saturday_start')->nullable()->after('operation_end');
            $table->time('operation_saturday_end')->nullable()->after('operation_saturday_start');
            $table->time('operation_sunday_start')->nullable()->after('operation_saturday_end');
            $table->time('operation_sunday_end')->nullable()->after('operation_sunday_start');
        });
    }

    public function down()
    {
        Schema::table('branches', function (Blueprint $table) {
            $table->dropColumn([
                'operation_saturday_start',
                'operation_saturday_end',
                'operation_sunday_start',
                'operation_sunday_end',
            ]);
        });
    }
}
