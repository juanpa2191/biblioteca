<?php

namespace App\Domain\Exceptions;

use RuntimeException;

class StockInsuficienteException extends RuntimeException
{
    public static function paraLibro(int $libroId, string $titulo): self
    {
        return new self("El libro '{$titulo}' (id={$libroId}) no tiene stock disponible.");
    }
}
