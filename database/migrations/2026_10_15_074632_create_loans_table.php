<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateLoansTable extends Migration
{
    public function up()
    {
        Schema::create('loans', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')
                ->constrained('users');

            $table->string('loan_reference')->unique();

            $table->enum('loan_provider', [
                'SSS',
                'Pag-IBIG',
                'Company'
            ]);

            $table->string('loan_type');

            $table->date('loan_date');

            $table->decimal('principal_amount', 12, 2);

            $table->decimal('interest_amount', 12, 2)
                ->default(0);

            $table->decimal('total_loan_amount', 12, 2);

            $table->decimal('monthly_amortization', 12, 2);

            $table->decimal('total_paid', 12, 2)
                ->default(0);

            $table->decimal('outstanding_balance', 12, 2);

            $table->date('deduction_start_date');

            $table->date('deduction_end_date')->nullable();

            $table->enum('status', [
                'Active',
                'Fully Paid',
                'Cancelled'
            ])->default('Active');

            $table->text('remarks')->nullable();

            $table->timestamps();
        });
    }

    public function down()
    {
        Schema::dropIfExists('loans');
    }
}
