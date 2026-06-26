<?php

namespace App\Models;

use App\Domain\Enums\EstadoPrestamo;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class Prestamo extends Model
{
    use HasFactory;

    protected $table = 'prestamos';

    protected $fillable = [
        'usuario_id',
        'libro_id',
        'fecha_prestamo',
        'fecha_devolucion_estimada',
        'fecha_devolucion_real',
        'estado',
    ];

    protected $casts = [
        'fecha_prestamo' => 'date',
        'fecha_devolucion_estimada' => 'date',
        'fecha_devolucion_real' => 'date',
    ];

    public function usuario(): BelongsTo
    {
        return $this->belongsTo(Usuario::class, 'usuario_id');
    }

    public function libro(): BelongsTo
    {
        return $this->belongsTo(Libro::class, 'libro_id');
    }

    public function scopeActivos(Builder $query): Builder
    {
        return $query->where('estado', EstadoPrestamo::ACTIVO);
    }

    public function scopeVencidos(Builder $query): Builder
    {
        return $query->where('estado', EstadoPrestamo::VENCIDO);
    }

    public function scopeDevueltos(Builder $query): Builder
    {
        return $query->where('estado', EstadoPrestamo::DEVUELTO);
    }

    public function estaActivo(): bool
    {
        return $this->estado === EstadoPrestamo::ACTIVO;
    }

    public function estaVencido(): bool
    {
        return $this->estado === EstadoPrestamo::VENCIDO;
    }

    public function fueDevuelto(): bool
    {
        return $this->estado === EstadoPrestamo::DEVUELTO;
    }
}
