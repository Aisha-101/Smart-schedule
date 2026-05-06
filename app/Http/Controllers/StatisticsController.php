<?php

namespace App\Http\Controllers;

use App\Models\Appointment;

class StatisticsController extends Controller
{
    public function index()
    {
        $total = Appointment::count();

        $canceled = Appointment::where('status', 'CANCELED')->count();
        $noShows = Appointment::where('status', 'NO_SHOW')->count();

        $averageDelay = Appointment::whereNotNull('delay_minutes')
            ->avg('delay_minutes');

        return response()->json([
            'total_appointments' => $total,
            'canceled_percentage' => $total > 0 ? round(($canceled / $total) * 100, 2) : 0,
            'no_show_percentage' => $total > 0 ? round(($noShows / $total) * 100, 2) : 0,
            'average_delay_minutes' => round($averageDelay ?? 0, 2),
        ]);
    }
}