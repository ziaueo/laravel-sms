<?php

namespace Database\Factories;

use App\Models\Curriculum;
use App\Models\School;
use App\Models\SchoolYear;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<SchoolYear>
 */
class SchoolYearFactory extends Factory
{
    protected $model = SchoolYear::class;

    public function definition(): array
    {
        // Tabel unik pada (school_id, year, semester), jadi tahunnya digilir
        // supaya beberapa tahun ajaran bisa dibuat untuk sekolah yang sama.
        static $offset = 0;
        $start = 2000 + ($offset++ % 90);

        return [
            'school_id'     => School::factory(),
            'curriculum_id' => Curriculum::factory(),
            'name'          => 'TA ' . $start . '/' . ($start + 1),
            'year'          => $start . '/' . ($start + 1),
            'semester'      => 1,
            'start_date'    => $start . '-07-01',
            'end_date'      => ($start + 1) . '-06-30',
            'is_active'     => false,
        ];
    }

    public function active(): static
    {
        return $this->state(fn () => ['is_active' => true]);
    }
}
