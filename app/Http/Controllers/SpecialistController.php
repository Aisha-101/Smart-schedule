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
            ->with(['specialist:id,user_id,specialization,workload_factor'])
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
    
     public function update(Request $request, $id)
    {
        $specialist = Specialist::findOrFail($id);

        $request->validate([
            'specialization' => 'sometimes|required|string',
            'workload_factor' => 'sometimes|required|numeric|min:0.1|max:3'
        ]);

        $specialist->update($request->only(['specialization', 'workload_factor']));

        return response()->json($specialist->fresh(), 200);
    }

    public function destroy($id)
    {
        $specialist = Specialist::findOrFail($id);
        $specialist->delete();

        return response()->json(['message' => 'Specialist deleted successfully']);
    }
}
