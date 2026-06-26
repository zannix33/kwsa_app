<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayrollItemsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('payroll_items', function (Blueprint $table) {
            $table->id();

            $table->foreignId('payroll_id')
                ->constrained()
                ->onDelete('cascade');

            $table->enum('type',[
                'earning',
                'deduction'
            ]);

            $table->string('code');

            $table->string('description');

            $table->decimal('quantity',10,2)
                ->default(1);

            $table->decimal('rate',12,2)
                ->default(0);

            $table->decimal('amount',12,2);

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
        Schema::dropIfExists('payroll_items');
    }
}
