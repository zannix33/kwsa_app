@extends('layout.master')

@push('plugin-styles')
    <!-- {!! Html::style('/assets/plugins/plugin.css') !!} -->
@endpush


@section('content')
<div class="container">
    <h3>Edit DTR — {{ optional($dtr->user)->firstname }} {{ optional($dtr->user)->lastname }}</h3>
    @if(session('error')) <div class="alert alert-danger">{{ session('error') }}</div> @endif
    @if($errors->any()) <div class="alert alert-danger"><ul>@foreach($errors->all() as $error)<li>{{ $error }}</li>@endforeach</ul></div>@endif
    <form method="POST" action="{{ route('dtr.update',$dtr) }}">
        @csrf @method('PUT')
        <div class="form-row">
            <div class="form-group col-md-4"><label>Work Date</label><input type="date" name="work_date" class="form-control" required value="{{ old('work_date',optional($dtr->work_date)->format('Y-m-d')) }}"></div>
            <div class="form-group col-md-4"><label>Area</label><select name="area_id" class="form-control" required>@foreach($areas as $area)<option value="{{ $area->id }}" {{ old('area_id',$dtr->area_id)==$area->id?'selected':'' }}>{{ $area->name }}</option>@endforeach</select></div>
            <div class="form-group col-md-4"><label>Branch (optional)</label><select name="branch_id" class="form-control"><option value="">No Branch</option>@foreach($branches as $branch)<option value="{{ $branch->id }}" {{ (string)old('branch_id',$dtr->branch_id)===(string)$branch->id?'selected':'' }}>{{ $branch->name }}</option>@endforeach</select></div>
            <div class="form-group col-md-3"><label>Scheduled Start</label><input type="time" name="operation_start" class="form-control" required value="{{ old('operation_start', $dtr->operation_start ? \Carbon\Carbon::parse($dtr->operation_start)->format('H:i') : '') }}"></div>
            <div class="form-group col-md-3"><label>Scheduled End</label><input type="time" name="operation_end" class="form-control" required value="{{ old('operation_end', $dtr->operation_end ? \Carbon\Carbon::parse($dtr->operation_end)->format('H:i') : '') }}"></div>
            <div class="form-group col-md-3"><label>Time In</label><input type="time" name="time_in" class="form-control" required value="{{ old('time_in', $dtr->time_in ? \Carbon\Carbon::parse($dtr->time_in)->format('H:i') : '') }}"></div>
            <div class="form-group col-md-3"><label>Time Out</label><input type="time" name="time_out" class="form-control" required value="{{ old('time_out', $dtr->time_out ? \Carbon\Carbon::parse($dtr->time_out)->format('H:i') : '') }}"></div>
            <div class="form-group col-md-4"><label>Break (minutes)</label><input type="number" min="0" name="break_minutes" class="form-control" value="{{ old('break_minutes',$dtr->break_minutes ?? 60) }}"></div>
        </div>
        <div class="form-group">
            <label class="mr-3"><input type="checkbox" name="is_rest_day" value="1" {{ old('is_rest_day',$dtr->is_rest_day)?'checked':'' }}> Rest Day</label>
            <label class="mr-3"><input type="checkbox" name="is_holiday" value="1" {{ old('is_holiday',$dtr->is_holiday)?'checked':'' }}> Regular Holiday</label>
            <label class="mr-3"><input type="checkbox" name="special_holiday" value="1" {{ old('special_holiday',$dtr->special_holiday)?'checked':'' }}> Special Holiday</label>
            <label><input type="checkbox" name="is_extended_hours" value="1" {{ old('is_extended_hours',$dtr->is_extended_hours)?'checked':'' }}> Extended Hours</label>
        </div>
        <div class="form-group"><label>Remarks</label><textarea name="remarks" class="form-control" rows="2">{{ old('remarks',$dtr->remarks) }}</textarea></div>
        <div class="form-group"><label>Reason for Change (required for audit)</label><textarea name="reason" class="form-control" rows="2" required>{{ old('reason') }}</textarea></div>
        <button class="btn btn-primary">Save & Recalculate</button>
        <a href="{{ route('dtr.index') }}" class="btn btn-light">Cancel</a>
    </form>
    @if($dtr->payroll)
        <div class="alert alert-info mt-3">Linked payroll #{{ $dtr->payroll->id }} ({{ $dtr->payroll->status }}). After saving, use Reprocess Payroll from the DTR list to refresh attendance pay items.</div>
    @endif
</div>
@endsection
