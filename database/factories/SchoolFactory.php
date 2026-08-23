<?php

namespace Database\Factories;

use App\Models\School;
use App\Models\SchoolType;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<School>
 */
class SchoolFactory extends Factory
{
    protected $model = School::class;

    public function definition(): array
    {
        // Nomor urut menjaga slug tetap unik tanpa bergantung pada keberuntungan Faker.
        $suffix = fake()->unique()->numberBetween(1, 999999);

        return [
            'school_type_id' => SchoolType::factory(),
            'name'           => 'Sekolah Uji ' . $suffix,
            'slug'           => 'sekolah-uji-' . $suffix,
            'is_active'      => true,
        ];
    }

    public function named(string $name): static
    {
        return $this->state(fn () => [
            'name' => $name,
            'slug' => Str::slug($name) . '-' . fake()->unique()->numberBetween(1, 999999),
        ]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
