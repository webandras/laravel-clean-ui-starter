<?php

namespace Database\Factories\Job;

use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Carbon;
use Modules\Job\Models\Client;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Model>
 */
class ClientFactory extends Factory
{
    /**
     * @var string
     */
    protected $model = Client::class;


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $now = Carbon::now('utc')->toDateTimeString();

        return [
            'name'             => $this->faker->company(),
            'address'          => $this->faker->streetAddress(),
            'type'             => $this->faker->randomElement(['company', 'private person']),
            'client_detail_id' => 1,
            'created_at'       => $now,
            'updated_at'       => $now,
        ];
    }
}
