<?php

namespace App\Http\Controllers;

namespace App\Http\Controllers;

use App\Models\Incident;
use App\Models\User;
use App\Models\Branch;
use App\Models\Area;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\IncidentAttachment;
use Illuminate\Support\Facades\Storage;
use Yajra\DataTables\Facades\DataTables;
use Maatwebsite\Excel\Facades\Excel;
use App\Exports\IncidentExport;
use Barryvdh\DomPDF\Facade\Pdf;


use Carbon\Carbon;
use Illuminate\Support\Facades\DB;

class IncidentController extends Controller
{
    public function index(Request $request)
    {
        $query = Incident::with([
            'user',
            'branch',
            'area',
            'reporter'
        ]);


        if($request->filled('category')){
            $query->where('category',$request->category);
        }

        if($request->filled('status')){
            $query->where('status',$request->status);
        }

        if($request->filled('user')){
            $query->where('user_id',$request->user_id);
        }

        if($request->filled('branch')){
            $query->where('branch_id',$request->branch);
        }

        if($request->filled('area')){
            $query->where('area_id',$request->area);
        }

        if($request->filled('from')){
            $query->whereDate('incident_date','>=',$request->from);
        }

        if($request->filled('to')){
            $query->whereDate('incident_date','<=',$request->to);
        }

        $incidents = $query
            ->latest('incident_date')
            ->paginate(20);

        return view('pages.incidents.index',[
            'incidents'=>$incidents,
            'employees'=>User::orderBy('lastname')->get(),
            'branches'=>Branch::orderBy('name')->get(),
            'areas'=>Area::orderBy('name')->get()
        ]);
    }

    public function create()
    {
        return view('pages.incidents.create',[
            'employees'=>User::orderBy('lastname')->get(),
            'branches'=>Branch::orderBy('name')->get(),
            'areas'=>Area::orderBy('name')->get()
        ]);
    }

    public function store(Request $request)
    {
        $validated = $request->validate([
            'user_id'=>'required',
            'branch_id'=>'nullable',
            'area_id'=>'nullable',
            'category'=>'required',
            'incident_type'=>'required',
            'incident_date'=>'required|date',
            'incident_time'=>'nullable',
            'location'=>'required',
            'description'=>'required',
            'action_taken' => 'nullable',
            'recommendation' => 'nullable',
            'attachments.*' =>'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,xls,mp4,mov|max:10240',
        ]);

        $validated['reported_by'] = Auth::id();

        $incident = Incident::create($validated);

        if($request->hasFile('attachments')){

            foreach($request->file('attachments') as $file){

                $stored = $file->store(
                    'incident_attachments',
                    'public'
                );

                $incident->attachments()->create([

                    'original_name' => $file->getClientOriginalName(),

                    'file_name' => $stored,

                    'mime_type' => $file->getMimeType(),

                    'file_size' => $file->getSize()

                ]);

            }

        }

        return redirect()
            ->route('incidents.index')
            ->with('success','Incident report created successfully.');
    }

    public function show(Incident $incident)
    {
        $incident->load([
            'user',
            'branch',
            'area',
            'reporter',
            'investigator',
            'attachments'

        ]);

        return view('pages.incidents.show', compact('incident'));
    }

    public function edit(Incident $incident)
    {
        return view('pages.incidents.edit',[
            'incident'=>$incident,
            'employees'=>user::orderBy('lastname')->get(),
            'branches'=>Branch::orderBy('name')->get(),
            'areas'=>Area::orderBy('name')->get()
        ]);
    }

    public function update(Request $request, Incident $incident)
    {
        $validated = $request->validate([
            'user_id'=>'required',
            'branch_id'=>'nullable',
            'area_id'=>'nullable',
            'category'=>'required',
            'incident_type'=>'required',
            'incident_date'=>'required|date',
            'incident_time'=>'nullable',
            'location'=>'required',
            'description'=>'required',
            'action_taken' => 'nullable',
            'recommendation' => 'nullable',
            'attachments.*' =>'nullable|file|mimes:jpg,jpeg,png,pdf,doc,docx,xlsx,xls,mp4,mov|max:10240',
        ]);

        $incident->update($validated);

        if($request->hasFile('attachments')){

            foreach($request->file('attachments') as $file){

                $stored = $file->store(
                    'incident_attachments',
                    'public'
                );

                $incident->attachments()->create([

                    'original_name'=>$file->getClientOriginalName(),

                    'file_name'=>$stored,

                    'mime_type'=>$file->getMimeType(),

                    'file_size'=>$file->getSize()

                ]);

            }

        }

        return redirect()
            ->route('incidents.index')
            ->with('success','Incident updated successfully.');
    }

