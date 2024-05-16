<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Worker;
use App\Models\ClockIn;
use Illuminate\Support\Facades\Validator;

class WorkerController extends Controller
{

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
        $location = [-34.615662, -58.362512]; // Coordinates for example location
        $distance = $this->distance($latitude, $longitude, $location[0], $location[1]);
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

    // get list of clock_ins for specific worker
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

    // calculating distance
    public function distance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) +  cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $unit = 'K';
        $distance = $miles * 1.609344;
        return $distance;
    }
}
