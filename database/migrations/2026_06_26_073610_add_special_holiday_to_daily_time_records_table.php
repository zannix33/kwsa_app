<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddSpecialHolidayToDailyTimeRecordsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::table('daily_time_records', function (Blueprint $table) {

            $table->boolean('special_holiday')
                ->default(false)
                ->after('is_holiday');

        });
    }

    /**
     * Reverse the migrations.
     *
     * @return void
     */
    public function down()
    {
        Schema::table('daily_time_records', function (Blueprint $table) {
            $table->dropColumn('special_holiday');
        });
    }
}