    public function destroy(Incident $incident)
    {
        $incident->delete();

        return redirect()
            ->route('incidents.index')
            ->with('success','Incident deleted.');
    }

    public function employeeInformation(User $user)
    {
        $user->load([
            'branch',
            'area'
        ]);

        $branch['id'] = null;
        $branch['name'] = null;
        $area['id'] = null;
        $area['name'] = null;


        if($user->branch_id) {
            $branch['id'] = $user->branch->id;
            $branch['name'] = $user->branch->name;
        }

        if($user->area_id == null && $user->branch_id != null ){
            $area = Area::where('id', $user->branch->area_id)->first();
        }

        return response()->json([
            'branch_id'   => optional($branch)['id'],
            'branch_name' => optional($branch)['name'],
            'area_id'     => optional($area)['id'],
            'area_name'   => optional($area)['name'],
        ]);
    }

    public function print(Incident $incident)
    {
        $incident->load([
            'user',
            'branch',
            'area',
            'reporter',
            'investigator'
        ]);

        return view('pages.incidents.print', compact('incident'));
    }

    public function deleteAttachment(
        IncidentAttachment $attachment
    ){

        Storage::disk('public')
            ->delete($attachment->file_name);

        $attachment->delete();

        return back()->with(
            'success',
            'Attachment removed.'
        );

    }

    public function dashboard(Request $request)
    {
        $year = $request->year ?? now()->year;

        $major = Incident::whereYear('incident_date', $year)
            ->where('category', 'Major')
            ->count();

        $minor = Incident::whereYear('incident_date', $year)
            ->where('category', 'Minor')
            ->count();

        $open = Incident::where('status', 'Open')->count();

        $investigating = Incident::where(
            'status',
            'Under Investigation'
        )->count();

        $resolved = Incident::where('status', 'Resolved')->count();

        $closed = Incident::where('status', 'Closed')->count();

        $monthly = Incident::selectRaw("
            MONTH(incident_date) month,
            COUNT(*) total
        ")
            ->whereYear('incident_date', $year)
            ->groupBy(DB::raw("MONTH(incident_date)"))
            ->pluck('total', 'month');

        $recent = Incident::with([
            'user',
            'branch'
        ])
            ->latest('incident_date')
            ->take(10)
            ->get();

        $topEmployees = Incident::select(
            'user_id',
            DB::raw('COUNT(*) as total')
        )
            ->with('user')
            ->groupBy('user_id')
            ->orderByDesc('total')
            ->take(10)
            ->get();

        return view(
            'pages.incidents.dashboard',
            compact(
                'year',
                'major',
                'minor',
                'open',
                'investigating',
                'resolved',
                'closed',
                'monthly',
                'recent',
                'topEmployees'
            )
        );
    }

    public function datatable(Request $request)
    {
        $query = Incident::with([
            'user',
            'branch',
            'area'
        ]);

        if($request->user){

            $query->where(
                'user_id',
                $request->user
            );

        }

        if($request->branch){

            $query->where(
                'branch_id',
                $request->branch
            );

        }

        if($request->area){

            $query->where(
                'area_id',
                $request->area
            );

        }

        if($request->category){

            $query->where(
                'category',
                $request->category
            );

        }

        if($request->status){

            $query->where(
                'status',
                $request->status
            );

        }

        if($request->from){

            $query->whereDate(
                'incident_date',
                '>=',
                $request->from
            );

        }

        if($request->to){

            $query->whereDate(
                'incident_date',
                '<=',
                $request->to
            );

        }

        return DataTables::eloquent($query)

            ->addColumn('guard',function($row){

                return optional(
                    $row->user
                )->fullname;

            })

            ->addColumn('branch',function($row){

                return optional(
                    $row->branch
                )->name;

            })

            ->addColumn('area',function($row){

                return optional(
                    $row->area
                )->name;

            })

            ->addColumn('action',function($row){

                return view(
                    'pages.incidents.partials.action',
                    compact('row')
                );

            })

            ->rawColumns([
                'action'
            ])

            ->make(true);
    }

    public function excel()
    {
        return Excel::download(

            new IncidentExport,

            'IncidentReport.xlsx'

        );
    }

    public function pdf()
    {
        $incidents = Incident::with([
            'user',
            'branch',
            'area'
        ])->get();

        $pdf = PDF::loadView(

            'pages.incidents.partials.pdfs.pdf',

            compact('incidents')

        );

        return $pdf->download(

            'IncidentReport.pdf'

        );
    }

}
