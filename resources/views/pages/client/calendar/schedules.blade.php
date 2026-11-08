@extends('layout.master')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <div>
            <h3 class="mb-0">Branch Operating Schedules</h3>
            <small class="text-muted">{{ $client->name }} — weekly schedule by branch</small>
        </div>
        <a href="{{ route('clients.show', $client) }}" class="btn btn-secondary">Back to Client</a>
    </div>

    @if(session('success'))
        <div class="alert alert-success">{{ session('success') }}</div>
    @endif
    @if($errors->any())
        <div class="alert alert-danger">
            <strong>Please check the following:</strong>
            <ul class="mb-0">
                <?php foreach ($errors->all() as $error): ?>
                    <li>{{ $error }}</li>
                <?php endforeach; ?>
            </ul>
        </div>
    @endif

    <div class="alert alert-info">Set each branch's regular weekly opening days and optional operating hours. These schedules are informational for now and do not affect billing, payroll, or attendance.</div>

    <?php $days = [1 => 'Monday', 2 => 'Tuesday', 3 => 'Wednesday', 4 => 'Thursday', 5 => 'Friday', 6 => 'Saturday', 7 => 'Sunday']; ?>
    <form method="POST" action="{{ route('clients.branch-calendar.schedules.save', $client) }}">
        @csrf

        <?php if ($client->areas->count() === 0): ?>
            <div class="alert alert-warning">This client has no Areas yet. Add an Area and its branches before configuring schedules.</div>
        <?php else: ?>
            <?php foreach ($client->areas as $area): ?>
                <div class="card mb-3">
                    <div class="card-header">
                        <strong>{{ $area->name }}</strong>
                        <span class="text-muted">({{ $area->branches->count() }} branches)</span>
                    </div>
                    <div class="card-body">
                        <?php if ($area->branches->count() === 0): ?>
                            <p class="text-muted mb-0">No branches in this Area.</p>
                        <?php else: ?>
                            <?php foreach ($area->branches as $branch): ?>
                                <div class="border rounded p-3 mb-3">
                                    <h5>{{ $branch->name }}</h5>
                                    <div class="table-responsive">
                                        <table class="table table-sm table-bordered mb-0">
                                            <thead class="thead-light">
                                                <tr>
                                                    <th style="width:20%">Day</th>
                                                    <th style="width:15%">Open?</th>
                                                    <th style="width:30%">Opening time</th>
                                                    <th style="width:30%">Closing time</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <?php foreach ($days as $dayNo => $dayName): ?>
                                                    <?php
                                                        $saved = $branch->operatingSchedules->firstWhere('day_of_week', $dayNo);
                                                        $legacyOpen = $dayNo <= 5 || ($dayNo === 6 && $branch->operation_saturday_start) || ($dayNo === 7 && $branch->operation_sunday_start);
                                                        $isOpen = old("schedules.{$branch->id}.{$dayNo}.is_open", $saved ? (int) $saved->is_open : ($legacyOpen ? 1 : 0));
                                                        $start = old("schedules.{$branch->id}.{$dayNo}.opens_at", $saved ? substr((string) $saved->opens_at, 0, 5) : ($dayNo === 6 ? $branch->operation_saturday_start : ($dayNo === 7 ? $branch->operation_sunday_start : $branch->operation_start)));
                                                        $end = old("schedules.{$branch->id}.{$dayNo}.closes_at", $saved ? substr((string) $saved->closes_at, 0, 5) : ($dayNo === 6 ? $branch->operation_saturday_end : ($dayNo === 7 ? $branch->operation_sunday_end : $branch->operation_end)));
                                                    ?>
                                                    <tr>
                                                        <td class="align-middle">{{ $dayName }}</td>
                                                        <td class="align-middle">
                                                            <input type="hidden" name="schedules[{{ $branch->id }}][{{ $dayNo }}][is_open]" value="0">
                                                            <label class="mb-0"><input type="checkbox" name="schedules[{{ $branch->id }}][{{ $dayNo }}][is_open]" value="1" {{ $isOpen ? 'checked' : '' }}> Open</label>
                                                        </td>
                                                        <td><input type="time" class="form-control form-control-sm" name="schedules[{{ $branch->id }}][{{ $dayNo }}][opens_at]" value="{{ $start }}"></td>
                                                        <td><input type="time" class="form-control form-control-sm" name="schedules[{{ $branch->id }}][{{ $dayNo }}][closes_at]" value="{{ $end }}"></td>
                                                    </tr>
                                                <?php endforeach; ?>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>

        @if($client->areas->sum(function ($area) { return $area->branches->count(); }) > 0)
            <button class="btn btn-primary" type="submit"><i class="fa fa-save"></i> Save Operating Schedules</button>
        @endif
        <a href="{{ route('clients.show', $client) }}" class="btn btn-light">Cancel</a>
    </form>
</div>
@endsection
