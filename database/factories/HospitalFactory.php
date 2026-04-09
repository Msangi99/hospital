<?php

namespace Database\Factories;

use App\Models\Hospital;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Hospital>
 */
class HospitalFactory extends Factory
{
    protected $model = Hospital::class;

    public function definition(): array
    {
        return [
            'name' => fake()->company().' Medical',
            'location' => fake()->city(),
            'latitude' => fake()->latitude(-11, -1),
            'longitude' => fake()->longitude(29, 40),
            'type' => 'Clinic',
            'status' => 'Online',
        ];
    }
}
