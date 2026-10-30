<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="utf-8">
<title>Billing preview — {{ $period['name'] }}</title>
<style>
body{font-family:Arial,sans-serif;font-size:11px;color:#172235;margin:20px}
h1{font-size:17px;margin:0 0 5px}h2{font-size:14px;margin:18px 0 8px}
.meta{margin-bottom:12px}.muted{color:#576579}
.sheet{margin-bottom:34px;page-break-after:always}
.table-wrap{overflow-x:auto}table{border-collapse:collapse;min-width:1850px;width:100%}
th,td{border:1px solid #b8c3d0;padding:5px 6px;white-space:nowrap;text-align:right}
th{background:#dce8f4;font-weight:bold}td.left,th.left{text-align:left}
.branch td{background:#eaf0f7;font-weight:bold;text-align:left}
.total td{background:#dce8f4;font-weight:bold}
.note{font-size:10px;line-height:1.5;max-width:1150px;margin:12px 0}
@media print{body{margin:6mm}.no-print{display:none}.sheet{page-break-after:always}@page{size:A3 landscape;margin:7mm}}
</style>
</head>
<body>
<div class="no-print"><button onclick="window.print()">Print preview</button></div>
@if(!empty($data['unassigned_branches']))<p class="note"><strong>Excluded — branches without an assigned company:</strong> @foreach($data['unassigned_branches'] as $b){{ $b['area_name'] }} / {{ $b['branch_name'] }}@if(!$loop->last); @endif @endforeach</p>@endif
@foreach($areas as $area)
<section class="sheet">
<h1>KINGWIZARD SECURITY AGENCY — Billing worksheet preview</h1>
<div class="meta"><strong>Area:</strong> {{ $area['area_name'] }} &nbsp; | &nbsp;
<strong>Period:</strong> {{ $period['date_from'] }} to {{ $period['date_to'] }} &nbsp; | &nbsp;
<strong>Client:</strong> {{ $area['client_name'] ?? '—' }}</div>
<div class="table-wrap"><table>
<thead>
<tr>
<th class="left">Branch</th><th class="left">Guard name</th><th># guards</th>
<th>Daily wage</th><th>Monthly contract (less retirement)</th><th>Half-month base</th>
<th>Reg OT hrs</th><th>Reg OT amount 125%</th>
<th>Sun/Spcl OT hrs</th><th>Sun/Spcl amount 169%</th>
<th>Legal OT hrs</th><th>Legal amount 260%</th>
<th>Reg ND hrs</th><th>Reg ND amount</th>
<th>Sun/Spcl ND hrs</th><th>Sun/Spcl ND amount</th>
<th>Legal ND hrs</th><th>Legal ND amount</th>
<th>Late/UT hrs</th><th>Late/UT deduction</th>
<th>Before admin</th><th>Admin 20%</th><th>EWT 2% of admin</th>
<th>Retirement (half month)</th><th>Estimated total</th>
</tr>
</thead><tbody>
@php
$sum = array_fill_keys(['base','ot_reg','ot_special','ot_legal','nd_reg','nd_special','nd_legal','deduction','subtotal','admin','ewt','retirement','grand'],0.0);
@endphp
@foreach($area['branches'] as $branch)
<tr class="branch"><td colspan="25">{{ $branch['branch_name'] }} — {{ $branch['company_name'] ?? 'Company not assigned' }}</td></tr>
@foreach($branch['guards'] as $guard)
@php
$rate = (float) $guard['billing_daily_rate'];
$hourly = $rate / 8;
$contract = (float) $guard['contract_cost']['contract_cost_excluding_retirement'];
$retirementMonthly = (float) $guard['contract_cost']['retirement_contribution'];
$base = $contract / 2;
$otReg = (float)$guard['regular_ot_hours'] * $hourly * 1.25;
$otSpecial = (float)$guard['sunday_special_ot_hours'] * $hourly * 1.69;
$otLegal = (float)$guard['regular_holiday_ot_hours'] * $hourly * 2.60;
$ndReg = (float)$guard['regular_nd_hours'] * $hourly * 1.25 * 0.10;
$ndSpecial = (float)$guard['sunday_special_nd_hours'] * $hourly * 1.69 * 0.10;
$ndLegal = (float)$guard['legal_holiday_nd_hours'] * $hourly * 2.60 * 0.10;
$lateHours = (float)$guard['late_hours'] + (float)$guard['undertime_hours'];
// Workbook uses monthly contract + retirement + 20% admin as its late-rate basis.
$lateRate = ($contract + $retirementMonthly + ($contract * 0.20)) * 12 / 365 / 8;
$deduction = $lateHours * $lateRate;
$subtotal = $base + $otReg + $otSpecial + $otLegal + $ndReg + $ndSpecial + $ndLegal - $deduction;
$admin = $subtotal * 0.20;
$ewt = $admin * 0.02;
$retirement = $retirementMonthly / 2;
$grand = $subtotal + $admin - $ewt + $retirement;
foreach(compact('base','otReg','otSpecial','otLegal','ndReg','ndSpecial','ndLegal','deduction','subtotal','admin','ewt','retirement','grand') as $key => $value) {
    $normalized = ['otReg'=>'ot_reg','otSpecial'=>'ot_special','otLegal'=>'ot_legal','ndReg'=>'nd_reg','ndSpecial'=>'nd_special','ndLegal'=>'nd_legal'][$key] ?? $key;
    $sum[$normalized] += $value;
}
@endphp
<tr>
<td class="left">{{ $loop->first ? $branch['branch_name'] : '' }}</td>
<td class="left">{{ $guard['guard_name'] }}</td><td>1</td>
<td>{{ number_format($rate,2) }}</td><td>{{ number_format($contract,2) }}</td><td>{{ number_format($base,2) }}</td>
<td>{{ number_format($guard['regular_ot_hours'],2) }}</td><td>{{ number_format($otReg,2) }}</td>
<td>{{ number_format($guard['sunday_special_ot_hours'],2) }}</td><td>{{ number_format($otSpecial,2) }}</td>
<td>{{ number_format($guard['regular_holiday_ot_hours'],2) }}</td><td>{{ number_format($otLegal,2) }}</td>
<td>{{ number_format($guard['regular_nd_hours'],2) }}</td><td>{{ number_format($ndReg,2) }}</td>
<td>{{ number_format($guard['sunday_special_nd_hours'],2) }}</td><td>{{ number_format($ndSpecial,2) }}</td>
<td>{{ number_format($guard['legal_holiday_nd_hours'],2) }}</td><td>{{ number_format($ndLegal,2) }}</td>
<td>{{ number_format($lateHours,2) }}</td><td>{{ number_format($deduction,2) }}</td>
<td>{{ number_format($subtotal,2) }}</td><td>{{ number_format($admin,2) }}</td><td>{{ number_format($ewt,2) }}</td>
<td>{{ number_format($retirement,2) }}</td><td>{{ number_format($grand,2) }}</td>
</tr>
@endforeach
@endforeach
<tr class="total"><td class="left" colspan="5">AREA TOTAL ({{ $area['totals']['guard_count'] }} guard assignments)</td>
<td>{{ number_format($sum['base'],2) }}</td><td></td><td>{{ number_format($sum['ot_reg'],2) }}</td>
<td></td><td>{{ number_format($sum['ot_special'],2) }}</td><td></td><td>{{ number_format($sum['ot_legal'],2) }}</td>
<td></td><td>{{ number_format($sum['nd_reg'],2) }}</td><td></td><td>{{ number_format($sum['nd_special'],2) }}</td>
<td></td><td>{{ number_format($sum['nd_legal'],2) }}</td><td></td><td>{{ number_format($sum['deduction'],2) }}</td>
<td>{{ number_format($sum['subtotal'],2) }}</td><td>{{ number_format($sum['admin'],2) }}</td>
<td>{{ number_format($sum['ewt'],2) }}</td><td>{{ number_format($sum['retirement'],2) }}</td>
<td>{{ number_format($sum['grand'],2) }}</td></tr>
</tbody></table></div>
<p class="note"><strong>Preview only — not an issued invoice.</strong>
Workbook-derived rates: regular OT 125%, Sunday/special OT 169%, legal holiday OT 260%, ND at 10% of the corresponding OT rate, admin 20%, EWT 2% of admin, retirement fund at half-month.
This preview assumes one half-month contract base per guard/branch assignment and no additional-post, absence, penalty, 5%-of-admin, or client-specific adjustments. These items must be confirmed before final billing. VAT is not included.</p>
</section>
@endforeach
</body></html>
