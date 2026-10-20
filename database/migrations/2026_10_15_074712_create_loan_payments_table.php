<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoanPaymentsTable extends Migration
{
    public function up()
    {
        Schema::create('loan_payments', function (Blueprint $table) {
            $table->id();

            $table->foreignId('loan_id')
                ->constrained('loans');

            // Link to the existing payroll period.
            $table->unsignedBigInteger('payroll_period_id');

            $table->date('payment_date');

            $table->decimal('amount', 12, 2);

            $table->string('payment_method')
                ->default('Payroll');

            $table->timestamps();

            // Prevent duplicate loan repayment records
            // for the same loan and payroll period.
            $table->unique(
                ['loan_id', 'payroll_period_id'],
                'loan_period_unique'
            );
        });
    }

    public function down()
    {
        Schema::dropIfExists('loan_payments');
    }
}
