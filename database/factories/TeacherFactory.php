<?php

namespace Database\Factories;

use App\Constants\GenderConstant;
use App\Models\School;
use App\Models\Teacher;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Teacher>
 */
class TeacherFactory extends Factory
{
    protected $model = Teacher::class;

    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'full_name' => 'Pegawai Uji ' . fake()->unique()->numberBetween(1, 999999),
            'gender'    => GenderConstant::LAKI_LAKI,
            'is_active' => true,
        ];
    }

    public function named(string $name): static
    {
        return $this->state(fn () => ['full_name' => $name]);
    }

    public function inactive(): static
    {
        return $this->state(fn () => ['is_active' => false]);
    }
}
