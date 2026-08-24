<?php

namespace Database\Factories;

use App\Models\Gallery;
use App\Models\GalleryItem;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<GalleryItem>
 */
class GalleryItemFactory extends Factory
{
    protected $model = GalleryItem::class;

    public function definition(): array
    {
        $n = fake()->unique()->numberBetween(1, 999999);

        return [
            'gallery_id' => Gallery::factory(),
            'type'       => 1,
            'file_path'  => 'uploads/galleries/foto-' . $n . '.jpg',
            'caption'    => 'Foto Uji ' . $n,
            'order'      => 0,
        ];
    }

    public function captioned(string $caption): static
    {
        return $this->state(fn () => ['caption' => $caption]);
    }
}
