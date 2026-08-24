<?php

namespace Database\Factories;

use App\Models\Curriculum;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Curriculum>
 */
class CurriculumFactory extends Factory
{
    protected $model = Curriculum::class;

    public function definition(): array
    {
        $n = fake()->unique()->numberBetween(1, 999999);

        return [
            'name'      => 'Kurikulum Uji ' . $n,
            'code'      => 'KU' . $n,
            'is_active' => true,
        ];
    }
}
