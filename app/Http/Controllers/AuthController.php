<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Traits\Api_response;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Hash;
use App\Http\Controllers\ApiController;
use Illuminate\Support\Facades\Validator;

class AuthController extends ApiController
{

   public function register(Request $req)  {
   // dd($req->all());

    $validate=Validator::make($req->all(),[
        'name'=>'required|string',
        'email'=>'required|email|unique:users,email',
        'password'=>'required|string',
        'c_password'=>'required|same:password',
        'address'=>'required',
        'cellphone'=>'required',
        'postal_code'=>'required',
        'province_id'=>'required',
        'city_id'=>'required',

    ]);
    if($validate->fails()){
       return  $this->errorRes($validate->messages(),422);
    }
    DB::beginTransaction();
    $user=User::create([
        'name'=>$req->name,
        'email'=>$req->email,
        'address'=>$req->address,
        'password'=>Hash::make($req->password),
        'cellphone'=>$req->cellphone,
        'postal_code'=>$req->postal_code,
        'province_id'=>$req->province_id,
        'city_id'=>$req->city_id,
    ]);
    $token=$user->createToken('apiStore')->plainTextToken;
    DB::commit();
    return $this->successRes(['user'=>$user,'token'=>$token]);
   }
   function login(Request $req) {
    $validate=Validator::make($req->all(),[
        'email'=>'required|exists:users,email',
        'password'=>'required'
    ]);
    if ($validate->fails()){
        return $this->errorRes($validate->messages());
    }
    $user=User::where('email',$req->email)->first();

    if (!$user or !Hash::check($req->password,$user->password)) {
        return $this->errorRes(['error'=>'اطلاعات وارد شده اشتباه است']);
    }
    $token=$user->createToken('apiStore')->plainTextToken;
    return $this->successRes(['user'=>$user,'token'=>$token]);


   }

   public function logout() {
        auth()->user()->tokens()->delete();
        return $this->successRes("you are logged out");
   }
}
