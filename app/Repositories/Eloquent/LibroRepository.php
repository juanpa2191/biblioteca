<?php

namespace App\Repositories\Eloquent;

use App\Domain\Enums\EstadoPrestamo;
use App\Models\Libro;
use App\Repositories\Contracts\LibroRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;
use Illuminate\Support\Facades\DB;

class LibroRepository implements LibroRepositoryInterface
{
    public function paginarConFiltros(array $filtros, int $porPagina = 15): LengthAwarePaginator
    {
        $query = Libro::query()->with('autores');

        if (!empty($filtros['titulo'])) {
            $query->porTitulo($filtros['titulo']);
        }

        if (!empty($filtros['autor'])) {
            $query->porAutor((int) $filtros['autor']);
        }

        if (!empty($filtros['anio'])) {
            $query->porAnio((int) $filtros['anio']);
        }

        if (!empty($filtros['disponibles'])) {
            $query->disponibles();
        }

        return $query->orderBy('titulo')->paginate($porPagina);
    }

    public function buscarPorId(int $id, bool $conAutores = false): ?Libro
    {
        $query = Libro::query();

        if ($conAutores) {
            $query->with('autores');
        }

        return $query->find($id);
    }

    public function crear(array $datos): Libro
    {
        return Libro::create($datos);
    }

    public function actualizar(Libro $libro, array $datos): Libro
    {
        $libro->update($datos);

        return $libro->refresh();
    }

    public function eliminar(Libro $libro): bool
    {
        return (bool) $libro->delete();
    }

    public function decrementarStock(Libro $libro, int $cantidad = 1): bool
    {
        $afectados = Libro::where('id', $libro->id)
            ->where('stock_disponible', '>=', $cantidad)
            ->update(['stock_disponible' => DB::raw("stock_disponible - {$cantidad}")]);

        if ($afectados > 0) {
            $libro->refresh();
        }

        return $afectados > 0;
    }

    public function incrementarStock(Libro $libro, int $cantidad = 1): bool
    {
        $libro->increment('stock_disponible', $cantidad);

        return true;
    }

    public function sincronizarAutores(Libro $libro, array $autorIdsConOrden): void
    {
        $sync = [];
        foreach ($autorIdsConOrden as $orden => $autorId) {
            $sync[(int) $autorId] = ['orden_autor' => $orden + 1];
        }

        $libro->autores()->sync($sync);
    }

    public function masPrestados(int $limite = 10): Collection
    {
        return Libro::query()
            ->withCount(['prestamos as total_prestamos'])
            ->orderByDesc('total_prestamos')
            ->limit($limite)
            ->get();
    }

    public function sinStock(): Collection
    {
        return Libro::query()
            ->where('stock_disponible', 0)
            ->orderBy('titulo')
            ->get();
    }
}
