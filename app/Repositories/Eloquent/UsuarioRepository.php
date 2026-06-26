<?php

namespace App\Repositories\Eloquent;

use App\Domain\Enums\EstadoPrestamo;
use App\Models\Usuario;
use App\Repositories\Contracts\UsuarioRepositoryInterface;
use Illuminate\Database\Eloquent\Collection;

class UsuarioRepository implements UsuarioRepositoryInterface
{
    public function buscarPorId(int $id): ?Usuario
    {
        return Usuario::query()->find($id);
    }

    public function conPrestamosVencidos(): Collection
    {
        return Usuario::query()
            ->whereHas('prestamos', function ($q) {
                $q->where('estado', EstadoPrestamo::VENCIDO);
            })
            ->withCount(['prestamos as prestamos_vencidos_count' => function ($q) {
                $q->where('estado', EstadoPrestamo::VENCIDO);
            }])
            ->orderByDesc('prestamos_vencidos_count')
            ->get();
    }
}
