<?php

namespace Database\Factories;

use App\Models\ClockIn;
use App\Models\Worker;
use Illuminate\Database\Eloquent\Factories\Factory;

class ClockInFactory extends Factory
{
    protected $model = ClockIn::class;

    public function definition()
    {
        return [
            'worker_id' => Worker::factory(),
            'timestamp' => now(),
            'latitude' => $this->faker->latitude,
            'longitude' => $this->faker->longitude,
        ];
    }
}
