<?php

namespace TheRiptide\LaravelDynamicDashboard\Factories;

use Illuminate\Support\Str;
use Illuminate\Database\Eloquent\Factories\Factory;
use TheRiptide\LaravelDynamicDashboard\Models\DynTime;

class DynTimeFactory extends Factory
{

    protected $model = DynTime::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition()
    {
        return [
            'name' => Str::snake($this->faker->words(2, true)),
            'content' => $this->faker->date(),

        ];
    }
}
