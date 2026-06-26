<?php

namespace App\Repositories\Eloquent;

use App\Models\Autor;
use App\Repositories\Contracts\AutorRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AutorRepository implements AutorRepositoryInterface
{
    public function paginar(int $porPagina = 15): LengthAwarePaginator
    {
        return Autor::query()->orderBy('apellido')->orderBy('nombre')->paginate($porPagina);
    }

    public function buscarPorId(int $id): ?Autor
    {
        return Autor::query()->find($id);
    }

    public function crear(array $datos): Autor
    {
        return Autor::create($datos);
    }

    public function actualizar(Autor $autor, array $datos): Autor
    {
        $autor->update($datos);

        return $autor->refresh();
    }

    public function eliminar(Autor $autor): bool
    {
        return (bool) $autor->delete();
    }

    public function contarLibros(Autor $autor): int
    {
        return $autor->libros()->count();
    }
}
