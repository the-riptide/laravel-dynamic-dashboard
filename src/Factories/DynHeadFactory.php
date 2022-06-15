<?php

namespace TheRiptide\LaravelDynamicDashboard\Factories;

use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;
use TheRiptide\LaravelDynamicDashboard\Models\DynHead;

class DynHeadFactory extends Factory
{

    protected $model = DynHead::class;


    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'slug' => $this->faker->slug(),
            'dyn_type' => $this->faker->word(),
            'dyn_type' => $this->faker->numberBetween(1, 20),
            'user_id' => User::factory(),
        ];
    }
}
