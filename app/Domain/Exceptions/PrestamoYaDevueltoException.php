<?php

namespace App\Domain\Exceptions;

use RuntimeException;

class PrestamoYaDevueltoException extends RuntimeException
{
    public static function paraPrestamo(int $prestamoId): self
    {
        return new self("El préstamo (id={$prestamoId}) ya fue devuelto previamente.");
    }
}
