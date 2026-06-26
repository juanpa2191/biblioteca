<?php

namespace Database\Factories;

use App\Domain\Enums\EstadoPrestamo;
use App\Models\Libro;
use App\Models\Prestamo;
use App\Models\Usuario;
use Illuminate\Database\Eloquent\Factories\Factory;

class PrestamoFactory extends Factory
{
    protected $model = Prestamo::class;

    public function definition()
    {
        $fechaPrestamo = $this->faker->dateTimeBetween('-30 days', 'now');
        $fechaEstimada = (clone $fechaPrestamo)->modify('+14 days');

        return [
            'usuario_id' => Usuario::factory(),
            'libro_id' => Libro::factory(),
            'fecha_prestamo' => $fechaPrestamo->format('Y-m-d'),
            'fecha_devolucion_estimada' => $fechaEstimada->format('Y-m-d'),
            'fecha_devolucion_real' => null,
            'estado' => EstadoPrestamo::ACTIVO,
        ];
    }

    public function activo(): self
    {
        return $this->state(fn () => [
            'estado' => EstadoPrestamo::ACTIVO,
            'fecha_devolucion_real' => null,
        ]);
    }

    public function devuelto(): self
    {
        return $this->state(function () {
            $fechaPrestamo = $this->faker->dateTimeBetween('-60 days', '-20 days');
            $fechaEstimada = (clone $fechaPrestamo)->modify('+14 days');
            $fechaReal = (clone $fechaPrestamo)->modify('+' . rand(5, 20) . ' days');

            return [
                'fecha_prestamo' => $fechaPrestamo->format('Y-m-d'),
                'fecha_devolucion_estimada' => $fechaEstimada->format('Y-m-d'),
                'fecha_devolucion_real' => $fechaReal->format('Y-m-d'),
                'estado' => EstadoPrestamo::DEVUELTO,
            ];
        });
    }

    public function vencido(): self
    {
        return $this->state(function () {
            $fechaPrestamo = $this->faker->dateTimeBetween('-60 days', '-35 days');
            $fechaEstimada = (clone $fechaPrestamo)->modify('+14 days');

            return [
                'fecha_prestamo' => $fechaPrestamo->format('Y-m-d'),
                'fecha_devolucion_estimada' => $fechaEstimada->format('Y-m-d'),
                'fecha_devolucion_real' => null,
                'estado' => EstadoPrestamo::VENCIDO,
            ];
        });
    }
}
