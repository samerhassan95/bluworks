<?php

namespace Tests\Unit;

use Tests\TestCase;
use App\Services\DistanceService;

class DistanceServiceTest extends TestCase
{
    /** @test */
    public function it_calculates_distance_correctly()
    {
        $distanceService = new DistanceService();

        $lat1 = -34.615662;
        $lon1 = -58.362512;
        $lat2 = -34.615700;
        $lon2 = -58.362600;

        $distance = $distanceService->calculateDistance($lat1, $lon1, $lat2, $lon2);

        $this->assertIsFloat($distance);
        $this->assertLessThan(2, $distance, 'Distance should be less than 2 km');
    }
}
