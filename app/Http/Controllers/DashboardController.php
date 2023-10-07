<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use App\Models\Client;
use App\Models\ConsumptionProfile;
use App\Models\Mapping;
use App\Models\Project;
use App\Models\SourceProfile;
use App\Models\User;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

// use App\Models\Client;

class DashboardController extends Controller
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
     * Show the application dashboard.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index(Request $request)
    {
        $id = base64_decode($request['id']);
        if (Session::has('client_detail')) {
            Session::forget('client_detail');
        }
        $client_detail = Client::find($id);
        Session::put('client_detail', $client_detail);

        $sessionId = Session::get('client_detail');
        // $current_client = Session::get('client_detail');


        // $project_list = Project::where('client_id', $current_client->id)->get();
        $client_list = Client::get();
        $consumption_list = ConsumptionProfile::with('state','voltage')->where('client_id',$client_detail->id)->latest()->take(5)->get();
        $source_list = SourceProfile::with('contract','voltage')->where('client_id',$client_detail->id)->latest()->take(5)->get();
        $mapping_list = Mapping::with('consumption_profile','source_profile')->where('client_id',$client_detail->id)->latest()->take(5)->get();
        $project_list = Project::where('client_id',$client_detail->id)->latest()->take(5)->get();
        return view('dashboard', ['sessionId'=> $sessionId,'consumption_list'=>$consumption_list,'source_list' => $source_list,'current_client' => $sessionId, 'client_list' => $client_list,'mapping_list'=>$mapping_list,'project_list'=>$project_list]);
    }
    /**
     * listing of consumptionprofile and source profile display in dashboard
     */
    public function getList(Request $request)
    {
        $consumption_list = ConsumptionProfile::with('state')->sortByDesc('created_at');
        $source_list = SourceProfile::all()->sortByDesc('created_at');
        return view('dashboard',compact($consumption_list,$source_list));
    }

    public function changePassword(Request $request)
    {
        $current_client = Session::get('client_detail');
        $client_list = Client::get();
        $showLeftSidebar = true;
        $user = Auth::user();
        return view('auth.passwords.reset',compact('user','current_client','client_list','showLeftSidebar'));
    }
    public function updatePassword(Request $request)
    {
        $user = Auth::user();
        $plainPassword = $request->password;
        $user = User::where('email', $user->email)->first();
        if($user && Hash::check($plainPassword, $user->password))
        {
            $user->password = bcrypt($request->updatepassword);
            $user->update();
            return redirect()->back()->with('success', __('Password update successfully.'));
        }
        else
        {
            return redirect()->back()->with('error', __('Something Went wrong'));
        }

    }
}
