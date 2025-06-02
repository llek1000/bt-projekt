<?php

namespace Database\Factories;

use App\Models\ConferenceYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\ConferenceYear>
 */
class ConferenceYearFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = ConferenceYear::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'year' => $this->faker->numberBetween(2020, 2030),
            'semester' => $this->faker->randomElement(['Winter', 'Summer']),
            'is_active' => $this->faker->boolean(80),
        ];
    }


    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    public function yearSemester(string $year, string $semester): static
    {
        return $this->state(fn (array $attributes) => [
            'year' => $year,
            'semester' => $semester,
        ]);
    }
}
