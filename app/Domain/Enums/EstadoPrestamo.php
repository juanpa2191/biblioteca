<?php

namespace App\Domain\Enums;

final class EstadoPrestamo
{
    public const ACTIVO = 'activo';
    public const DEVUELTO = 'devuelto';
    public const VENCIDO = 'vencido';

    public static function all(): array
    {
        return [self::ACTIVO, self::DEVUELTO, self::VENCIDO];
    }
}
