<?php

namespace Database\Factories;

use App\Constants\PositionConstant;
use App\Models\Position;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Position>
 */
class PositionFactory extends Factory
{
    protected $model = Position::class;

    public function definition(): array
    {
        return [
            'name'  => 'Jabatan Uji ' . fake()->unique()->numberBetween(1, 999999),
            'type'  => PositionConstant::GURU,
            'order' => 10,
        ];
    }

    public function pimpinan(string $name, int $order): static
    {
        return $this->state(fn () => [
            'name'  => $name,
            'type'  => PositionConstant::PIMPINAN,
            'order' => $order,
        ]);
    }

    public function guru(string $name, int $order = 10): static
    {
        return $this->state(fn () => [
            'name'  => $name,
            'type'  => PositionConstant::GURU,
            'order' => $order,
        ]);
    }

    public function staff(string $name, int $order = 20): static
    {
        return $this->state(fn () => [
            'name'  => $name,
            'type'  => PositionConstant::STAFF,
            'order' => $order,
        ]);
    }
}
