<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateIncidentsTable extends Migration
{
    /**
     * Run the migrations.
     *
     * @return void
     */
    public function up()
    {
        Schema::create('incidents', function (Blueprint $table) {
            $table->id();

            $table->foreignId('user_id')->constrained()->cascadeOnDelete();
            $table->foreignId('branch_id')->nullable()->constrained()->nullOnDelete();
            $table->foreignId('area_id')->nullable()->constrained()->nullOnDelete();

            $table->enum('category', ['Major', 'Minor']);

            $table->string('incident_type');

            $table->date('incident_date');
            $table->time('incident_time')->nullable();

            $table->string('location');

            $table->text('description');

            $table->text('action_taken')->nullable();
            $table->text('recommendation')->nullable();

            $table->enum('status', [
                'Open',
                'Under Investigation',
                'Resolved',
                'Closed'
            ])->default('Open');

            $table->foreignId('reported_by')->nullable()->constrained('users');
            $table->foreignId('investigated_by')->nullable()->constrained('users');

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
        Schema::dropIfExists('incidents');
    }
}
