<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddExtendedHoursToDailyTimeRecordsTable extends Migration
{
    public function up()
    {
        Schema::table('daily_time_records', function (Blueprint $table) {
            $table->decimal('extended_hours', 5, 2)
                ->default(0)
                ->after('overtime_hours');
        });
    }

    public function down()
    {
        Schema::table('daily_time_records', function (Blueprint $table) {
            $table->dropColumn('extended_hours');
        });
    }
}
