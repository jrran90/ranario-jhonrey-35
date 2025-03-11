<?php

namespace App\Http\Controllers;

use App\Models\StoreHour;
use App\Services\StoreService;
use Illuminate\Http\Request;
use Illuminate\Http\JsonResponse;

class StoreHourController extends Controller
{
    protected StoreService $storeService;

    public function __construct(StoreService $storeService)
    {
        $this->storeService = $storeService;
    }

    public function getStoreStatus(): JsonResponse
    {
        return response()->json($this->storeService->getStoreStatus());
    }

    public function getNextOpening(Request $request): JsonResponse
    {
        $date = $request->query('date');
        return response()->json($this->storeService->checkIfOpen($date));
    }

    public function updateStoreHours(Request $request, StoreHour $storeHour): JsonResponse
    {
        $data = $request->validate([
            'day_of_week' => 'required|integer|min:0|max:6',
            'open_time' => 'required|date_format:H:i:s',
            'close_time' => 'required|date_format:H:i:s',
            'is_open' => 'boolean',
            'is_alternate_saturday' => 'boolean',
        ]);

        $storeHour->update($data);
        return response()->json(['message' => 'Store hours updated successfully']);
    }
}
