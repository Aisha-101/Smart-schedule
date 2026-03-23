<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Auth;

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
        if (!Auth::attempt($request->only('email','password'))){
            return response()->json(['message'=>'Invalid credentials'],401);
        }

        return response()->json(Auth::user());
    }
}
