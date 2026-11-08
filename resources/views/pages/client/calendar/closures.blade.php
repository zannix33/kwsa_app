@extends('layout.master')
@section('content')
<div class="container-fluid">
    <div class="d-flex justify-content-between align-items-center mb-3 flex-wrap">
        <div>
            <h3 class="mb-0">Closures &amp; Holidays</h3>
            <small class="text-muted">Client calendar: {{ $client->name }}</small>
        </div>
        <div class="mt-2 mt-md-0">
            <a href="{{ route('clients.show', $client) }}" class="btn btn-secondary">Back to Client</a>
            <a href="{{ route('clients.branch-calendar.closures.create', $client) }}" class="btn btn-primary"><i class="fa fa-plus"></i> Add Closure</a>
        </div>
    </div>

    @if(session('success'))<div class="alert alert-success">{{ session('success') }}</div>@endif
    @if(count($overlapWarnings))
        <div class="alert alert-warning">
            <strong><i class="fa fa-exclamation-triangle"></i> Overlapping closures detected</strong>
            <p class="mb-2">These closures overlap on one or more branches. Review them to make sure the dates and branch assignments are intentional.</p>
            <ul class="mb-0">
                @foreach($overlapWarnings as $warning)
                    <li><strong>{{ $warning['first'] }}</strong> and <strong>{{ $warning['second'] }}</strong> overlap {{ $warning['dates'] }} on {{ $warning['count'] }} branch(es).</li>
                @endforeach
            </ul>
        </div>
    @endif

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <strong>Monthly Calendar</strong>
            <div class="d-flex align-items-center mt-2 mt-md-0">
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('clients.branch-calendar.closures', ['client' => $client->id, 'month' => $previousMonth]) }}"><span aria-hidden="true" class="calendar-nav-arrow">&#8249;</span><span class="sr-only">Previous month</span></a>
                <strong class="mx-3">{{ $currentMonthLabel }}</strong>
                <a class="btn btn-sm btn-outline-secondary" href="{{ route('clients.branch-calendar.closures', ['client' => $client->id, 'month' => $nextMonth]) }}"><span aria-hidden="true" class="calendar-nav-arrow">&#8250;</span><span class="sr-only">Next month</span></a>
                <a class="btn btn-sm btn-link" href="{{ route('clients.branch-calendar.closures', ['client' => $client->id]) }}">Today</a>
            </div>
        </div>
        <div class="card-body p-2">
            <div class="row mb-3">
                <div class="col-md-4 mb-2">
                    <label class="small text-muted mb-1" for="calendarTypeFilter">Closure type</label>
                    <select id="calendarTypeFilter" class="form-control form-control-sm">
                        <option value="">All closure types</option>
                        @foreach($closures->pluck('closure_type')->unique()->sort() as $type)
                            <option value="{{ strtolower($type) }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-2">
                    <label class="small text-muted mb-1" for="calendarAreaFilter">Area</label>
                    <select id="calendarAreaFilter" class="form-control form-control-sm">
                        <option value="">All Areas</option>
                        @foreach($areas as $area)
                            <option value="{{ $area->id }}">{{ $area->name }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4 mb-2 d-flex align-items-end">
                    <button type="button" id="clearCalendarFilters" class="btn btn-sm btn-outline-secondary">Clear filters</button>
                </div>
            </div>
            <div class="closure-calendar">
                @foreach(['Sunday','Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'] as $weekday)
                    <div class="calendar-heading">{{ $weekday }}</div>
                @endforeach
                @foreach($calendarDays as $calendarDay)
                    @if($calendarDay)
                        <div class="calendar-day {{ $calendarDay['is_today'] ? 'calendar-today' : '' }}">
                            <div class="calendar-day-number">{{ $calendarDay['day'] }}</div>
                            @foreach($calendarDay['events'] as $event)
                                @php
                                    $typeClass = \Illuminate\Support\Str::slug($event['type'] ?: 'other');
                                    $eventPosition = $calendarDay['date'] === $event['start_date'] ? 'event-start' : ($calendarDay['date'] === $event['end_date'] ? 'event-end' : 'event-middle');
                                @endphp
                                <button type="button" class="calendar-event event-{{ $typeClass }} {{ $eventPosition }}"
                                    data-name="{{ $event['name'] }}" data-type="{{ $event['type'] }}"
                                    data-description="{{ $event['description'] }}" data-start="{{ $event['start_date'] }}"
                                    data-end="{{ $event['end_date'] }}" data-coverage="{{ $event['assignment_mode'] === 'all' ? 'All branches' : ($event['assignment_mode'] === 'areas' ? 'Selected Areas' : ($event['assignment_mode'] === 'all_except' ? 'All branches except selected branches' : 'Selected branches')) }}"
                                    data-branch-count="{{ $event['branch_count'] }}" data-branches="{{ implode(', ', $event['branch_names']) }}"
                                    data-area-ids="{{ implode(',', $event['area_ids']) }}" data-area-names="{{ implode(', ', $event['area_names']) }}"
                                    data-url="{{ $event['url'] }}" data-type-filter="{{ strtolower($event['type']) }}">
                                    {{ $event['name'] }}
                                </button>
                            @endforeach
                        </div>
                    @else
                        <div class="calendar-day calendar-empty"></div>
                    @endif
                @endforeach
            </div>
            <small class="text-muted d-block mt-2">Select a closure on the calendar to view or edit its details.</small>
        </div>
    </div>

    <div id="closureDetailsModal" class="closure-modal" aria-hidden="true">
        <div class="closure-modal-backdrop" data-close-closure-modal></div>
        <div class="closure-modal-dialog" role="dialog" aria-modal="true" aria-labelledby="closureModalTitle">
            <div class="closure-modal-header"><strong id="closureModalTitle">Closure details</strong><button type="button" class="closure-modal-close" data-close-closure-modal>&times;</button></div>
            <div class="closure-modal-body">
                <p><strong>Type:</strong> <span id="closureModalType"></span></p>
                <p><strong>Date(s):</strong> <span id="closureModalDates"></span></p>
                <p><strong>Branch coverage:</strong> <span id="closureModalCoverage"></span></p>
                <p><strong>Affected branches:</strong> <span id="closureModalBranchCount"></span></p>
                <p><strong>Areas:</strong> <span id="closureModalAreas"></span></p>
                <p><strong>Description:</strong> <span id="closureModalDescription"></span></p>
            </div>
            <div class="closure-modal-footer"><a href="#" id="closureModalEdit" class="btn btn-primary btn-sm">Edit closure</a><button type="button" class="btn btn-secondary btn-sm" data-close-closure-modal>Close</button></div>
        </div>
    </div>

    <div class="card mb-4">
        <div class="card-header d-flex justify-content-between align-items-center flex-wrap">
            <strong>Closure List</strong>
            <span class="text-muted small">{{ $closures->count() }} closure(s)</span>
        </div>
        <div class="card-body border-bottom">
            <div class="form-row">
                <div class="form-group col-md-5 mb-2">
                    <input type="text" id="closureSearch" class="form-control" placeholder="Search closure name or description...">
                </div>
                <div class="form-group col-md-3 mb-2">
                    <select id="closureTypeFilter" class="form-control">
                        <option value="">All closure types</option>
                        @foreach($closures->pluck('closure_type')->unique()->sort() as $type)
                            <option value="{{ strtolower($type) }}">{{ $type }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-2 mb-2">
                    <select id="closureYearFilter" class="form-control">
                        <option value="">All years</option>
                        @foreach($closures->map(function($item){ return substr($item->start_date, 0, 4); })->unique()->sort() as $year)
                            <option value="{{ $year }}">{{ $year }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="form-group col-md-2 mb-2">
                    <button type="button" id="clearClosureFilters" class="btn btn-outline-secondary btn-block">Clear</button>
                </div>
            </div>
        </div>
        <div class="table-responsive">
            <table class="table table-bordered table-hover mb-0" id="closuresTable">
                <thead class="thead-light"><tr><th>Closure</th><th>Type</th><th>Dates</th><th>Branch coverage</th><th>Description</th><th style="width:150px">Actions</th></tr></thead>
                <tbody>
                @forelse($closures as $closure)
                    @php
                        $coverage = $closure->assignment_mode === 'all' ? 'All branches' : ($closure->assignment_mode === 'areas' ? 'Selected Areas' : ($closure->assignment_mode === 'all_except' ? 'All except selected branches' : 'Selected branches'));
                        $areaNames = isset($areaIds[$closure->id]) ? \App\Models\Area::whereIn('id', $areaIds[$closure->id]->pluck('area_id'))->pluck('name')->implode(', ') : '';
                        $branchNames = isset($branchIds[$closure->id]) ? \App\Models\Branch::whereIn('id', $branchIds[$closure->id]->pluck('branch_id'))->pluck('name')->implode(', ') : '';
                        if ($areaNames) $coverage .= ': '.$areaNames;
                        if ($branchNames) $coverage .= ': '.$branchNames;
                    @endphp
                    <tr class="closure-row" data-search="{{ strtolower($closure->name.' '.$closure->description) }}" data-type="{{ strtolower($closure->closure_type) }}" data-year="{{ substr($closure->start_date, 0, 4) }}">
                        <td><strong>{{ $closure->name }}</strong></td>
                        <td>{{ $closure->closure_type }}</td>
                        <td>{{ \Carbon\Carbon::parse($closure->start_date)->format('M d, Y') }}@if($closure->start_date !== $closure->end_date) – {{ \Carbon\Carbon::parse($closure->end_date)->format('M d, Y') }}@endif</td>
                        <td>{{ $coverage }}</td>
                        <td>{{ $closure->description ?: '—' }}</td>
                        <td>
                            <a class="btn btn-sm btn-warning" href="{{ route('clients.branch-calendar.closures.edit', [$client, $closure->id]) }}">Edit</a>
                            <form class="d-inline" method="POST" action="{{ route('clients.branch-calendar.closures.destroy', [$client, $closure->id]) }}" onsubmit="return confirm('Delete this closure?')">@csrf @method('DELETE')<button class="btn btn-sm btn-danger">Delete</button></form>
                        </td>
                    </tr>
                @empty
                    <tr><td colspan="6" class="text-center text-muted p-4">No closures have been created for this client.</td></tr>
                @endforelse
                <tr id="noClosureMatches" style="display:none"><td colspan="6" class="text-center text-muted p-4">No closures match your filters.</td></tr>
                </tbody>
            </table>
        </div>
    </div>

    <div class="card">
        <div class="card-header"><strong>Recent Activity</strong></div>
        <div class="table-responsive">
            <table class="table table-sm table-striped mb-0">
                <thead><tr><th>Date / Time</th><th>Administrator</th><th>Action</th><th>Details</th></tr></thead>
                <tbody>
                @forelse($activityLogs as $log)
                    @php($actor = $log->user_id ? \App\Models\User::find($log->user_id) : null)
                    <tr><td>{{ \Carbon\Carbon::parse($log->created_at)->format('M d, Y h:i A') }}</td><td>{{ $actor ? $actor->name : 'Former / deleted user' }}</td><td>{{ ucfirst($log->action) }}</td><td>{{ $log->summary }}</td></tr>
                @empty
                    <tr><td colspan="4" class="text-center text-muted">No activity recorded yet.</td></tr>
                @endforelse
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection

@push('style')
.calendar-nav-arrow{display:inline-block;font-family:Arial,sans-serif;font-size:25px;font-weight:600;line-height:16px;vertical-align:middle;color:#495057;}

.calendar-event{width:100%;text-align:left;border:0;cursor:pointer}
.calendar-event.event-regular-holiday{background:#fde8e7;color:#9b1c1c;border-left:4px solid #dc3545}
.calendar-event.event-special-non-working-holiday,.calendar-event.event-special-holiday{background:#fff1d6;color:#805400;border-left:4px solid #f0ad4e}
.calendar-event.event-local-holiday{background:#e9e2ff;color:#563d91;border-left:4px solid #7952b3}
.calendar-event.event-company-closure,.calendar-event.event-branch-closure{background:#dceeff;color:#174b78;border-left:4px solid #2584c7}
.calendar-event.event-maintenance{background:#e2f4ed;color:#176342;border-left:4px solid #28a06a}
.calendar-event.event-emergency-closure{background:#f8dce8;color:#84264d;border-left:4px solid #c43c77}
.calendar-event.event-other{background:#e8edf2;color:#405365;border-left:4px solid #73879a}
.event-middle{border-radius:0}.event-start{border-radius:4px 0 0 4px}.event-end{border-radius:0 4px 4px 0}
.closure-modal{display:none;position:fixed;inset:0;z-index:3000}.closure-modal.is-open{display:block}
.closure-modal-backdrop{position:absolute;inset:0;background:rgba(0,0,0,.45)}
.closure-modal-dialog{position:relative;background:#fff;width:calc(100% - 30px);max-width:560px;margin:8vh auto;border-radius:6px;box-shadow:0 8px 32px rgba(0,0,0,.25);overflow:hidden}
.closure-modal-header,.closure-modal-footer{padding:14px 18px;border-bottom:1px solid #e5e7eb;display:flex;justify-content:space-between;align-items:center}
.closure-modal-footer{border-top:1px solid #e5e7eb;border-bottom:0;justify-content:flex-end;gap:8px}
.closure-modal-body{padding:18px}.closure-modal-body p{margin-bottom:10px;overflow-wrap:anywhere}
.closure-modal-close{border:0;background:transparent;font-size:25px;line-height:1;cursor:pointer}
.closure-calendar{display:grid;grid-template-columns:repeat(7,minmax(0,1fr));gap:1px;background:#dee2e6;border:1px solid #dee2e6}
.calendar-heading{background:#f3f5f7;text-align:center;font-weight:600;padding:9px 3px;font-size:12px}
.calendar-day{background:#fff;min-height:112px;padding:6px;overflow:hidden}
.calendar-empty{background:#f8f9fa}
.calendar-day-number{font-weight:600;font-size:13px;margin-bottom:5px}
.calendar-today{background:#f0f7ff;box-shadow:inset 0 0 0 2px #80bdff}
.calendar-event{display:block;background:#e8f0fe;color:#174ea6;border-left:3px solid #4285f4;border-radius:3px;padding:3px 5px;margin-bottom:3px;font-size:11px;line-height:1.25;white-space:nowrap;overflow:hidden;text-overflow:ellipsis}
.calendar-event:hover{background:#d2e3fc;color:#174ea6;text-decoration:none}
@media(max-width:767px){.calendar-day{min-height:76px;padding:3px}.calendar-heading{font-size:10px;padding:6px 1px}.calendar-event{font-size:9px;padding:2px}.calendar-day-number{font-size:11px}}
@endpush

@push('custom-scripts')
<script>
(function(){
    var search=document.getElementById('closureSearch');
    var type=document.getElementById('closureTypeFilter');
    var year=document.getElementById('closureYearFilter');
    var clear=document.getElementById('clearClosureFilters');
    var rows=[].slice.call(document.querySelectorAll('.closure-row'));
    var empty=document.getElementById('noClosureMatches');
    function filter(){
        var q=(search.value||'').toLowerCase().trim(), t=type.value, y=year.value, visible=0;
        rows.forEach(function(row){
            var match=(!q || row.getAttribute('data-search').indexOf(q)!==-1) &&
                (!t || row.getAttribute('data-type')===t) &&
                (!y || row.getAttribute('data-year')===y);
            row.style.display=match?'':'none';
            if(match) visible++;
        });
        if(empty) empty.style.display=visible?'none':'';
    }
    search.addEventListener('input',filter);
    type.addEventListener('change',filter);
    year.addEventListener('change',filter);
    clear.addEventListener('click',function(){search.value='';type.value='';year.value='';filter();});

    var typeFilter=document.getElementById('calendarTypeFilter'), areaFilter=document.getElementById('calendarAreaFilter');
    function filterCalendar(){
        var t=typeFilter ? typeFilter.value : '', a=areaFilter ? areaFilter.value : '';
        document.querySelectorAll('.calendar-event').forEach(function(el){
            var areas=(el.getAttribute('data-area-ids')||'').split(',');
            el.style.display=(!t || el.getAttribute('data-type-filter')===t) && (!a || areas.indexOf(a)!==-1) ? 'block' : 'none';
        });
    }
    if(typeFilter) typeFilter.addEventListener('change',filterCalendar);
    if(areaFilter) areaFilter.addEventListener('change',filterCalendar);
    var clearCalendar=document.getElementById('clearCalendarFilters');
    if(clearCalendar) clearCalendar.addEventListener('click',function(){typeFilter.value='';areaFilter.value='';filterCalendar();});

    var modal=document.getElementById('closureDetailsModal');
    function closeModal(){modal.classList.remove('is-open');modal.setAttribute('aria-hidden','true');}
    document.querySelectorAll('.calendar-event').forEach(function(el){
        el.addEventListener('click',function(){
            document.getElementById('closureModalTitle').textContent=el.getAttribute('data-name')||'Closure details';
            document.getElementById('closureModalType').textContent=el.getAttribute('data-type')||'—';
            document.getElementById('closureModalDates').textContent=el.getAttribute('data-start')+' to '+el.getAttribute('data-end');
            document.getElementById('closureModalCoverage').textContent=el.getAttribute('data-coverage')||'—';
            document.getElementById('closureModalBranchCount').textContent=el.getAttribute('data-branch-count')||'0';
            document.getElementById('closureModalAreas').textContent=el.getAttribute('data-area-names')||'—';
            document.getElementById('closureModalDescription').textContent=el.getAttribute('data-description')||'—';
            document.getElementById('closureModalEdit').href=el.getAttribute('data-url')||'#';
            modal.classList.add('is-open');modal.setAttribute('aria-hidden','false');
        });
    });
    modal.querySelectorAll('[data-close-closure-modal]').forEach(function(el){el.addEventListener('click',closeModal);});
    document.addEventListener('keydown',function(e){if(e.key==='Escape')closeModal();});
})();
</script>
@endpush

