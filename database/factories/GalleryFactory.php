<?php

namespace Database\Factories;

use App\Models\Gallery;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Gallery>
 */
class GalleryFactory extends Factory
{
    protected $model = Gallery::class;

    public function definition(): array
    {
        return [
            'school_id'    => School::factory(),
            'title'        => 'Album Uji ' . fake()->unique()->numberBetween(1, 999999),
            'type'         => 1,
            'is_published' => true,
            'published_at' => now(),
        ];
    }

    public function titled(string $title): static
    {
        return $this->state(fn () => ['title' => $title]);
    }

    public function unpublished(): static
    {
        return $this->state(fn () => ['is_published' => false, 'published_at' => null]);
    }
}
