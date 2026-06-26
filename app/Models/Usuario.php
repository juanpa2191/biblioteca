<?php

namespace App\Models;

use App\Domain\Enums\EstadoUsuario;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Usuario extends Model
{
    use HasFactory;

    protected $table = 'usuarios';

    protected $fillable = [
        'nombre',
        'email',
        'telefono',
        'fecha_registro',
        'estado',
    ];

    protected $casts = [
        'fecha_registro' => 'date',
    ];

    public function prestamos(): HasMany
    {
        return $this->hasMany(Prestamo::class, 'usuario_id');
    }

    public function prestamosActivos(): HasMany
    {
        return $this->prestamos()->where('estado', \App\Domain\Enums\EstadoPrestamo::ACTIVO);
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('estado', EstadoUsuario::ACTIVO);
    }

    public function estaActivo(): bool
    {
        return $this->estado === EstadoUsuario::ACTIVO;
    }
}
