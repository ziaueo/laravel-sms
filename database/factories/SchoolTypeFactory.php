<?php

namespace Database\Factories;

use App\Models\SchoolType;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SchoolType>
 */
class SchoolTypeFactory extends Factory
{
    protected $model = SchoolType::class;

    public function definition(): array
    {
        return [
            'name'      => fake()->randomElement([
                'Sekolah Dasar',
                'Sekolah Menengah Pertama',
                'Sekolah Menengah Atas',
            ]),
            'order'     => 0,
            'is_active' => true,
        ];
    }
}
