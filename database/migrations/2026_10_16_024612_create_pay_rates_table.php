<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreatePayRatesTable extends Migration
{
    public function up()
    {
        Schema::create('pay_rates', function (Blueprint $table) {
            $table->id();

            $table->foreignId('region_id')
                ->constrained('regions')
                ->onDelete('restrict');

            $table->decimal('daily_rate', 12, 2);

            $table->date('effective_from');
            $table->date('effective_to')->nullable();

            $table->string('wage_order_reference')->nullable();
            $table->text('remarks')->nullable();

            $table->timestamps();

            $table->unique(
                ['region_id', 'effective_from'],
                'pay_rates_region_effective_unique'
            );

            $table->index(
                ['region_id', 'effective_from', 'effective_to'],
                'pay_rates_region_dates_index'
            );
        });
    }

    public function down()
    {
        Schema::dropIfExists('pay_rates');
    }
}
