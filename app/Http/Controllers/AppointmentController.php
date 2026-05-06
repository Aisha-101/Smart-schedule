<?php

namespace App\Http\Controllers;
use App\Models\Appointment;
use Illuminate\Http\Request;
use Carbon\Carbon;

class AppointmentController extends Controller
{
    public function index()
    {
        return Appointment::with(['services','client','specialist'])->get();
    }
    public function store(Request $request)
    {
        $request->validate([
            'specialist_id' => 'required|exists:users,id',
            'services' => 'required|array',
            'services.*' => 'exists:services,id',
            'start_time' => 'required|date',
            'end_time' => 'required|date|after:start_time'
        ]);

        $clientId = auth()->id();

       $overlap = Appointment::where('specialist_id', $request->specialist_id)
            ->where('status', '!=', 'CANCELED')
            ->where(function ($query) use ($request){
                $query->where('start_time', '<', $request->end_time)
                    ->where('end_time', '>', $request->start_time);
            })
            ->exists();

        if($overlap){
            return response()->json([
                'message' => 'Time slot is already taken'
            ], 400);
        }

        $appointment = Appointment::create([
            'client_id' => $clientId,
            'specialist_id' => $request->specialist_id,
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
            'status' => 'SCHEDULED'
        ]);
        $appointment->services()->attach($request->services);

        return $appointment->load('services');
    }

    public function update(Request $request, $id)
    {
        $appointment = Appointment::findOrFail($id);

        $request->validate([
            'start_time' => 'sometimes|required|date',
            'end_time' => 'sometimes|required|date|after:start_time',
            'status' => 'sometimes|required|in:SCHEDULED,COMPLETED,CANCELED,NO_SHOW,LATE',
            'services' => 'sometimes|array',
            'services.*' => 'exists:services,id'
        ]);

        if ($request->has('start_time') && $request->has('end_time')) {

        $specialistOverlap = Appointment::where('specialist_id', $appointment->specialist_id)
            ->where('id', '!=', $appointment->id)
            ->where('status', '!=', 'CANCELED')
            ->where(function ($query) use ($request) {
                $query->where('start_time', '<', $request->end_time)
                    ->where('end_time', '>', $request->start_time);
            })
            ->exists();

        if ($specialistOverlap) {
            return response()->json([
                'message' => 'This time slot is already taken by the specialist'
            ], 400);
        }

        $clientOverlap = Appointment::where('client_id', $appointment->client_id)
            ->where('id', '!=', $appointment->id)
            ->where('status', '!=', 'CANCELED')
            ->where(function ($query) use ($request) {
                $query->where('start_time', '<', $request->end_time)
                    ->where('end_time', '>', $request->start_time);
            })
            ->exists();

        if ($clientOverlap) {
            return response()->json([
                'message' => 'Client already has another appointment at this time'
            ], 400);
        }

        $appointment->update([
            'start_time' => $request->start_time,
            'end_time' => $request->end_time,
        ]);

        if ($request->has('status')) {
            $appointment->update([
                'status' => $request->status,
            ]);
        }

        if ($request->has('services')) {
            $appointment->services()->sync($request->services);
        }

        return $appointment->load(['services','client','specialist']);
    }

    if ($request->has('status')) {
        $appointment->update([
            'status' => $request->status,
        ]);
    }

    if ($request->has('services')) {
        $appointment->services()->sync($request->services);
    }


    }

    //Cancel Appointment
    public function destroy($id)
    {
        $appointment = Appointment::findOrFail($id);

        $appointment->update(
            ['status' => 'CANCELED']
        );
        return response()->json(['message' => 'Appointment canceled successfully']);
    }
    public function my()
    {
        $user = auth()->user();

        if($user->role === 'CLIENT'){
            return Appointment::with([
                'services',
                'specialist'
            ])
            ->where('client_id', $user->id)
            ->get();
        }

        if($user->role === 'SPECIALIST'){
            return Appointment::with([
                'services',
                'client'
            ])
            ->where('specialist_id', $user->id)
            ->get();
        }

        return response()->json([]);
    }

    public function updateStatus(Request $request, $id)
    {
        $request->validate([
            'status' => 'required|in:SCHEDULED,COMPLETED,CANCELED,NO_SHOW,LATE',
            'delay_minutes' => 'nullable|integer|min:0',
        ]);

        $appointment = Appointment::findOrFail($id);

        $delayMinutes = 0;
        if ($request->status === 'LATE') {
            $startTime = Carbon::parse($appointment->start_time);
            $now = Carbon::now();

            $delayMinutes = $now->greaterThan($startTime)
                 ? $startTime->diffInMinutes($now) 
                 : 0;
        }
        $appointment->update([
            'status' => $request->status,
            'delay_minutes' => $delayMinutes,
        ]);

        return response()->json([
            'message' => 'Appointment status updated successfully',
            'appointment' => $appointment->load(['client', 'services'])
        ]);
    }
}
