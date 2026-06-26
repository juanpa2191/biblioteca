<?php

namespace App\Repositories\Eloquent;

use App\Domain\Enums\EstadoPrestamo;
use App\Models\Prestamo;
use App\Repositories\Contracts\PrestamoRepositoryInterface;
use Carbon\CarbonImmutable;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Database\Eloquent\Collection;

class PrestamoRepository implements PrestamoRepositoryInterface
{
    public function paginarConRelaciones(int $porPagina = 15): LengthAwarePaginator
    {
        return Prestamo::query()
            ->with(['usuario', 'libro'])
            ->orderByDesc('fecha_prestamo')
            ->paginate($porPagina);
    }

    public function buscarPorId(int $id): ?Prestamo
    {
        return Prestamo::query()->with(['usuario', 'libro'])->find($id);
    }

    public function crear(array $datos): Prestamo
    {
        return Prestamo::create($datos);
    }

    public function actualizar(Prestamo $prestamo, array $datos): Prestamo
    {
        $prestamo->update($datos);

        return $prestamo->refresh();
    }

    public function contarActivosPorUsuario(int $usuarioId): int
    {
        return Prestamo::query()
            ->where('usuario_id', $usuarioId)
            ->where('estado', EstadoPrestamo::ACTIVO)
            ->count();
    }

    public function vencidosPorMarcar(int $diasGracia = 15): Collection
    {
        $umbral = CarbonImmutable::now()->subDays($diasGracia)->toDateString();

        return Prestamo::query()
            ->where('estado', EstadoPrestamo::ACTIVO)
            ->whereNull('fecha_devolucion_real')
            ->where('fecha_devolucion_estimada', '<', $umbral)
            ->get();
    }

    public function marcarComoVencido(Prestamo $prestamo): bool
    {
        return (bool) $prestamo->update(['estado' => EstadoPrestamo::VENCIDO]);
    }
}
