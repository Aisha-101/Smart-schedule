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
            'specialization' => 'required|string',
            'workload_factor' => 'nullable|numeric|min:0.1|max:3'
        ]);

        return Specialist::create($request->all());
    }

    public function syncFromUsers()
    {
        $users = User::where('role', 'SPECIALIST')->get();

        foreach ($users as $user) {
            Specialist::firstOrCreate(
                ['user_id' => $user->id],
                [
                    'specialization' => 'General',
                    'workload_factor' => 1.00,
                ]
            );
        }

        return response()->json([
            'message' => 'Specialists synced from users successfully',
            'count' => $users->count(),
        ]);
    }

    public function services($id)
    {
        $specialist = Specialist::with('services')
            ->findOrFail($id);

        return $specialist->services;
    }
    
}
