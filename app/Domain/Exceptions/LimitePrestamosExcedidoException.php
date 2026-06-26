<?php

namespace App\Domain\Exceptions;

use RuntimeException;

class LimitePrestamosExcedidoException extends RuntimeException
{
    public const LIMITE = 3;

    public static function paraUsuario(int $usuarioId, int $actuales): self
    {
        return new self(
            "El usuario (id={$usuarioId}) ya tiene {$actuales} préstamos activos. "
            . 'El límite permitido es ' . self::LIMITE . '.'
        );
    }
}
