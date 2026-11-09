<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeReimbursementsTable extends Migration
{
    public function up()
    {
        Schema::create('employee_reimbursements', function (Blueprint $table) {
            $table->bigIncrements('id');
            $table->unsignedBigInteger('user_id')->index();
            $table->unsignedBigInteger('payroll_period_id')->nullable()->index();
            $table->unsignedBigInteger('payroll_id')->nullable()->index();
            $table->date('filed_date')->index();
            $table->string('category', 120);
            $table->string('description', 255);
            $table->decimal('amount', 12, 2);
            $table->string('receipt_path')->nullable();
            $table->string('status', 30)->default('Filed')->index();
            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_reimbursements');
    }
}
