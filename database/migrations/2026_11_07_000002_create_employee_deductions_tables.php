<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeDeductionsTables extends Migration
{
    public function up()
    {
        Schema::create('employee_deductions', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained('users')->onDelete('cascade');
            $table->string('description');
            $table->decimal('amount', 12, 2);
            $table->enum('frequency', ['one_time', 'every_payroll', 'monthly']);
            $table->date('start_date')->nullable();
            $table->date('end_date')->nullable();
            $table->enum('status', ['Active', 'Inactive'])->default('Active');
            $table->enum('payment_status', ['Pending', 'Fully Paid'])->default('Pending');
            $table->timestamps();
            $table->index(['user_id', 'status']);
        });

        Schema::create('employee_deduction_periods', function (Blueprint $table) {
            $table->id();
            $table->foreignId('employee_deduction_id')->constrained('employee_deductions')->onDelete('cascade');
            $table->foreignId('payroll_period_id')->constrained('payroll_periods')->onDelete('cascade');
            $table->foreignId('payroll_id')->nullable()->constrained('payrolls')->nullOnDelete();
            $table->decimal('amount', 12, 2)->nullable();
            $table->enum('status', ['Scheduled', 'Processed'])->default('Scheduled');
            $table->timestamp('processed_at')->nullable();
            $table->timestamps();
            $table->unique(['employee_deduction_id', 'payroll_period_id'], 'deduction_period_unique');
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_deduction_periods');
        Schema::dropIfExists('employee_deductions');
    }
}
