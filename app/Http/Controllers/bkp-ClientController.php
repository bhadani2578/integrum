<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Client;
use Illuminate\Support\Facades\Session;

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
    public function index(Request $request)
    {
        $showLeftSidebar = true; 
        
        $client = Client::all();
        
        return view('client.index',  ['showLeftSidebar' => $showLeftSidebar, 'client' => $client]);
    }

    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function store(Request $request)
    {


        $client = Client::create([
            'client_name' => $request['client_name'],
            'parent_group' => $request['parent_group'],
            'email' => $request['email'],
            'phone' => $request['phone']           
        ]);


        if (Session::has('client_detail')) {            
            Session::forget('client_detail');
        }
        Session::put('client_detail', $client);

        return redirect()->route('client.index')->with('success',__('Client added successfuly.'));

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
}
