<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddPremiumPoliciesToClientsTable extends Migration
{
    public function up()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->boolean('apply_overtime_rate')->default(true);
            $table->boolean('apply_night_diff_rate')->default(true);
            $table->string('premium_combination_method', 32)->default('billing_formula');
        });
    }

    public function down()
    {
        Schema::table('clients', function (Blueprint $table) {
            $table->dropColumn([
                'apply_overtime_rate',
                'apply_night_diff_rate',
                'premium_combination_method',
            ]);
        });
    }
}
