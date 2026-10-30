<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class CreateCompaniesTable extends Migration {
 public function up(){ Schema::create('companies',function(Blueprint $table){ $table->id(); $table->foreignId('client_id')->constrained('clients')->onDelete('cascade'); $table->string('name'); $table->boolean('active')->default(true); $table->timestamps(); $table->index(['client_id','name']); }); }
 public function down(){ Schema::dropIfExists('companies'); }
}
