<?php

namespace App\Domain\Exceptions;

use RuntimeException;

class UsuarioInactivoException extends RuntimeException
{
    public static function paraUsuario(int $usuarioId): self
    {
        return new self("El usuario (id={$usuarioId}) está inactivo y no puede tomar préstamos.");
    }
}
