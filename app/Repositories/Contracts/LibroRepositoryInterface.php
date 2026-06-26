<?php

namespace App\Repositories\Contracts;

use App\Models\Libro;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface LibroRepositoryInterface
{
    public function paginarConFiltros(array $filtros, int $porPagina = 15): LengthAwarePaginator;

    public function buscarPorId(int $id, bool $conAutores = false): ?Libro;

    public function crear(array $datos): Libro;

    public function actualizar(Libro $libro, array $datos): Libro;

    public function eliminar(Libro $libro): bool;

    public function decrementarStock(Libro $libro, int $cantidad = 1): bool;

    public function incrementarStock(Libro $libro, int $cantidad = 1): bool;

    public function sincronizarAutores(Libro $libro, array $autorIdsConOrden): void;

    public function masPrestados(int $limite = 10): Collection;

    public function sinStock(): Collection;
}
