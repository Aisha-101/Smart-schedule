<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class AdminUserController extends Controller
{
    public function index()
    {
        return User::with(['specialist:id,user_id,specialization,workload_factor'])
            ->select('id', 'name', 'email', 'role', 'email_verified_at', 'created_at', 'updated_at')
            ->orderBy('created_at', 'desc')
            ->get();
    }

    public function show($id)
    {
        return User::with(['specialist:id,user_id,specialization,workload_factor'])
            ->findOrFail($id);
    }

    public function destroy($id)
    {
        $user = User::findOrFail($id);
        $user->delete();

        return response()->json(['message' => 'User deleted successfully']);
    }
}