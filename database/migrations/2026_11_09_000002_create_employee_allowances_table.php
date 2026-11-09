<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateEmployeeAllowancesTable extends Migration
{
    public function up()
    {
        Schema::create('employee_allowances', function (Blueprint $table) {
            $table->id();
            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('allowance_type_id')->constrained('allowance_types')->restrictOnDelete();
            $table->decimal('amount', 12, 2);
            $table->date('effective_from');
            $table->date('effective_to')->nullable();
            $table->string('status', 20)->default('Active');
            $table->timestamps();
            $table->index(['user_id', 'status']);
            $table->index(['effective_from', 'effective_to']);
        });
    }

    public function down()
    {
        Schema::dropIfExists('employee_allowances');
    }
}
