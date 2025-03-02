<?php

namespace Database\Factories;

use App\Models\Category;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends \Illuminate\Database\Eloquent\Factories\Factory<\App\Models\Post>
 */
class PostFactory extends Factory
{
    /**
     * Define the model's default state.
     *
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $title = fake()->sentence();
        $slug = Str::slug($title);

        return [
            'title' => $title,
            'slug' => $slug,
            'content' => fake()->paragraphs(5, true),
            'summary' => fake()->paragraph(),
            'locale' => fake()->randomElement(['zh', 'zh-CN', 'ja', 'vi', 'en']),
            'status' => fake()->randomElement(['published', 'draft']),
            'views' => fake()->numberBetween(0, 1000),
        ];
    }

    /**
     * Set the post's status to published.
     */
    public function published(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'published',
        ]);
    }

    /**
     * Set the post's status to draft.
     */
    public function draft(): static
    {
        return $this->state(fn (array $attributes) => [
            'status' => 'draft',
        ]);
    }
}
