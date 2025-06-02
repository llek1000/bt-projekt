<?php


namespace Database\Factories;

use App\Models\EditorAssignment;
use App\Models\ConferenceYear;
use App\Models\User;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\EditorAssignment>
 */
class EditorAssignmentFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = EditorAssignment::class;

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
        // Get available users and conference years
        $users = User::pluck('id')->toArray();
        $conferenceYears = ConferenceYear::pluck('id')->toArray();

        // If no users or conference years exist, create them
        if (empty($users)) {
            $users = [User::factory()->create()->id];
        }
        if (empty($conferenceYears)) {
            $conferenceYears = [ConferenceYear::factory()->create()->id];
        }

        // Generate unique user-conference year combination
        $attempts = 0;
        $maxAttempts = 100; // Prevent infinite loop

        do {
            $userId = $this->faker->randomElement($users);
            $conferenceYearId = $this->faker->randomElement($conferenceYears);
            $combination = $userId . '-' . $conferenceYearId;
            $attempts++;

            // If we've tried too many times, reset combinations
            if ($attempts > $maxAttempts) {
                self::$usedCombinations = [];
                break;
            }
        } while (in_array($combination, self::$usedCombinations));

        // Track this combination
        self::$usedCombinations[] = $combination;

        return [
            'user_id' => $userId,
            'conference_year_id' => $conferenceYearId,
        ];
    }

    /**
     * Assign to a specific user.
     */
    public function forUser(User $user): static
    {
        return $this->state(fn (array $attributes) => [
            'user_id' => $user->id,
        ]);
    }

    /**
     * Assign to a specific conference year.
     */
    public function forConferenceYear(ConferenceYear $conferenceYear): static
    {
        return $this->state(fn (array $attributes) => [
            'conference_year_id' => $conferenceYear->id,
        ]);
    }

    /**
     * Reset used combinations (useful for testing)
     */
    public static function resetUsedCombinations(): void
    {
        self::$usedCombinations = [];
    }

    /**
     * Create unique assignments for all users across conference years
     */
    public static function createUniqueAssignments(): void
    {
        $users = User::all();
        $conferenceYears = ConferenceYear::all();

        foreach ($users as $user) {
            // Assign each user to 1-3 random conference years
            $assignmentCount = min(rand(1, 3), $conferenceYears->count());
            $selectedConferences = $conferenceYears->random($assignmentCount);

            foreach ($selectedConferences as $conference) {
                // Check if assignment already exists
                $exists = EditorAssignment::where('user_id', $user->id)
                                         ->where('conference_year_id', $conference->id)
                                         ->exists();

                if (!$exists) {
                    EditorAssignment::create([
                        'user_id' => $user->id,
                        'conference_year_id' => $conference->id,
                    ]);
                }
            }
        }
    }
}
