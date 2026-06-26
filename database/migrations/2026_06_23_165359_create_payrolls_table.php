<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payrolls', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payroll_period_id')
                ->constrained()
                ->onDelete('cascade');

            $table->foreignId('user_id')
                ->constrained()
                ->onDelete('cascade');

            $table->decimal('gross_pay',12,2)->default(0);

            $table->decimal('total_earnings',12,2)->default(0);

            $table->decimal('total_deductions',12,2)->default(0);

            $table->decimal('net_pay',12,2)->default(0);

            $table->enum('status',[
                'Draft',
                'Processed',
                'Paid'
            ])->default('Processed');

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
        Schema::dropIfExists('payrolls');
    }
}
