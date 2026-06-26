<?php

namespace Database\Factories;

use App\Models\Libro;
use Illuminate\Database\Eloquent\Factories\Factory;

class LibroFactory extends Factory
{
    protected $model = Libro::class;

    public function definition()
    {
        return [
            'titulo' => ucfirst($this->faker->words(rand(2, 5), true)),
            'isbn' => $this->faker->unique()->isbn13(),
            'anio_publicacion' => $this->faker->numberBetween(1950, 2025),
            'numero_paginas' => $this->faker->numberBetween(80, 800),
            'descripcion' => $this->faker->paragraph(2),
            'stock_disponible' => $this->faker->numberBetween(1, 8),
        ];
    }

    public function sinStock(): self
    {
        return $this->state(fn () => ['stock_disponible' => 0]);
    }
}
