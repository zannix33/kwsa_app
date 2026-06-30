<!DOCTYPE html>

<html>

<head>

    <title>

        Incident Report

    </title>

    <link
        rel="stylesheet"
        href="{{ asset('css/app.css') }}">

    <style>

        body{

            font-size:14px;

            padding:30px;

        }

        table{

            width:100%;

        }

        td{

            padding:8px;

            vertical-align:top;

        }

        h2{

            text-align:center;

            margin-bottom:30px;

        }

        .section{

            margin-top:25px;

        }

    </style>

</head>

<body onload="window.print()">

<h2>

    INCIDENT REPORT

</h2>

<table border="1" cellspacing="0">

    <tr>

        <td width="25%"><strong>Employee</strong></td>

        <td>{{ optional($incident->user)->full_name }}</td>

    </tr>

    <tr>

        <td><strong>Branch</strong></td>

        <td>{{ optional($incident->branch)->name }}</td>

    </tr>

    <tr>

        <td><strong>Area</strong></td>

        <td>{{ optional($incident->area)->name }}</td>

    </tr>

    <tr>

        <td><strong>Date</strong></td>

        <td>{{ $incident->incident_date->format('F d, Y') }}</td>

    </tr>

    <tr>

        <td><strong>Time</strong></td>

        <td>{{ $incident->incident_time }}</td>

    </tr>

    <tr>

        <td><strong>Category</strong></td>

        <td>{{ $incident->category }}</td>

    </tr>

    <tr>

        <td><strong>Incident Type</strong></td>

        <td>{{ $incident->incident_type }}</td>

    </tr>

    <tr>

        <td><strong>Location</strong></td>

        <td>{{ $incident->location }}</td>

    </tr>

</table>

<div class="section">

    <strong>Description</strong>

    <hr>

    {!! nl2br(e($incident->description)) !!}

</div>

<div class="section">

    <strong>Immediate Action Taken</strong>

    <hr>

    {!! nl2br(e($incident->action_taken)) !!}

</div>

<div class="section">

    <strong>Recommendation</strong>

    <hr>

    {!! nl2br(e($incident->recommendation)) !!}

</div>

<br><br><br>

<table>

    <tr>

        <td align="center">

            _________________________<br>

            Reported By

        </td>

        <td align="center">

            _________________________<br>

            Investigated By

        </td>

    </tr>

</table>

</body>

</html>
