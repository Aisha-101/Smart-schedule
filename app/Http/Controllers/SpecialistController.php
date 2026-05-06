<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Specialist;
use App\Models\User;

class SpecialistController extends Controller
{
    public function index()
    {
        return User::where('role', 'SPECIALIST')
            ->select('id', 'name', 'email', 'role')
            ->get();
    }
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'specialization' => 'required|string'
        ]);

        return Specialist::create($request->all());
    }

    public function services($id)
    {
        $specialist = Specialist::with('services')
            ->findOrFail($id);

        return $specialist->services;
    }
    
}
