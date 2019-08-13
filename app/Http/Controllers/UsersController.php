<?php

namespace App\Http\Controllers;

class UsersController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
   // public function __construct()
    //{
        //
    //}

    //
    
    public function index()
    {
        $users=app('db')->table('users')->get();
        
        return response()->json($users);
    }
    
    public function create(\Illuminate\Http\Request $request)
    {
         try{
            $this->validate($request,[
                'username' =>'required',
                'email'   => 'required|email',
                'password'=> 'required|min:6',
            ]);
        }catch (ValidationException $e){
            
            return response()->json([
                'success' =>false,
                'message' =>$e->getMessage(),
            ],422);      
        }
        
    try{
                $id=app('db')->table('users')->insertGetId([
                'username'=> strtolower(trim($request->input('username'))),
                'email'=> strtolower(trim($request->input('email'))),
                'password'=>app('hash')->make($request->input('password')),
                //'created_at'=> \Carbon\Carbon::now,
                //'updated_at'=> \Carbon\Carbon::now,
                    ///ai duita te shomossa aasey
            ]);
            
            $user=app('db')->table('users')->select('username','email')->where('id',$id)->first();    
                
            return response()->json([
                'id'=>$id,
                'username' => $user->username,
                'email' => $user->email,
            ],201);
        } catch (PDOException $e) {
            return response()->json([
                'success'=>false,
                'message'=>$e->getMessage(),
            ],400);

        }
    }
    
    public function authenticate(\Illuminate\Http\Request $request)
    {
        try{
            $this->validate($request,[
                
                'email'   => 'required|email',
                'password'=> 'required|min:6',
            ]);
        }catch (ValidationException $e){
            
            return response()->json([
                'success' =>false,
                'message' =>$e->getMessage(),
            ],422);      
        }
        
            $token=app('auth')->attempt($request->only('email','password'));
            
            
            if($token)
            {
                 return response()->json([
                'success' =>true,
                'message' =>'user authenticate',
                'token'=>$token,
            ],201);
            }
            
            return response()->json([
                'success' =>false,
                'message' =>'invalid credential',
                
            ],402);
    }
}
