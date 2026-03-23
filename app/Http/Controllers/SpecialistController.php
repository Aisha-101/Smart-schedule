<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Specialist;

class SpecialistController extends Controller
{
    public function store(Request $request)
    {
        $request->validate([
            'user_id' => 'required|exists:users,id',
            'specialization' => 'required|string'
        ]);

        return Specialist::create($request->all());
    }
}
