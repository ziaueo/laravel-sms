<?php

namespace Database\Factories;

use App\Models\Banner;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Banner>
 */
class BannerFactory extends Factory
{
    protected $model = Banner::class;

    public function definition(): array
    {
        $n = fake()->unique()->numberBetween(1, 999999);

        return [
            'school_id'    => School::factory(),
            'title'        => 'Banner Uji ' . $n,
            'subtitle'     => 'Subjudul banner ' . $n,
            'image'        => 'uploads/banners/banner-' . $n . '.jpg',
            'order'        => 0,
            'is_published' => true,
        ];
    }

    public function titled(string $title): static
    {
        return $this->state(fn () => ['title' => $title]);
    }

    public function unpublished(): static
    {
        return $this->state(fn () => ['is_published' => false]);
    }
}
