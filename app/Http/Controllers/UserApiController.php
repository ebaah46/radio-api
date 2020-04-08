<?php

namespace App\Http\Controllers;

use App\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Laravel\Passport\Passport;
use Illuminate\Support\Facades\Validator;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;

class UserApiController extends Controller
{
    // Create User API controller functions

    /*
     *
     * Register Function for API
     *
     */
    public function register(Request $request){
        $validator = Validator::make($request->all(),[
            'name'=>'required',
            'email'=>'required|email',
            'password'=>'required'
        ]);
        if ($validator->fails()){
            return response()->json([
                'msg'=>'Registration Failed',
                'error'=>$validator->errors(),
            ],400);
        }
        $inputs = $request->all();
        $inputs['password']= bcrypt($inputs['password']);
        $user = User::create($inputs);
        $user->assignRole('client');
        $token = $user->createToken('radio')->accessToken;
        return response()->json([
            'msg'=>'Registration Successful',
            'token'=>$token
        ],201);
    }

    /*
     *
     * Login Function for API
     *
     */
    public  function login(){
    // Authenticate user
        if(Auth::attempt(['email'=>request('email'),'password'=>request('password')])){
            $user = Auth::user();
            $token = $user->createToken('radio')->accessToken;
            return response()->json([
                'msg'=>'Login Successful',
                'token'=>$token
            ],200);
        }else{
            return response()->json([
                'msg'=>'Unauthorized'
            ],401);
        }
    }
}
