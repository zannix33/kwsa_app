<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

class AddBillingRatesToAreasTable extends Migration
{
    public function up()
    {
        Schema::table('areas', function (Blueprint $table) {
            // Contract pricing (billing), separate from the existing payroll rate fields.
            $table->decimal('billing_half_month_rate_per_guard', 14, 2)
                ->nullable()
                ->after('region_id');

            $table->decimal('billing_additional_post_daily_rate', 14, 2)
                ->nullable()
                ->after('billing_half_month_rate_per_guard');

            // Billing multipliers based on the supplied billing worksheet.
            $table->decimal('billing_regular_ot_multiplier', 6, 4)
                ->default(1.2500)
                ->after('billing_additional_post_daily_rate');

            $table->decimal('billing_special_ot_multiplier', 6, 4)
                ->default(1.6900)
                ->after('billing_regular_ot_multiplier');

            $table->decimal('billing_regular_holiday_ot_multiplier', 6, 4)
                ->default(2.6000)
                ->after('billing_special_ot_multiplier');

            $table->decimal('billing_night_diff_percent', 6, 4)
                ->default(0.1000)
                ->after('billing_regular_holiday_ot_multiplier');

            // Agency fee and withholding tax percentages from the worksheet.
            $table->decimal('billing_agency_fee_percent', 6, 4)
                ->default(0.2000)
                ->after('billing_night_diff_percent');

            $table->decimal('billing_ewt_percent', 6, 4)
                ->default(0.0200)
                ->after('billing_agency_fee_percent');

            // Configure the retirement fund according to the area's contract.
            // Exactly one of the amount/rate may be used by the calculation service.
            $table->decimal('billing_retirement_fund_amount', 14, 2)
                ->nullable()
                ->after('billing_ewt_percent');

            $table->decimal('billing_retirement_fund_percent', 6, 4)
                ->nullable()
                ->after('billing_retirement_fund_amount');

            // Optional fixed penalty; keep null until configured for the area.
            $table->decimal('billing_penalty_amount', 14, 2)
                ->nullable()
                ->after('billing_retirement_fund_percent');

            // Worksheet sample is non-VAT. Change per area if needed.
            $table->string('billing_tax_type', 20)
                ->default('non_vat')
                ->after('billing_penalty_amount');
        });
    }

    public function down()
    {
        Schema::table('areas', function (Blueprint $table) {
            $table->dropColumn([
                'billing_half_month_rate_per_guard',
                'billing_additional_post_daily_rate',
                'billing_regular_ot_multiplier',
                'billing_special_ot_multiplier',
                'billing_regular_holiday_ot_multiplier',
                'billing_night_diff_percent',
                'billing_agency_fee_percent',
                'billing_ewt_percent',
                'billing_retirement_fund_amount',
                'billing_retirement_fund_percent',
                'billing_penalty_amount',
                'billing_tax_type',
            ]);
        });
    }
}
