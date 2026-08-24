<?php

namespace Database\Factories;

use App\Models\Extracurricular;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Extracurricular>
 */
class ExtracurricularFactory extends Factory
{
    protected $model = Extracurricular::class;

    public function definition(): array
    {
        return [
            'school_id'   => School::factory(),
            'name'        => 'Ekskul Uji ' . fake()->unique()->numberBetween(1, 999999),
            'description' => 'Deskripsi kegiatan ekstrakurikuler.',
            'is_active'   => true,
        ];
    }

    public function named(string $name): static
    {
        return $this->state(fn () => ['name' => $name]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
