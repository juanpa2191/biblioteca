<?php

namespace App\Services;

use App\Domain\Exceptions\AutorConLibrosException;
use App\Models\Autor;
use App\Repositories\Contracts\AutorRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;

class AutorService
{
    public function __construct(private AutorRepositoryInterface $autores)
    {
    }

    public function listar(int $porPagina = 15): LengthAwarePaginator
    {
        return $this->autores->paginar($porPagina);
    }

    public function obtener(int $id): ?Autor
    {
        return $this->autores->buscarPorId($id);
    }

    public function crear(array $datos): Autor
    {
        return $this->autores->crear($datos);
    }

    public function actualizar(Autor $autor, array $datos): Autor
    {
        return $this->autores->actualizar($autor, $datos);
    }

    /**
     * Elimina un autor solo si no tiene libros asociados.
     *
     * @throws AutorConLibrosException
     */
    public function eliminar(Autor $autor): bool
    {
        $cantidad = $this->autores->contarLibros($autor);

        if ($cantidad > 0) {
            throw AutorConLibrosException::paraAutor($autor->id, $cantidad);
        }

        return $this->autores->eliminar($autor);
    }
}
