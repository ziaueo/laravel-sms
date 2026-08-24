<?php

namespace Database\Factories;

use App\Models\Classroom;
use App\Models\GradeLevel;
use App\Models\School;
use App\Models\SchoolYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Classroom>
 */
class ClassroomFactory extends Factory
{
    protected $model = Classroom::class;

    public function definition(): array
    {
        return [
            'school_id'      => School::factory(),
            'school_year_id' => SchoolYear::factory(),
            'grade_level_id' => GradeLevel::factory(),
            'name'           => 'Kelas Uji ' . fake()->unique()->numberBetween(1, 999999),
            'capacity'       => 30,
            'is_active'      => true,
        ];
    }
}
