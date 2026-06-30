<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLoanPaymentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_loan_payments', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_loan_id')
                ->constrained()
                ->cascadeOnDelete();

            $table->foreignId('payroll_id')
                ->nullable()
                ->constrained()
                ->nullOnDelete();

            $table->date('payment_date');

            $table->decimal('amount',12,2);

            $table->decimal('balance_after',12,2);
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
        Schema::dropIfExists('user_loan_payments');
    }
}
