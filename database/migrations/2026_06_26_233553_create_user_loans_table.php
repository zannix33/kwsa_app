<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateUserLoansTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('user_loans', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('loan_type_id')->constrained();

            $table->string('reference_no')->nullable();

            $table->decimal('loan_amount',12,2);

            $table->decimal('balance',12,2);

            $table->decimal('monthly_amortization',12,2);

            // If payroll is semi-monthly
            $table->decimal('payroll_deduction',12,2);

            $table->date('start_date');

            $table->date('end_date')->nullable();

            $table->unsignedInteger('term_months')->nullable();

            $table->enum('status',[
                'Active',
                'Completed',
                'Cancelled'
            ])->default('Active');

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
        Schema::dropIfExists('user_loans');
    }
}
