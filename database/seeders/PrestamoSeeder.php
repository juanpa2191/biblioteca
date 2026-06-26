<?php

namespace Database\Seeders;

use App\Domain\Enums\EstadoPrestamo;
use App\Domain\Enums\EstadoUsuario;
use App\Models\Libro;
use App\Models\Prestamo;
use App\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class PrestamoSeeder extends Seeder
{
    public function run()
    {
        $usuariosActivos = Usuario::where('estado', EstadoUsuario::ACTIVO)->get();
        $librosConStock = Libro::where('stock_disponible', '>', 0)->get();

        if ($usuariosActivos->isEmpty() || $librosConStock->isEmpty()) {
            $this->command->warn('PrestamoSeeder: requiere usuarios activos y libros con stock.');
            return;
        }

        $distribucion = [
            ['estado' => EstadoPrestamo::ACTIVO,   'cantidad' => 5],
            ['estado' => EstadoPrestamo::DEVUELTO, 'cantidad' => 3],
            ['estado' => EstadoPrestamo::VENCIDO,  'cantidad' => 2],
        ];

        $prestamosPorUsuario = [];

        foreach ($distribucion as $grupo) {
            for ($i = 0; $i < $grupo['cantidad']; $i++) {
                DB::transaction(function () use ($grupo, $usuariosActivos, $librosConStock, &$prestamosPorUsuario) {
                    $usuario = $this->seleccionarUsuarioDisponible($usuariosActivos, $prestamosPorUsuario);
                    $libro = $this->seleccionarLibroConStock($librosConStock, $grupo['estado']);

                    if (!$usuario || !$libro) {
                        return;
                    }

                    $factory = Prestamo::factory();
                    $factory = match ($grupo['estado']) {
                        EstadoPrestamo::ACTIVO   => $factory->activo(),
                        EstadoPrestamo::DEVUELTO => $factory->devuelto(),
                        EstadoPrestamo::VENCIDO  => $factory->vencido(),
                    };

                    $factory->create([
                        'usuario_id' => $usuario->id,
                        'libro_id' => $libro->id,
                    ]);

                    if ($grupo['estado'] !== EstadoPrestamo::DEVUELTO) {
                        $libro->decrement('stock_disponible');
                        $prestamosPorUsuario[$usuario->id] = ($prestamosPorUsuario[$usuario->id] ?? 0) + 1;
                    }
                });
            }
        }
    }

    private function seleccionarUsuarioDisponible($usuarios, array $prestamosPorUsuario): ?Usuario
    {
        $candidatos = $usuarios->filter(
            fn (Usuario $u) => ($prestamosPorUsuario[$u->id] ?? 0) < 3
        );

        return $candidatos->isEmpty() ? null : $candidatos->random();
    }

    private function seleccionarLibroConStock($libros, string $estado): ?Libro
    {
        if ($estado === EstadoPrestamo::DEVUELTO) {
            return $libros->random();
        }

        $candidatos = $libros->fresh()->filter(fn (Libro $l) => $l->stock_disponible > 0);

        return $candidatos->isEmpty() ? null : $candidatos->random();
    }
}
