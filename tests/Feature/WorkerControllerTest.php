<?php

namespace Tests\Feature;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;
use App\Models\Worker;
use App\Models\ClockIn;
use App\Http\Controllers\WorkerController;
use App\Services\DistanceService;

class WorkerControllerTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function a_worker_can_clock_in_successfully()
    {
        $worker = Worker::factory()->create();

        $response = $this->postJson('/api/worker/clock-in', [
            'worker_id' => $worker->id,
            'timestamp' => 1672549200,
            'latitude' => -34.615662,
            'longitude' => -58.362512
        ]);

        $response->assertStatus(200)
            ->assertJson(['message' => 'Clock-in recorded successfully.']);

        $this->assertDatabaseHas('clock_ins', [
            'worker_id' => $worker->id,
            'latitude' => -34.615662,
            'longitude' => -58.362512
        ]);
    }

    /** @test */
    public function clock_in_fails_with_invalid_data()
    {
        $response = $this->postJson('/api/worker/clock-in', []);

        $response->assertStatus(422) // Expect 422 status code for validation errors
            ->assertJsonStructure(['error']);
    }


    /** @test */
    public function clock_in_fails_if_location_is_not_within_2km()
    {
        $worker = Worker::factory()->create();

        $response = $this->postJson('/api/worker/clock-in', [
            'worker_id' => $worker->id,
            'timestamp' => 1672549200,
            'latitude' => -34.0, // Far from the specified location
            'longitude' => -58.0
        ]);

        $response->assertStatus(400)
            ->assertJson(['error' => 'Location not within 2km radius.']);
    }

    /** @test */
    public function it_can_retrieve_clock_ins_for_a_worker()
    {
        $worker = Worker::factory()->create();
        $clockIns = ClockIn::factory()->count(3)->create(['worker_id' => $worker->id]);

        $response = $this->getJson('/api/worker/clock-ins?worker_id=' . $worker->id);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'worker' => ['id', 'name'],
                'clockIns' => [
                    '*' => ['timestamp', 'latitude', 'longitude']
                ]
            ]);
    }

    /** @test */
    public function it_fails_to_retrieve_clock_ins_with_invalid_worker_id()
    {
        $response = $this->getJson('/api/worker/clock-ins?worker_id=999'); // Non-existing worker_id

        $response->assertStatus(422) // Use 422 for validation errors
            ->assertJson(['error' => ['worker_id' => ['The selected worker id is invalid.']]]);
    }

    /** @test */
}
