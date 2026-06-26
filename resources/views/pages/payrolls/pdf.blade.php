<!DOCTYPE html>
<html>
<head>

    <meta charset="utf-8">

    <title>Payslip</title>

    <style>

        body{
            font-family: DejaVu Sans;
            font-size:12px;
        }

        table{
            width:100%;
            border-collapse: collapse;
        }

        th,
        td{
            border:1px solid #000;
            padding:5px;
        }

        .text-right{
            text-align:right;
        }

        .text-center{
            text-align:center;
        }

        .no-border{
            border:none;
        }

    </style>

</head>
<body>

<h2 class="text-center">

    KingWizard Security Agency

</h2>

<h3 class="text-center">

    PAYSLIP

</h3>

<table>
    <tr>
        <td>Employee No</td>
        <td>{{ $payroll->user->id }}</td>

        <td>Position</td>
        <td>{{ $payroll->user->position }}</td>
    </tr>

    <tr>
        <td>Department</td>
        <td>
            @{{ $payroll->user->department }}
        </td>

        <td>Branch</td>
        <td>{{ $payroll->user->branch->name ?? '' }}</td>
    </tr>

    <tr>

        <td>
            Employee
        </td>

        <td>
            {{ $payroll->user->full_name }}
        </td>

        <td>
            Payroll Period
        </td>

        <td>
            {{ $payroll->period->name }}
        </td>

    </tr>


    <tr>

        <td>
            Payroll ID
        </td>

        <td>
            {{ $payroll->id }}
        </td>

        <td>
            Status
        </td>

        <td>
            {{ $payroll->status }}
        </td>

    </tr>

</table>

<br>

<h4>EARNINGS</h4>

<table>

    <thead>

    <tr>

        <th>Description</th>

        <th width="120">
            Amount
        </th>

    </tr>

    </thead>

    <tbody>

    @foreach(
        $payroll->items
        ->where('type','earning')
        as $item
    )

        <tr>

            <td>
                {{ $item->description }}
            </td>

            <td class="text-right">

                {{ number_format(
                    $item->amount,
                    2
                ) }}

            </td>

        </tr>

    @endforeach

    </tbody>

</table>

<br>

<h4>DEDUCTIONS</h4>

<table>

    <thead>

    <tr>

        <th>Description</th>

        <th width="120">
            Amount
        </th>

    </tr>

    </thead>

    <tbody>

    @foreach(
        $payroll->items
        ->where(
            'type',
            'deduction'
        )
        as $item
    )

        <tr>

            <td>
                {{ $item->description }}
            </td>

            <td class="text-right">

                {{ number_format(
                    $item->amount,
                    2
                ) }}

            </td>

        </tr>

    @endforeach

    </tbody>

</table>

<br>

<table>

    <tr>

        <th>
            Gross Pay
        </th>

        <td class="text-right">

            {{ number_format(
                $payroll->gross_pay,
                2
            ) }}

        </td>

    </tr>

    <tr>

        <th>
            Total Deductions
        </th>

        <td class="text-right">

            {{ number_format(
                $payroll->total_deductions,
                2
            ) }}

        </td>

    </tr>

    <tr>

        <th>
            Net Pay
        </th>

        <th class="text-right">

            {{ number_format(
                $payroll->net_pay,
                2
            ) }}

        </th>

    </tr>

</table>

<br><br><br>

<table class="no-border">

    <tr>

        <td class="no-border text-center">

            ______________________

            <br>

            Employee Signature

        </td>

        <td class="no-border text-center">

            ______________________

            <br>

            Payroll Officer

        </td>

    </tr>

</table>

</body>
</html>
