<?php

namespace App\Http\Controllers;

use App\Services\RecommendationService;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function get(Request $request, RecommendationService $service)
    {
        $request->validate([
            'specialist_id' => 'required|integer',
            'date' => 'required|date',
            'service_id' => 'required|exists:services,id'
        ]);

        $data = $service->getRecommendedTimes(
            auth()->id(),
            $request->specialist_id,
            $request->date,
            $request->service_id
        );

        return response()->json($data);
    }
}