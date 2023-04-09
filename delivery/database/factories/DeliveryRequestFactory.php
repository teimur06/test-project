<?php

namespace Database\Factories;

use App\Models\DeliveryRequest;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\DeliveryRequest>
 */
class DeliveryRequestFactory extends Factory
{
    protected $model = DeliveryRequest::class;
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'user_id' => function () {
                return \App\Models\User::factory()->create()->id;
            },
            'from_city_id' => function () {
                return \App\Models\City::factory()->create()->id;
            },
            'to_city_id' => function () {
                return \App\Models\City::factory()->create()->id;
            },
            'delivery_date' => $this->faker->date(),
            'status' => $this->faker->randomElement(['pending', 'rejected', 'approved']),
            'group' => null
        ];
    }
}
