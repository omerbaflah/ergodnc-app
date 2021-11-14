<?php

namespace Database\Factories;

use App\Models\Office;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class ReservationFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'price' => $this->faker->numberBetween(10_000,20_000),
            'status' => 1,
            'start_date' => now()->addDay(1)->format('Y-m-d'),
            'end_date' => now()->addDay(5)->format('Y-m-d'),
            'user_id' => User::factory(),
            'office_id' => Office::factory(),
        ];
    }
}
