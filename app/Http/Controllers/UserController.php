<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use File;
use Illuminate\Support\Str;


class UserController extends Controller
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
     * Show the user list.
     *
     * @return \Illuminate\Contracts\Support\Renderable
     */
    public function index()
    {
       
        $user = User::all();

        return view('user.index', ['user' => $user]);
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function create()
    {
        return view('user.create');
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
            'name' => ['required', 'string', 'max:255'],
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
            'password' => ['required', 'string', 'min:8', 'confirmed'],
            'photo' => 'required|image|mimes:jpeg,png,jpg',
        ]);
   

        $uniq_id = Str::random(5);
        $imageName = time().'.'.$request['photo']->extension();  

        $path = public_path().'/images/user/' . $uniq_id;
        File::makeDirectory($path, $mode = 0777, true, true);
       


        $request['photo']->move($path, $imageName);
        
        User::create([
            'name' => $request['name'],
            'email' => $request['email'],
            'phone' => $request['phone'],
            'address' => $request['address'],
            'password' => Hash::make($request['password']),
            'position' => $request['position'],
            'permission' => isset($request['permissions']) && !empty($request['permissions']) ? $request['permissions'] : 0,
            'uniq_id' => $uniq_id,
            'image' => "images/user/" . $uniq_id . "/".$imageName,
        ]);

        return redirect()->route('user.index')->with('success',__('User added successfuly.'));
            
    }

    /**
     * Display the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function show($id)
    {
        $user_detail = User::find($id);
        return view('user.view', ['user_details' => $user_detail]);
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function edit($id)
    {
        $user_data = User::find($id);
        
        return view('user.edit', ['user_details' => $user_data]);
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function update(Request $request, $id)
    {
        
        $request->validate([
            'name' => 'required|string|max:255',
            'email' => 'required|email|unique:users,email,'.$id,
            'photo' => 'required_if:photo, not null|image|mimes:jpeg,png,jpg',
        ]);
        
        $user = User::find($id);  

        if(isset($request['photo']) && !empty($request['photo']))
        {
            $imageName = time().'.'.$request['photo']->extension();  
            $path = public_path().'/images/user/' . $user->uniq_id;
            $request['photo']->move($path, $imageName);
        }

             
        $user->name = $request['name'];
        $user->email = $request['email'];
        $user->phone = $request['phone'];
        $user->address = $request['address'];
        $user->position = $request['position'];
        $user->permission = isset($request['permissions']) && !empty($request['permissions']) ? $request['permissions'] : 0;
        if(isset($request['photo']) && !empty($request['photo']))
        {
            $user->image = "images/user/" . $user->uniq_id . "/" . $imageName;
        }

        $user->save();

        

        return redirect()->route('user.index')->with('success',__('User updated successfuly.'));
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  \App\Models\User  $user
     * @return \Illuminate\Http\Response
     */
    public function destroy($id)
    {
        
        User::find($id)->delete();
  
        return redirect()->back();
    }

    /**
     * restore all user
     *
     * @return response()
     */
    public function restoreAll()
    {
        // restore all user
        User::onlyTrashed()->restore();
        // restore specific user
        Post::withTrashed()->find($id)->restore();
  
        return redirect()->back();
    }
}
