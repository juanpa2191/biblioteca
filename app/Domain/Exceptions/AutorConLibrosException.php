<?php

namespace App\Domain\Exceptions;

use RuntimeException;

class AutorConLibrosException extends RuntimeException
{
    public static function paraAutor(int $autorId, int $cantidadLibros): self
    {
        return new self(
            "No se puede eliminar el autor (id={$autorId}) porque tiene {$cantidadLibros} libro(s) asociado(s)."
        );
    }
}
