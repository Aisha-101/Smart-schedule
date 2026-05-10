<?php

namespace App\Http\Controllers;

use App\Services\RecommendationService;
use Illuminate\Http\Request;

class RecommendationController extends Controller
{
    public function get(Request $request, RecommendationService $service)
    {
        $request->validate([
            'specialist_id' => 'required|integer|exists:users,id',
            'date' => 'required|date',
            'service_id' => 'nullable|exists:services,id',
            'service_ids' => 'nullable|array|min:1',
            'service_ids.*' => 'exists:services,id',
        ]);

        if ($request->date < now()->toDateString()) {
            return response()->json([
                'message' => 'Cannot recommend times in the past.'
            ], 422);
        }

        $serviceIdsInput = $request->input('service_ids', null);

        if (is_array($serviceIdsInput) && ! empty($serviceIdsInput)) {
            $serviceIds = $serviceIdsInput;
        } elseif ($request->filled('service_id')) {
            $serviceIds = [$request->input('service_id')];
        } else {
            $serviceIds = [];
        }
        return response()->json(
            $service->getRecommendedTimes(
                auth()->id(),
                $request->specialist_id,
                $request->date,
                $request->service_ids
            )
        );
    }
}