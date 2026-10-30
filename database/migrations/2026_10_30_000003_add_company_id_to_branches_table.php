<?php
use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;
class AddCompanyIdToBranchesTable extends Migration {
 public function up(){ Schema::table('branches',function(Blueprint $table){ $table->foreignId('company_id')->nullable()->after('area_id')->constrained('companies')->nullOnDelete(); }); }
 public function down(){ Schema::table('branches',function(Blueprint $table){ $table->dropForeign(['company_id']); $table->dropColumn('company_id'); }); }
}
