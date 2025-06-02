<?php

namespace Database\Factories;

use App\Models\Article;
use App\Models\ConferenceYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Article>
 */
class ArticleFactory extends Factory
{
    /**
     * The name of the factory's corresponding model.
     *
     * @var string
     */
    protected $model = Article::class;

    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'title' => $this->faker->sentence(6, true),
            'content' => $this->faker->paragraphs(5, true),
            'conference_year_id' => function() {
                return ConferenceYear::inRandomOrder()->first()?->id ?? ConferenceYear::factory()->create()->id;
            },
            'author_name' => $this->faker->name(),
        ];
    }

    public function forConferenceYear(ConferenceYear $conferenceYear): static
    {
        return $this->state(fn (array $attributes) => [
            'conference_year_id' => $conferenceYear->id,
        ]);
    }

    public function byAuthor(string $authorName): static
    {
        return $this->state(fn (array $attributes) => [
            'author_name' => $authorName,
        ]);
    }
}
