<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Billing worksheet — {{ $period['name'] ?? 'Payroll period' }}</title>
<style>
*{box-sizing:border-box}body{font-family:Arial,Helvetica,sans-serif;color:#111;margin:14px;font-size:10px}.no-print{margin-bottom:12px}.no-print button{padding:8px 14px;cursor:pointer}
.sheet{width:100%;margin:0 auto 28px;page-break-after:always;break-after:page}.sheet:last-child{page-break-after:auto;break-after:auto}.sheet-title{font-size:17px;font-weight:700;text-align:center;padding:5px;border:1px solid #111}
.info{width:100%;border-collapse:collapse;table-layout:fixed;margin-bottom:8px}.info td{border:1px solid #888;padding:3px 5px;height:21px}.info .label{font-weight:700;background:#f1f1f1}.info .section{font-weight:700;text-align:center;background:#e8edf3}.info .value{font-weight:600}
.billing{width:100%;border-collapse:collapse;table-layout:fixed;font-size:8px}.billing th,.billing td{border:1px solid #555;padding:3px 2px;vertical-align:middle;overflow-wrap:anywhere}.billing th{text-align:center;font-weight:700;background:#e8edf3}.billing td{text-align:right;height:21px}.billing td.left{text-align:left}.billing .subhead th{background:#f4f6f8;font-weight:600}.billing .rate-row td{background:#fafafa;font-weight:600;height:24px}.billing .branch-heading td{background:#f1f1f1;font-weight:700;text-align:left}.billing .branch-total td{background:#edf1f5;font-weight:700}.billing .area-total td{background:#dce5ef;font-weight:700;border-top:2px solid #222}.billing .accounting-head{background:#dce5ef}.summary{width:42%;margin:8px 0 0 auto;border-collapse:collapse}.summary td{border:1px solid #777;padding:4px 6px}.summary td:last-child{text-align:right;font-weight:700}.note{font-size:9px;margin-top:8px}
@media screen{.sheet{overflow-x:auto}.billing{min-width:1500px}}
@media print{body{margin:5mm}.no-print{display:none}.sheet{margin:0;page-break-after:always;break-after:page}.sheet:last-child{page-break-after:auto;break-after:auto}.billing{min-width:0;font-size:6.5px}.billing th,.billing td{padding:2px 1px}.info td{padding:2px 3px}@page{size:A3 landscape;margin:6mm}}
</style>
</head>
<body>
<div class="no-print"><button onclick="window.print()">Print all areas</button></div>
@if(!empty($data['unassigned_branches']))
<p class="note"><strong>Excluded — branches without an assigned company:</strong>
@foreach($data['unassigned_branches'] as $b){{ $b['area_name'] }} / {{ $b['branch_name'] }}@if(!$loop->last); @endif @endforeach</p>
@endif

@foreach($areas as $area)
    @php
        $firstBranch = $area['branches'][0] ?? [];
        $areaCompany = $companies->get($firstBranch['company_id'] ?? null);
        $guardsForHeader = collect($area['branches'])->flatMap(fn($branch) => $branch['guards']);
        $dailyRate = $guardsForHeader->count() ? $guardsForHeader->avg(fn($g) => (float)($g['billing_daily_rate'] ?? 0)) : 0;
        $monthlyRate = $guardsForHeader->count() ? $guardsForHeader->avg(fn($g) => (float)($g['contract_cost']['contract_cost_excluding_retirement'] ?? 0)) : 0;
        $govAmount = $guardsForHeader->count() ? $guardsForHeader->avg(fn($g) => (float)($g['contract_cost']['retirement_contribution'] ?? 0)) : 0;
        $adminHeader = $monthlyRate * .20;
        $monthlyAfterAdmin = $monthlyRate + $govAmount + $adminHeader;
        $areaSum = $area['billing_totals'];
        $money = fn($value) => number_format((float)$value, 2);
        $hours = fn($value) => number_format((float)$value, 2);
    @endphp
    <section class="sheet">
        <div class="sheet-title">KINGWIZARD SECURITY AGENCY</div>
        <table class="info">
            <tr>
                <td class="label" colspan="2">Company:</td><td class="value" colspan="4">{{ $areaCompany->name ?? ($firstBranch['company_name'] ?? '') }}</td>
                <td class="section" colspan="5" rowspan="2">PER CONTRACT:</td><td class="label" colspan="5">Daily Minimum Wage</td><td class="value" colspan="3">{{ $money($dailyRate) }}</td><td colspan="16"></td>
            </tr>
            <tr>
                <td class="label" colspan="2">VAT Registration Number:</td><td class="value" colspan="4">{{ $areaCompany->vat_registration_number ?? '' }}</td><td class="label" colspan="5">Monthly Rate for Guard</td><td class="value" colspan="3">{{ $money($monthlyRate) }}</td><td colspan="16"></td>
            </tr>
            <tr>
                <td class="label" colspan="2">Account Number:</td><td class="value" colspan="4">{{ $areaCompany->account_number ?? '' }}</td><td class="label" colspan="5">Amount to Gov't in favor to Guard</td><td class="value" colspan="3">{{ $money($govAmount) }}</td><td colspan="16"></td>
            </tr>
            <tr>
                <td class="label" colspan="2">Period Covered:</td><td class="value" colspan="4">{{ \Illuminate\Support\Carbon::parse($period['date_from'])->format('F d') }} - {{ \Illuminate\Support\Carbon::parse($period['date_to'])->format('d, Y') }}</td><td class="label" colspan="5">Admin Fee</td><td class="value" colspan="3">{{ $money($adminHeader) }}</td><td colspan="16"></td>
            </tr>
            <tr>
                <td class="label" colspan="2">Area:</td><td class="value" colspan="4">{{ $area['area_name'] }}</td><td class="label" colspan="5">Monthly Rate after Admin Fee</td><td class="value" colspan="3">{{ $money($monthlyAfterAdmin) }}</td><td class="value" colspan="2">(non VAT)</td><td colspan="14"></td>
            </tr>
            <tr><td class="label" colspan="2">Particulars:</td><td class="value" colspan="4">REG/OT</td><td colspan="29"></td></tr>
        </table>

        <table class="billing">
            <colgroup>
                @for($i=0;$i<35;$i++)<col style="width:{{ $i===0?'5.2%':($i===1?'7.4%':'2.5%') }}">@endfor
            </colgroup>
            <thead>
                <tr>
                    <th rowspan="3">Branch Name</th><th rowspan="3">GUARD NAME</th><th rowspan="3"># guards</th><th rowspan="2">Rate</th><th colspan="2">Additional Posts</th><th colspan="2">REG. O.T</th><th colspan="2">Sun Spcl OT</th><th colspan="2">Legal or Reg Holiday OT</th><th colspan="6">Night Shift Differential- 10%</th><th colspan="2">LESS: Absences/Late</th><th rowspan="3">GRAND TOTAL (before Admin Fee and VAT)</th><th rowspan="3">ADMIN FEE</th><th rowspan="3">EWT</th><th rowspan="3">Add: Retirement contribution (for Guard)</th><th rowspan="3">GRAND TOTAL (after Admin Fee and VAT)</th><th colspan="2">Less: Penalty Charges</th><th rowspan="3">NET PAYABLE AMOUNT</th><th rowspan="3"></th><th colspan="6" class="accounting-head">ACCOUNTING USE</th>
                </tr>
                <tr class="subhead">
                    <th># Days</th><th>Rate/per day</th><th>No. of hours</th><th>rate per hour × 125%</th><th>No. of hours</th><th>rate per hour × 169%</th><th>No. of hours</th><th>rate per hour × 260%</th><th># hrs</th><th>Regular OT rate/hr × 10%</th><th># hrs</th><th>Sun Spcl OT rate/hr × 10%</th><th># hrs</th><th>Legal Holiday OT rate/hr × 10%</th><th># hrs</th><th>Rate per hour</th><th># of hours</th><th>Amount/hr</th><th>60091</th><th>60095</th><th>60092</th><th>20202</th><th>A/P Vendor</th><th>Cost Center</th>
                </tr>
                <tr class="rate-row">
                    <td>{{ $money($monthlyRate / 2) }}</td><td></td><td>{{ $money($dailyRate * 12 / 365) }}</td><td></td><td>{{ $money($dailyRate / 8 * 1.25) }}</td><td></td><td>{{ $money($dailyRate / 8 * 1.69) }}</td><td></td><td>{{ $money($dailyRate / 8 * 2.60) }}</td><td></td><td>{{ $money($dailyRate / 8 * 1.25 * .10) }}</td><td></td><td>{{ $money($dailyRate / 8 * 1.69 * .10) }}</td><td></td><td>{{ $money($dailyRate / 8 * 2.60 * .10) }}</td><td></td><td>{{ $money($monthlyAfterAdmin * 12 / 365 / 8) }}</td><td></td><td>200.00</td><td></td><td></td><td></td><td></td><td></td><td></td>
                </tr>
            </thead>
            <tbody>
            @foreach($area['branches'] as $branch)
                @php
                    $guards = $branch['guards'];
                    $sum = $branch['billing_totals'];
                    $totalRegularOtHours = array_sum(array_map(fn($g) => (float)($g['regular_ot_hours'] ?? 0), $guards));
                    $totalSpecialOtHours = array_sum(array_map(fn($g) => (float)($g['sunday_special_ot_hours'] ?? 0), $guards));
                    $totalHolidayOtHours = array_sum(array_map(fn($g) => (float)($g['regular_holiday_ot_hours'] ?? 0), $guards));
                    $totalRegularNdHours = array_sum(array_map(fn($g) => (float)($g['regular_nd_hours'] ?? 0), $guards));
                    $totalSpecialNdHours = array_sum(array_map(fn($g) => (float)($g['sunday_special_nd_hours'] ?? 0), $guards));
                    $totalHolidayNdHours = array_sum(array_map(fn($g) => (float)($g['legal_holiday_nd_hours'] ?? 0), $guards));
                @endphp
                @foreach($guards as $guard)
                    @php
                        $v = $guard['billing'];
                        $daily = (float)$guard['billing_daily_rate'];
                        $additionalDays = (float)($v['additional_post_days'] ?? 0);
                        $deductionHours = (float)($guard['late_hours'] ?? 0) + (float)($guard['undertime_hours'] ?? 0);
                        $penaltyHours = (float)($v['penalty_hours'] ?? 0);
                        $penaltyRate = 200.00;
                        $accountingBase = (float)$v['half_month_rate'] + (float)($v['additional_post_amount'] ?? 0) - (float)$v['deductions'] - (float)($v['penalty_amount'] ?? 0) + (float)$v['retirement_half'];
                        $accountingOt = (float)$v['regular_ot'] + (float)$v['special_ot'] + (float)$v['holiday_ot'] + (float)$v['regular_nd'] + (float)$v['special_nd'] + (float)$v['holiday_nd'];
                    @endphp
                    <tr>
                        <td class="left">{{ $loop->first ? $branch['branch_name'] : '' }}</td><td class="left">{{ $guard['guard_name'] }}</td><td>{{ number_format((float)($v['guard_count'] ?? 1),0) }}</td>
                        <td>{{ $money($v['half_month_rate']) }}</td><td>{{ $hours($additionalDays) }}</td><td>{{ $money($daily * 12 / 365) }}</td>
                        <td>{{ $hours($guard['regular_ot_hours']) }}</td><td>{{ $money($v['regular_ot']) }}</td><td>{{ $hours($guard['sunday_special_ot_hours']) }}</td><td>{{ $money($v['special_ot']) }}</td><td>{{ $hours($guard['regular_holiday_ot_hours']) }}</td><td>{{ $money($v['holiday_ot']) }}</td>
                        <td>{{ $hours($guard['regular_nd_hours']) }}</td><td>{{ $money($v['regular_nd']) }}</td><td>{{ $hours($guard['sunday_special_nd_hours']) }}</td><td>{{ $money($v['special_nd']) }}</td><td>{{ $hours($guard['legal_holiday_nd_hours']) }}</td><td>{{ $money($v['holiday_nd']) }}</td>
                        <td>{{ $hours($deductionHours) }}</td><td>{{ $money($v['deductions']) }}</td><td>{{ $money($v['before_fee']) }}</td><td>{{ $money($v['agency_fee']) }}</td><td>{{ $money($v['ewt']) }}</td><td>{{ $money($v['retirement_half']) }}</td><td>{{ $money($v['gross']) }}</td><td>{{ $hours($penaltyHours) }}</td><td>{{ $money($penaltyRate) }}</td><td>{{ $money($v['net_payable']) }}</td><td></td>
                        <td>{{ $money($accountingBase) }}</td><td>{{ $money($accountingOt) }}</td><td>{{ $money($v['agency_fee']) }}</td><td>{{ $money(-1 * (float)$v['ewt']) }}</td><td></td><td></td>
                    </tr>
                @endforeach
                <tr class="branch-total">
                    <td class="left">{{ $branch['branch_name'] }} TOTAL</td><td></td><td>{{ number_format((float)$sum['guard_count'],0) }}</td><td>{{ $money($sum['half_month_rate']) }}</td><td></td><td></td>
                    <td>{{ $hours($totalRegularOtHours) }}</td><td>{{ $money($sum['regular_ot']) }}</td><td>{{ $hours($totalSpecialOtHours) }}</td><td>{{ $money($sum['special_ot']) }}</td><td>{{ $hours($totalHolidayOtHours) }}</td><td>{{ $money($sum['holiday_ot']) }}</td>
                    <td>{{ $hours($totalRegularNdHours) }}</td><td>{{ $money($sum['regular_nd']) }}</td><td>{{ $hours($totalSpecialNdHours) }}</td><td>{{ $money($sum['special_nd']) }}</td><td>{{ $hours($totalHolidayNdHours) }}</td><td>{{ $money($sum['holiday_nd']) }}</td>
                    <td>{{ $hours($sum['deduction_hours']) }}</td><td>{{ $money($sum['deductions']) }}</td><td>{{ $money($sum['before_fee']) }}</td><td>{{ $money($sum['agency_fee']) }}</td><td>{{ $money($sum['ewt']) }}</td><td>{{ $money($sum['retirement_half']) }}</td><td>{{ $money($sum['gross']) }}</td><td>{{ $hours($sum['penalty_hours'] ?? 0) }}</td><td>{{ $money(200) }}</td><td>{{ $money($sum['net_payable']) }}</td><td></td>
                    <td>{{ $money($sum['half_month_rate'] + $sum['additional_post_amount'] - $sum['deductions'] - $sum['penalty_amount'] + $sum['retirement_half']) }}</td><td>{{ $money($sum['regular_ot'] + $sum['special_ot'] + $sum['holiday_ot'] + $sum['regular_nd'] + $sum['special_nd'] + $sum['holiday_nd']) }}</td><td>{{ $money($sum['agency_fee']) }}</td><td>{{ $money(-1 * $sum['ewt']) }}</td><td></td><td></td>
                </tr>
            @endforeach
            <tr class="area-total">
                <td class="left">AREA {{ $area['area_name'] }} TOTAL — ALL BRANCHES</td><td></td><td>{{ number_format((float)$areaSum['guard_count'],0) }}</td><td>{{ $money($areaSum['half_month_rate']) }}</td><td></td><td></td>
                <td></td><td>{{ $money($areaSum['regular_ot']) }}</td><td></td><td>{{ $money($areaSum['special_ot']) }}</td><td></td><td>{{ $money($areaSum['holiday_ot']) }}</td>
                <td></td><td>{{ $money($areaSum['regular_nd']) }}</td><td></td><td>{{ $money($areaSum['special_nd']) }}</td><td></td><td>{{ $money($areaSum['holiday_nd']) }}</td>
                <td>{{ $hours($areaSum['deduction_hours']) }}</td><td>{{ $money($areaSum['deductions']) }}</td><td>{{ $money($areaSum['before_fee']) }}</td><td>{{ $money($areaSum['agency_fee']) }}</td><td>{{ $money($areaSum['ewt']) }}</td><td>{{ $money($areaSum['retirement_half']) }}</td><td>{{ $money($areaSum['gross']) }}</td><td>{{ $hours($areaSum['penalty_hours'] ?? 0) }}</td><td>{{ $money(200) }}</td><td>{{ $money($areaSum['net_payable']) }}</td><td></td>
                <td>{{ $money($areaSum['half_month_rate'] + $areaSum['additional_post_amount'] - $areaSum['deductions'] - $areaSum['penalty_amount'] + $areaSum['retirement_half']) }}</td><td>{{ $money($areaSum['regular_ot'] + $areaSum['special_ot'] + $areaSum['holiday_ot'] + $areaSum['regular_nd'] + $areaSum['special_nd'] + $areaSum['holiday_nd']) }}</td><td>{{ $money($areaSum['agency_fee']) }}</td><td>{{ $money(-1 * $areaSum['ewt']) }}</td><td></td><td></td>
            </tr>
            </tbody>
        </table>
        <table class="summary">
            <tr><td>GROSS</td><td>{{ $money($areaSum['gross']) }}</td></tr>
            <tr><td>EWT</td><td>{{ $money($areaSum['ewt']) }}</td></tr>
            <tr><td>NET</td><td>{{ $money($areaSum['net_payable']) }}</td></tr>
        </table>
        <p class="note">All branches in this area are listed continuously on this worksheet. Branch names appear only on the first guard row, followed by branch subtotals and one area total.</p>
    </section>
@endforeach
</body>
</html>
