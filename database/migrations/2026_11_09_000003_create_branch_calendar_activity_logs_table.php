<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class CreateBranchCalendarActivityLogsTable extends Migration
{
    public function up()
    {
        Schema::create('branch_calendar_activity_logs', function (Blueprint $table) {
            $table->id();
            $table->foreignId('client_id')->constrained()->cascadeOnDelete();
            $table->foreignId('user_id')->nullable()->constrained()->nullOnDelete();
            $table->string('action', 40);
            $table->string('subject_type', 40);
            $table->unsignedBigInteger('subject_id')->nullable();
            $table->text('summary');
            $table->json('details')->nullable();
            $table->timestamps();
            $table->index(['client_id', 'created_at']);
        });
    }
    public function down() { Schema::dropIfExists('branch_calendar_activity_logs'); }
}
