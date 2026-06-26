<?php

namespace App\Repositories\Contracts;

use App\Models\Prestamo;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

interface PrestamoRepositoryInterface
{
    public function paginarConRelaciones(int $porPagina = 15): LengthAwarePaginator;

    public function buscarPorId(int $id): ?Prestamo;

    public function crear(array $datos): Prestamo;

    public function actualizar(Prestamo $prestamo, array $datos): Prestamo;

    public function contarActivosPorUsuario(int $usuarioId): int;

    public function vencidosPorMarcar(int $diasGracia = 15): Collection;

    public function marcarComoVencido(Prestamo $prestamo): bool;
}
