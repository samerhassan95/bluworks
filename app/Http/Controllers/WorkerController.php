<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Worker;
use App\Models\ClockIn;
use Illuminate\Support\Facades\Validator;
use App\Services\DistanceService;
use OpenApi\Annotations as OA;

/**
 * @OA\Info(title="Worker API", version="1.0")
 */
class WorkerController extends Controller
{
    protected $distanceService;

    public function __construct(DistanceService $distanceService)
    {
        $this->distanceService = $distanceService;
    }
    /**
     * @OA\Post(
     *     path="/api/clock-in",
     *     summary="Clock in a worker",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="worker_id", type="integer", example=1),
     *             @OA\Property(property="timestamp", type="integer", example=1621234567),
     *             @OA\Property(property="latitude", type="number", format="float", example=-34.615662),
     *             @OA\Property(property="longitude", type="number", format="float", example=-58.362512)
     *         )
     *     ),
     *     @OA\Response(response=200, description="Clock-in recorded successfully"),
     *     @OA\Response(response=400, description="Location not within 2km radius"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function clockIn(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'worker_id' => 'required|exists:workers,id',
            'timestamp' => 'required|integer',
            'latitude' => 'required|numeric',
            'longitude' => 'required|numeric',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        // Check if location is within 2km
        $latitude = $request->input('latitude');
        $longitude = $request->input('longitude');
        $location = DistanceService::LOCATION;
        $distance = $this->distanceService->calculateDistance($latitude, $longitude, $location[0], $location[1]);
        if ($distance > 2) {
            return response()->json(['error' => 'Location not within 2km radius.'], 400);
        }

        // Create ClockIn record
        ClockIn::create([
            'worker_id' => $request->input('worker_id'),
            'timestamp' => date('Y-m-d H:i:s', $request->input('timestamp')),
            'latitude' => $latitude,
            'longitude' => $longitude,
        ]);

        return response()->json(['message' => 'Clock-in recorded successfully.'], 200);
    }

    /**
     * @OA\Get(
     *     path="/api/clock-ins",
     *     summary="Get list of clock-ins for a specific worker",
     *     @OA\Parameter(
     *         name="worker_id",
     *         in="query",
     *         required=true,
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(response=200, description="Successful response"),
     *     @OA\Response(response=422, description="Validation error")
     * )
     */
    public function getClockIns(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'worker_id' => 'required|exists:workers,id',
        ]);

        if ($validator->fails()) {
            return response()->json(['error' => $validator->errors()], 422);
        }

        $worker = Worker::findOrFail($request->input('worker_id'));
        $clockIns = $worker->clockIns;

        return response()->json([
            'worker' => $worker,
            'clockIns' => $clockIns
        ], 200);
    }
}
