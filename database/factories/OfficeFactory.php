<?php

namespace Database\Factories;

use App\Models\Office;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

class OfficeFactory extends Factory
{
    protected $model = Office::class;
    /**
     * Define the model's default state.
     *
     * @return array
     */
    public function definition()
    {
        return [
            'title' => $this->faker->sentence,
            'description' => $this->faker->paragraph,
            'lat' => $this->faker->latitude,
            'lng' => $this->faker->longitude,
            'address_line1' => $this->faker->address,
            'approval_status' => 2,
            'hidden' => false,
            'price_per_day' => $this->faker->numberBetween(1_000,2_000),
            'monthly_discount' => $this->faker->numberBetween(0,100),
            'user_id' => User::factory()
        ];
    }
}
