<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Professional;
use App\Services\AvailabilityService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class AvailabilityController extends Controller
{
    public function __construct(private AvailabilityService $availability) {}

    public function slots(Professional $professional, Request $request): JsonResponse
    {
        $request->validate(['date' => 'required|date']);

        $date = \Carbon\Carbon::parse($request->date);
        $slots = $this->availability->getAvailableSlots($professional, $date);

        return response()->json([
            'professional_id' => $professional->id,
            'date' => $date->format('Y-m-d'),
            'slots' => $slots,
        ]);
    }

    public function dates(Professional $professional): JsonResponse
    {
        $dates = $this->availability->getAvailableDates($professional, 45);

        return response()->json([
            'professional_id' => $professional->id,
            'dates' => $dates,
        ]);
    }
}
