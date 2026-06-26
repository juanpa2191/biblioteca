<?php

namespace App\Domain\Enums;

final class EstadoUsuario
{
    public const ACTIVO = 'activo';
    public const INACTIVO = 'inactivo';

    public static function all(): array
    {
        return [self::ACTIVO, self::INACTIVO];
    }
}
