<?php

namespace Database\Factories;

use App\Models\GradeLevel;
use App\Models\School;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GradeLevel>
 */
class GradeLevelFactory extends Factory
{
    protected $model = GradeLevel::class;

    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'name'      => 'Tingkat ' . fake()->unique()->numberBetween(1, 999999),
            'order'     => 0,
        ];
    }
}
