<?php

namespace App\Services;

use App\Models\Libro;
use App\Repositories\Contracts\LibroRepositoryInterface;
use Illuminate\Contracts\Pagination\LengthAwarePaginator;
use Illuminate\Support\Facades\DB;

class LibroService
{
    public function __construct(private LibroRepositoryInterface $libros)
    {
    }

    public function listarConFiltros(array $filtros, int $porPagina = 15): LengthAwarePaginator
    {
        return $this->libros->paginarConFiltros($filtros, $porPagina);
    }

    public function obtenerConAutores(int $id): ?Libro
    {
        return $this->libros->buscarPorId($id, conAutores: true);
    }

    public function crear(array $datos, array $autorIds = []): Libro
    {
        return DB::transaction(function () use ($datos, $autorIds) {
            $libro = $this->libros->crear($datos);

            if (!empty($autorIds)) {
                $this->libros->sincronizarAutores($libro, $autorIds);
            }

            return $libro->load('autores');
        });
    }

    public function actualizar(Libro $libro, array $datos, ?array $autorIds = null): Libro
    {
        return DB::transaction(function () use ($libro, $datos, $autorIds) {
            $actualizado = $this->libros->actualizar($libro, $datos);

            if (is_array($autorIds)) {
                $this->libros->sincronizarAutores($actualizado, $autorIds);
            }

            return $actualizado->load('autores');
        });
    }

    public function eliminar(Libro $libro): bool
    {
        return $this->libros->eliminar($libro);
    }
}
