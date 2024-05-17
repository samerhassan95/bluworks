<?php
// app/Services/DistanceService.php
namespace App\Services;

class DistanceService
{
    const LOCATION = [-34.615662, -58.362512];

    public function calculateDistance($lat1, $lon1, $lat2, $lon2)
    {
        $theta = $lon1 - $lon2;
        $dist = sin(deg2rad($lat1)) * sin(deg2rad($lat2)) + cos(deg2rad($lat1)) * cos(deg2rad($lat2)) * cos(deg2rad($theta));
        $dist = acos($dist);
        $dist = rad2deg($dist);
        $miles = $dist * 60 * 1.1515;
        $distance = $miles * 1.609344; // Convert distance to kilometers
        return $distance;
    }
}