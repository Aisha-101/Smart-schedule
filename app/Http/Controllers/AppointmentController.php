<?php

namespace App\Http\Controllers;
use App\Models\Appointment;
use Illuminate\Http\Request;

class AppointmentController extends Controller
{
    public function index()
    {
        return Appointment::all();
    }
    public function store(Request $request)
    {
        $request->validate([
            'client_id'=>'required',
            'specialist_id'=>'required',
            'service_id'=>'required',
            'start_time'=>'required|date',
            'end_time'=>'required|date|after:start_time'
        ]);
        return Appointment::create([
            ...$request->all(),
            'status'=>'SCHEDULED'
        ]);
    }
}
