<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    public function register(Request $request)
    {
        $request->validate([
            'name'=>'required|string',
            'email'=>'required|email|unique:users',
            'password'=>'required|min:6|confirmed'
        ]);
        $user = User::create([
            'name'=>$request->name,
            'email'=>$request->email,
            'password'=>Hash::make($request->password),
            'role'=>'CLIENT'
        ]);
        return response()->json($user);
    }
    public function login(Request $request)
    {
       $credentials = $request->only ('email', 'password');

       if(!$token = auth()->attempt($credentials)){
           return response()->json(['error'=>'Invalid credentials'], 401);
       }

       return response()->json([
           'token'=>$token,
           'user'=>auth()->user()
       ]);
    }
}
