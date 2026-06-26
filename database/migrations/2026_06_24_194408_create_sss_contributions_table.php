<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateSssContributionsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('sss_contributions', function (Blueprint $table) {
            $table->id();

            $table->decimal('from_salary', 12, 2);
            $table->decimal('to_salary', 12, 2);

            $table->decimal('employee_share', 12, 2);
            $table->decimal('employer_share', 12, 2);

            $table->decimal('ec', 12, 2)->default(0);

            $table->boolean('active')->default(1);

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
        Schema::dropIfExists('sss_contributions');
    }
}
