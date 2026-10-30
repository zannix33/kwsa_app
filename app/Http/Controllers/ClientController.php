<?php
namespace App\Http\Controllers;
use App\Models\Client;
use App\Models\User;
use Illuminate\Http\Request;
class ClientController extends Controller {
 public function index(){ $clients=Client::latest()->paginate(10); return view('pages.client.view',compact('clients')); }
 public function create(){ return view('pages.client.create'); }
 public function store(Request $request){ $data=$request->validate(['name'=>'required|string|max:255','active'=>'required|boolean','category'=>'required|in:company,property,individual','age_limit'=>'nullable|integer|min:0']); Client::create($data); return redirect()->route('clients.index')->with('success','Client created successfully.'); }
 public function show(Client $client){ $client->load(['companies'=>function($q){$q->orderBy('name');}]); $company=$client; $guards=User::orderBy('name')->get(); return view('pages.client.show',compact('client','company','guards')); }
 public function edit(Client $client){ $company=$client; return view('pages.client.edit',compact('client','company')); }
 public function update(Request $request, Client $client){ $data=$request->validate(['name'=>'required|string|max:255','category'=>'required|in:company,property,individual','active'=>'required|boolean','age_limit'=>'nullable|integer|min:0']); $client->update($data); return redirect()->route('clients.index')->with('success','Client updated successfully.'); }
 public function destroy(Client $client){ if($client->companies()->exists() || $client->areas()->exists()) return back()->with('error','Cannot delete a Client that has Companies or Areas.'); $client->delete(); return redirect()->route('clients.index')->with('success','Client deleted successfully.'); }
}
