<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration {
    public function up(): void
    {
        if (!Schema::hasColumn('daily_time_records', 'company_id')) {
            Schema::table('daily_time_records', function (Blueprint $table) {
                $table->foreignId('company_id')->nullable()->after('area_id')->constrained('companies')->nullOnDelete();
            });
        }
    }

    public function down(): void
    {
        if (Schema::hasColumn('daily_time_records', 'company_id')) {
            Schema::table('daily_time_records', function (Blueprint $table) {
                $table->dropForeign(['company_id']);
                $table->dropColumn('company_id');
            });
        }
    }
};
