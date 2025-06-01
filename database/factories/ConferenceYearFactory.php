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
     * Track used combinations to avoid duplicates
     */
    private static $usedCombinations = [];

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        // Generate unique year-semester combination
        do {
            $year = $this->faker->numberBetween(2020, 2030);
            $semester = $this->faker->randomElement(['Winter', 'Summer']);
            $combination = $year . '-' . $semester;
        } while (in_array($combination, self::$usedCombinations));

        // Track this combination
        self::$usedCombinations[] = $combination;

        return [
            'year' => (string) $year,
            'semester' => $semester,
            'is_active' => $this->faker->boolean(80), // 80% chance of being active
        ];
    }

    /**
     * Indicate that the conference year is active.
     */
    public function active(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => true,
        ]);
    }

    /**
     * Indicate that the conference year is inactive.
     */
    public function inactive(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_active' => false,
        ]);
    }

    /**
     * Set a specific year and semester.
     */
    public function yearSemester(string $year, string $semester): static
    {
        return $this->state(fn (array $attributes) => [
            'year' => $year,
            'semester' => $semester,
        ]);
    }

    /**
     * Create sequential conference years (recommended approach)
     */
    public static function createSequential(int $startYear = 2020, int $count = 5): array
    {
        $conferences = [];
        $currentYear = $startYear;
        $semester = 'Winter';

        for ($i = 0; $i < $count; $i++) {
            $conferences[] = ConferenceYear::factory()->yearSemester((string) $currentYear, $semester)->create();

            // Alternate semester
            if ($semester === 'Winter') {
                $semester = 'Summer';
            } else {
                $semester = 'Winter';
                $currentYear++;
            }
        }

        return $conferences;
    }

    /**
     * Reset used combinations (useful for testing)
     */
    public static function resetUsedCombinations(): void
    {
        self::$usedCombinations = [];
    }
}
