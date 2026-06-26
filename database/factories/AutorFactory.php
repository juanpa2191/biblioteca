<?php

namespace Database\Factories;

use App\Models\Autor;
use Illuminate\Database\Eloquent\Factories\Factory;

class AutorFactory extends Factory
{
    protected $model = Autor::class;

    public function definition()
    {
        $nacionalidades = [
            'Colombiana', 'Argentina', 'Mexicana', 'Española', 'Chilena',
            'Peruana', 'Cubana', 'Uruguaya', 'Estadounidense', 'Francesa',
        ];

        return [
            'nombre' => $this->faker->firstName(),
            'apellido' => $this->faker->lastName(),
            'fecha_nacimiento' => $this->faker->dateTimeBetween('-90 years', '-30 years')->format('Y-m-d'),
            'nacionalidad' => $this->faker->randomElement($nacionalidades),
            'biografia' => $this->faker->paragraph(3),
        ];
    }
}
