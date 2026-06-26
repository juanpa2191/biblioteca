<?php

namespace App\Http\Controllers\Api;

use App\Domain\Enums\EstadoPrestamo;
use App\Domain\Enums\EstadoUsuario;
use App\Http\Controllers\Controller;
use App\Http\Resources\PrestamoResource;
use App\Models\Libro;
use App\Models\Prestamo;
use App\Models\Usuario;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    public function stats(): JsonResponse
    {
        $totalLibros        = Libro::count();
        $librosDisponibles  = Libro::where('stock_disponible', '>', 0)->count();
        $librosSinStock     = Libro::where('stock_disponible', 0)->count();

        $totalUsuarios      = Usuario::count();
        $usuariosActivos    = Usuario::where('estado', EstadoUsuario::ACTIVO)->count();

        $prestamosActivos   = Prestamo::where('estado', EstadoPrestamo::ACTIVO)->count();
        $prestamosDevueltos = Prestamo::where('estado', EstadoPrestamo::DEVUELTO)->count();
        $prestamosVencidos  = Prestamo::where('estado', EstadoPrestamo::VENCIDO)->count();

        $ultimosPrestamos = Prestamo::with(['usuario', 'libro'])
            ->orderByDesc('fecha_prestamo')
            ->orderByDesc('id')
            ->limit(5)
            ->get();

        $librosBajoStock = Libro::where('stock_disponible', '<=', 2)
            ->where('stock_disponible', '>', 0)
            ->orderBy('stock_disponible')
            ->limit(5)
            ->get(['id', 'titulo', 'isbn', 'stock_disponible']);

        return response()->json([
            'totales' => [
                'libros'              => $totalLibros,
                'libros_disponibles'  => $librosDisponibles,
                'libros_sin_stock'    => $librosSinStock,
                'usuarios'            => $totalUsuarios,
                'usuarios_activos'    => $usuariosActivos,
                'prestamos_activos'   => $prestamosActivos,
                'prestamos_devueltos' => $prestamosDevueltos,
                'prestamos_vencidos'  => $prestamosVencidos,
            ],
            'ultimos_prestamos' => PrestamoResource::collection($ultimosPrestamos),
            'libros_bajo_stock' => $librosBajoStock->map(fn ($l) => [
                'id'               => $l->id,
                'titulo'           => $l->titulo,
                'isbn'             => $l->isbn,
                'stock_disponible' => $l->stock_disponible,
            ]),
        ]);
    }
}
