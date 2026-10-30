<?php
namespace App\Http\Controllers;
use App\Models\Client;
use App\Models\Company;
use Illuminate\Http\Request;
class CompanyController extends Controller {
 public function index(Client $client){ $companies=$client->companies()->withCount('branches')->orderBy('name')->paginate(15); return view('pages.company.index',compact('client','companies')); }
 public function create(Client $client){ return view('pages.company.create',compact('client')); }
 public function store(Request $request, Client $client){ $data=$request->validate(['name'=>'required|string|max:255','active'=>'required|boolean']); $client->companies()->create($data); return redirect()->route('clients.companies.index',$client)->with('success','Company created successfully.'); }
 public function edit(Client $client, Company $company){ abort_unless($company->client_id===$client->id,404); return view('pages.company.edit',compact('client','company')); }
 public function update(Request $request, Client $client, Company $company){ abort_unless($company->client_id===$client->id,404); $data=$request->validate(['name'=>'required|string|max:255','active'=>'required|boolean']); $company->update($data); return redirect()->route('clients.companies.index',$client)->with('success','Company updated successfully.'); }
 public function destroy(Client $client, Company $company){ abort_unless($company->client_id===$client->id,404); if($company->branches()->exists()) return back()->with('error','Cannot delete a Company while Branches are assigned to it. Reassign those Branches first.'); $company->delete(); return back()->with('success','Company deleted successfully.'); }
}
