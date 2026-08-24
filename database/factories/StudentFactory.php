<?php

namespace Database\Factories;

use App\Constants\GenderConstant;
use App\Constants\StudentStatusConstant;
use App\Models\School;
use App\Models\Student;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Student>
 */
class StudentFactory extends Factory
{
    protected $model = Student::class;

    public function definition(): array
    {
        return [
            'school_id' => School::factory(),
            'full_name' => 'Siswa Uji ' . fake()->unique()->numberBetween(1, 999999),
            'gender'    => GenderConstant::LAKI_LAKI,
            'status'    => StudentStatusConstant::AKTIF,
        ];
    }

    public function status(int $status): static
    {
        return $this->state(fn () => ['status' => $status]);
    }
}
