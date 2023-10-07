<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
// use App\Models\Project;
use App\Models\Client;
use App\Models\Industry;
use App\Models\ComissionUnit;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Session;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\ImportClientMetadata;

use File;

class ClientController extends Controller
{


    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    /**
     * Show the client list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
        // $id = base64_decode($request['id']);
        // $showclient = true;

        // if (Session::has('client_detail')) {
        //     Session::forget('client_detail');
        // }
        // $client_detail = Client::find($id);
        // Session::put('client_detail', $client_detail);

        // $client_id = Session::get('client_detail');
        // $project = Project::where('client_id', $client_id->id)->get();
        $client_list = Client::all()->sortByDesc('created_at');

        $showLeftSidebar = true;

        return view('client.index',  [ 'client_list' => $client_list, 'showLeftSidebar' => $showLeftSidebar ]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {

        $industry = Industry::all();

        $comission_unit = ComissionUnit::all();
        $client_list = Client::all();
        $session_id = Session::get('client_detail');
        $showLeftSidebar = true;
        $client_id = isset($session_id) && !empty($session_id) ? $session_id : $client_list[0];

        return view('client.create', ['current_client' => $client_id, 'client_list' => $client_list, 'industry' => $industry, 'comission_unit' => $comission_unit, 'showLeftSidebar' => $showLeftSidebar]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {

        $request->validate([
            'client_name' => ['required', 'string', 'max:255'],
            'person_name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:clients'],
            // 'is_metadata' => 'required|mimes:csv,xlsx,xls',
        ]);


        if(isset($request['is_metadata']) && !empty($request['is_metadata'])){
            $meta_data = 'meta_data_' .time().'.'.$request['is_metadata']->extension();
            $path = public_path().'/documents/client';
            File::makeDirectory($path, $mode = 0777, true, true);
            $request['is_metadata']->move($path, $meta_data);
        }

        $client = Client::create([
            'client_name' => $request['client_name'],
            'parent_group' => $request['parent_group'],
            'person_name' => $request['person_name'],
            'email' => $request['email'],
            'country_code' => $request['country_code'],
            'phone' => $request['phone'],
            'designation' => $request['designation'],
            'lead_type' => $request['lead_type'],
            'consultant_name' => $request['consultant_name'],
            'comission_fee' => $request['comission_fee'],
            'type_of_industry' => $request['type_of_industry'],
            'consumption_point_no' => $request['consumption_point_no'],
            'source_point_no' => $request['source_point_no'],
            'is_metadata' => isset($request['is_metadata']) && !empty($request['is_metadata']) ? "documents/client/".$meta_data : NULL
        ]);

        if(isset($request['is_metadata']) && !empty($request['is_metadata'])){
            Excel::import(new ImportClientMetadata($client), $path."/". $meta_data);
        }


        return redirect()->route('dashboard', ['id' => base64_encode($client->id)])->with('success', 'Client added successfully.');

    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $client_detail = client::find($id);
        return view('client.view', ['client_details' => $client_detail]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $client_data = client::find($id);
        $client_list = Client::all();
        $industry = Industry::all();
        $comission_unit = ComissionUnit::all();
        $sessionId = Session::get('client_detail');

        return view('client.edit', ['comission_unit' => $comission_unit, 'industry' => $industry, 'client_details' => $client_data, 'current_client' => $sessionId, 'client_list' => $client_list]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\Project  $project
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {

        $request->validate([
            'client_name' => 'required|string|max:255',
            'person_name' => 'required|string|max:255',
            'email' => 'required|email|unique:clients,email,'.$id,

        ]);


        $client = Client::find($id);

        if(!empty($request['is_metadata'])){
            $meta_data = 'meta_data_' .time().'.'.$request['is_metadata']->extension();
            $path = public_path().'/documents/client';
            File::makeDirectory($path, $mode = 0777, true, true);
            $request['is_metadata']->move($path, $meta_data);
            $client->is_metadata = "documents/client/".$meta_data;
        }

        $client->client_name = $request['client_name'];
        $client->parent_group = $request['parent_group'];
        $client->person_name = $request['person_name'];
        $client->email = $request['email'];
        $client->country_code = $request['country_code'];
        $client->phone = $request['phone'];
        $client->designation = $request['designation'];
        $client->lead_type = $request['lead_type'];
        $client->consultant_name = $request['consultant_name'];
        $client->comission_fee = $request['comission_fee'];
        $client->type_of_industry = $request['type_of_industry'];
        $client->consumption_point_no = $request['consumption_point_no'];
        $client->source_point_no = $request['source_point_no'];
        $client->save();

        if(!empty($request['is_metadata'])){
            Excel::import(new ImportClientMetadata($client), $path."/". $meta_data);
        }


        return redirect()->route('dashboard', ['id' => base64_encode(Session::get('client_detail')->id)])->with('success',__('Client updated successfuly.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\Client  $client
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {

        Client::find($id)->delete();
        return redirect()->back();
    }

    /**
     * restore all client
     *
     * @return response()
     */
    public function restoreAll()
    {
        // restore all user
        Client::onlyTrashed()->restore();
        // restore specific user
        // Project::withTrashed()->find($id)->restore();

        return redirect()->back();
    }

}
