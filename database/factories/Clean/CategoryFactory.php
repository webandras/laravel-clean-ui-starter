<?php

namespace Database\Factories\Clean;

use Illuminate\Database\Eloquent\Factories\Factory;
use Modules\Clean\Models\Category;

/**
 * @extends Factory<Category>
 */
class CategoryFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Category::class;


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'name' => $this->faker->words(4, true),
            'slug' => $this->faker->slug,
            'category_id' => null,
        ];
    }
}
