<?php

namespace App\Http\Controllers;

use App\Models\Client;
use App\Models\Branch;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class ClientBranchCalendarController extends Controller
{
    private function logActivity(Client $client, $action, $subjectType, $subjectId, $summary, array $details = [])
    {
        DB::table('branch_calendar_activity_logs')->insert([
            'client_id' => $client->id,
            'user_id' => auth()->id(),
            'action' => $action,
            'subject_type' => $subjectType,
            'subject_id' => $subjectId,
            'summary' => $summary,
            'details' => json_encode($details),
            'created_at' => now(),
            'updated_at' => now(),
        ]);
    }

    public function schedules(Client $client)
    {
        $client->load(['areas' => function ($q) { $q->with(['branches' => function ($b) { $b->orderBy('name')->with('operatingSchedules'); }])->orderBy('name'); }]);
        return view('pages.client.calendar.schedules', compact('client'));
    }

    public function saveSchedules(Request $request, Client $client)
    {
        $data = $request->validate([
            'schedules' => 'required|array',
            'schedules.*' => 'array',
            'schedules.*.*.is_open' => 'required|boolean',
            'schedules.*.*.opens_at' => 'nullable|date_format:H:i',
            'schedules.*.*.closes_at' => 'nullable|date_format:H:i',
        ]);
        $branchIds = $client->areas()->with('branches')->get()->flatMap(function ($area) { return $area->branches->pluck('id'); })->map(function ($id) { return (string) $id; })->all();
        DB::transaction(function () use ($data, $branchIds) {
            foreach ($data['schedules'] as $branchId => $days) {
                abort_unless(in_array((string) $branchId, $branchIds, true), 403, 'A branch does not belong to this client.');
                foreach ($days as $day => $schedule) {
                    abort_unless((string) $day >= '1' && (string) $day <= '7', 422, 'Invalid day of week.');
                    $open = (bool) $schedule['is_open'];
                    if ($open && (!empty($schedule['opens_at']) xor !empty($schedule['closes_at']))) {
                        abort(422, 'Enter both opening and closing times, or leave both blank.');
                    }
                    if ($open && !empty($schedule['opens_at']) && $schedule['closes_at'] <= $schedule['opens_at']) {
                        abort(422, 'Closing time must be later than opening time.');
                    }
                    DB::table('branch_operating_schedules')->updateOrInsert(
                        ['branch_id' => $branchId, 'day_of_week' => (int) $day],
                        ['is_open' => $open, 'opens_at' => $open ? ($schedule['opens_at'] ?: null) : null, 'closes_at' => $open ? ($schedule['closes_at'] ?: null) : null, 'updated_at' => now(), 'created_at' => now()]
                    );
                }
            }
        });
        $this->logActivity($client, 'updated', 'schedule', null, 'Updated branch operating schedules.');
        return redirect()->route('clients.branch-calendar.schedules', $client)->with('success', 'Branch operating schedules saved.');
    }

    public function closures(Request $request, Client $client)
    {
        $month = $request->query('month');
        try {
            $monthStart = $month ? \Carbon\Carbon::createFromFormat('Y-m', $month)->startOfMonth() : now()->startOfMonth();
        } catch (\Exception $e) {
            $monthStart = now()->startOfMonth();
        }
        $monthEnd = $monthStart->copy()->endOfMonth();

        $closures = DB::table('branch_closures')
            ->where('client_id', $client->id)
            ->orderBy('start_date')
            ->orderBy('name')
            ->get();

        $areas = $client->areas()->withCount('branches')->orderBy('name')->get();
        $clientBranches = $client->areas()->with(['branches' => function ($q) {
            $q->orderBy('name');
        }])->get()->flatMap(function ($area) {
            return $area->branches->map(function ($branch) use ($area) {
                $branch->area_name = $area->name;
                return $branch;
            });
        })->values();

        $areaIds = DB::table('branch_closure_areas')->get()->groupBy('branch_closure_id');
        $branchIds = DB::table('branch_closure_branches')->get()->groupBy('branch_closure_id');

        $allBranchIds = $clientBranches->pluck('id')->map(function ($id) { return (int) $id; })->all();
        $closureBranchSets = [];
        foreach ($closures as $item) {
            $selectedAreaIds = isset($areaIds[$item->id]) ? $areaIds[$item->id]->pluck('area_id')->map(function ($v) { return (int) $v; })->all() : [];
            $selectedBranchIds = isset($branchIds[$item->id]) ? $branchIds[$item->id]->pluck('branch_id')->map(function ($v) { return (int) $v; })->all() : [];
            if ($item->assignment_mode === 'all') {
                $effective = $allBranchIds;
            } elseif ($item->assignment_mode === 'areas') {
                $effective = $clientBranches->filter(function ($branch) use ($selectedAreaIds) {
                    return in_array((int) $branch->area_id, $selectedAreaIds, true);
                })->pluck('id')->map(function ($v) { return (int) $v; })->all();
            } elseif ($item->assignment_mode === 'all_except') {
                $effective = array_values(array_diff($allBranchIds, $selectedBranchIds));
            } else {
                $effective = array_values(array_intersect($allBranchIds, $selectedBranchIds));
            }
            $closureBranchSets[$item->id] = $effective;
        }

        // Build calendar events with branch/area coverage for filters and details.
        $calendarEvents = [];
        $branchLookup = $clientBranches->keyBy('id');
        foreach ($closures as $item) {
            $effectiveIds = $closureBranchSets[$item->id] ?? [];
            $effectiveBranches = collect($effectiveIds)->map(function ($id) use ($branchLookup) {
                return $branchLookup->get($id);
            })->filter();
            $effectiveAreaIds = $effectiveBranches->pluck('area_id')->unique()->values()->all();
            $effectiveAreaNames = $effectiveBranches->pluck('area_name')->unique()->values()->all();
            $branchNames = $effectiveBranches->pluck('name')->values()->all();
            $from = \Carbon\Carbon::parse($item->start_date)->startOfDay();
            $to = \Carbon\Carbon::parse($item->end_date)->startOfDay();
            $cursor = $from->greaterThan($monthStart) ? $from->copy() : $monthStart->copy();
            $last = $to->lessThan($monthEnd) ? $to->copy() : $monthEnd->copy();
            if ($cursor->greaterThan($last)) continue;
            while ($cursor->lte($last)) {
                $calendarEvents[$cursor->format('Y-m-d')][] = [
                    'id' => $item->id, 'name' => $item->name, 'type' => $item->closure_type,
                    'description' => $item->description, 'start_date' => $item->start_date,
                    'end_date' => $item->end_date, 'assignment_mode' => $item->assignment_mode,
                    'branch_count' => count($effectiveIds), 'branch_names' => $branchNames,
                    'area_ids' => $effectiveAreaIds, 'area_names' => $effectiveAreaNames,
                    'url' => route('clients.branch-calendar.closures.edit', [$client, $item->id]),
                ];
                $cursor->addDay();
            }
        }

        // Identify overlapping date ranges that affect at least one common branch.
        $overlapWarnings = [];
        $items = $closures->values();
        for ($i = 0; $i < $items->count(); $i++) {
            for ($j = $i + 1; $j < $items->count(); $j++) {
                $a = $items[$i];
                $b = $items[$j];
                if ($a->start_date <= $b->end_date && $b->start_date <= $a->end_date) {
                    $common = array_intersect($closureBranchSets[$a->id] ?? [], $closureBranchSets[$b->id] ?? []);
                    if (count($common)) {
                        $overlapWarnings[] = [
                            'first' => $a->name,
                            'second' => $b->name,
                            'count' => count($common),
                            'dates' => max($a->start_date, $b->start_date) . ' to ' . min($a->end_date, $b->end_date),
                        ];
                    }
                }
            }
        }

        $activityLogs = DB::table('branch_calendar_activity_logs')
            ->where('client_id', $client->id)
            ->orderByDesc('created_at')
            ->limit(30)
            ->get();

        $calendarDays = [];
        $firstWeekday = (int) $monthStart->dayOfWeek; // Sunday = 0
        for ($i = 0; $i < $firstWeekday; $i++) $calendarDays[] = null;
        for ($day = 1; $day <= $monthStart->daysInMonth; $day++) {
            $date = $monthStart->copy()->day($day);
            $calendarDays[] = [
                'date' => $date->format('Y-m-d'),
                'day' => $day,
                'is_today' => $date->isToday(),
                'events' => $calendarEvents[$date->format('Y-m-d')] ?? [],
            ];
        }
        while (count($calendarDays) % 7 !== 0) $calendarDays[] = null;

        $previousMonth = $monthStart->copy()->subMonth()->format('Y-m');
        $nextMonth = $monthStart->copy()->addMonth()->format('Y-m');
        $currentMonthLabel = $monthStart->format('F Y');

        return view('pages.client.calendar.closures', compact(
            'client', 'closures', 'areaIds', 'branchIds', 'areas', 'activityLogs',
            'calendarDays', 'previousMonth', 'nextMonth', 'currentMonthLabel',
            'overlapWarnings', 'clientBranches'
        ));
    }

    public function createClosure(Client $client)
    {
        $areas = $client->areas()->with(['branches' => function ($q) { $q->orderBy('name'); }])->orderBy('name')->get();
        return view('pages.client.calendar.closure-form', ['client' => $client, 'areas' => $areas, 'closure' => null, 'selectedAreas' => [], 'selectedBranches' => []]);
    }

    public function editClosure(Client $client, $closure)
    {
        $closure = DB::table('branch_closures')->where('client_id', $client->id)->where('id', $closure)->first();
        abort_unless($closure, 404);
        $areas = $client->areas()->with(['branches' => function ($q) { $q->orderBy('name'); }])->orderBy('name')->get();
        $selectedAreas = DB::table('branch_closure_areas')->where('branch_closure_id', $closure->id)->pluck('area_id')->all();
        $selectedBranches = DB::table('branch_closure_branches')->where('branch_closure_id', $closure->id)->pluck('branch_id')->all();
        return view('pages.client.calendar.closure-form', compact('client', 'areas', 'closure', 'selectedAreas', 'selectedBranches'));
    }

    public function storeClosure(Request $request, Client $client)
    {
        return $this->persistClosure($request, $client, null);
    }

    public function updateClosure(Request $request, Client $client, $closure)
    {
        $existing = DB::table('branch_closures')->where('client_id', $client->id)->where('id', $closure)->first();
        abort_unless($existing, 404);
        return $this->persistClosure($request, $client, $existing->id);
    }

    private function persistClosure(Request $request, Client $client, $id)
    {
        $data = $request->validate([
            'name' => 'required|string|max:255',
            'closure_type' => 'required|string|max:80',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after_or_equal:start_date',
            'assignment_mode' => ['required', Rule::in(['all', 'areas', 'branches', 'all_except'])],
            'area_ids' => 'required_if:assignment_mode,areas|array',
            'area_ids.*' => 'integer',
            'branch_ids' => 'required_if:assignment_mode,branches,all_except|array',
            'branch_ids.*' => 'integer',
            'description' => 'nullable|string|max:2000',
        ]);
        $validAreaIds = $client->areas()->pluck('id')->all();
        foreach (($data['area_ids'] ?? []) as $areaId) abort_unless(in_array((int) $areaId, $validAreaIds, true), 422, 'Selected Area does not belong to this client.');
        $validBranchIds = $client->areas()->with('branches')->get()->flatMap(function ($a) { return $a->branches->pluck('id'); })->map(function ($v) { return (int) $v; })->all();
        foreach (($data['branch_ids'] ?? []) as $branchId) abort_unless(in_array((int) $branchId, $validBranchIds, true), 422, 'Selected Branch does not belong to this client.');

        $wasExisting = (bool) $id;
        $payload = ['client_id' => $client->id, 'name' => $data['name'], 'closure_type' => $data['closure_type'], 'start_date' => $data['start_date'], 'end_date' => $data['end_date'], 'assignment_mode' => $data['assignment_mode'], 'description' => $data['description'] ?? null, 'updated_at' => now()];
        DB::transaction(function () use (&$id, $payload, $data) {
            if ($id) {
                DB::table('branch_closures')->where('id', $id)->update($payload);
            } else {
                $payload['created_at'] = now();
                $id = DB::table('branch_closures')->insertGetId($payload);
            }
            DB::table('branch_closure_areas')->where('branch_closure_id', $id)->delete();
            DB::table('branch_closure_branches')->where('branch_closure_id', $id)->delete();
            foreach (($data['area_ids'] ?? []) as $areaId) DB::table('branch_closure_areas')->insert(['branch_closure_id' => $id, 'area_id' => $areaId]);
            foreach (($data['branch_ids'] ?? []) as $branchId) DB::table('branch_closure_branches')->insert(['branch_closure_id' => $id, 'branch_id' => $branchId]);
        });
        $this->logActivity($client, $wasExisting ? 'updated' : 'created', 'closure', $id, ($wasExisting ? 'Updated' : 'Created') . ' closure: ' . $data['name'], ['assignment_mode' => $data['assignment_mode'], 'start_date' => $data['start_date'], 'end_date' => $data['end_date']]);
        return redirect()->route('clients.branch-calendar.closures', $client)->with('success', 'Closure saved successfully.');
    }

    public function destroyClosure(Client $client, $closure)
    {
        $deleted = DB::table('branch_closures')->where('client_id', $client->id)->where('id', $closure)->delete();
        abort_unless($deleted, 404);
        $this->logActivity($client, 'deleted', 'closure', $closure, 'Deleted closure #' . $closure . '.');
        return back()->with('success', 'Closure deleted.');
    }
}
