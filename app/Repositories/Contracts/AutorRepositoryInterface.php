<?php

namespace App\Repositories\Contracts;

use App\Models\Autor;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

interface AutorRepositoryInterface
{
    public function paginar(int $porPagina = 15): LengthAwarePaginator;

    public function buscarPorId(int $id): ?Autor;

    public function crear(array $datos): Autor;

    public function actualizar(Autor $autor, array $datos): Autor;

    public function eliminar(Autor $autor): bool;

    public function contarLibros(Autor $autor): int;
}
