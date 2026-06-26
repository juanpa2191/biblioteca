<?php

namespace App\Services;

use App\Repositories\Contracts\LibroRepositoryInterface;
use App\Repositories\Contracts\UsuarioRepositoryInterface;

class ReporteService
{
    public function __construct(
        private LibroRepositoryInterface $libros,
        private UsuarioRepositoryInterface $usuarios,
    ) {
    }

    public function generarReporte(int $topLibros = 10): array
    {
        return [
            'libros_mas_prestados' => $this->libros->masPrestados($topLibros),
            'usuarios_con_prestamos_vencidos' => $this->usuarios->conPrestamosVencidos(),
            'libros_sin_stock' => $this->libros->sinStock(),
        ];
    }
}
