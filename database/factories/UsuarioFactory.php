<?php

namespace Database\Factories;

use App\Domain\Enums\EstadoUsuario;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class UsuarioFactory extends Factory
{
    protected $model = Usuario::class;

    public function definition()
    {
        return [
            'nombre' => $this->faker->name(),
            'email' => $this->faker->unique()->safeEmail(),
            'telefono' => $this->faker->numerify('30########'),
            'fecha_registro' => $this->faker->dateTimeBetween('-2 years', 'now')->format('Y-m-d'),
            'estado' => $this->faker->randomElement([
                EstadoUsuario::ACTIVO,
                EstadoUsuario::ACTIVO,
                EstadoUsuario::ACTIVO,
                EstadoUsuario::INACTIVO,
            ]),
        ];
    }

    public function activo(): self
    {
        return $this->state(fn () => ['estado' => EstadoUsuario::ACTIVO]);
    }

    public function inactivo(): self
    {
        return $this->state(fn () => ['estado' => EstadoUsuario::INACTIVO]);
    }
}
