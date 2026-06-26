<?php

namespace App\Models;

use App\Models\Pivots\AutorLibro;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;

class Libro extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'libros';

    protected $fillable = [
        'titulo',
        'isbn',
        'anio_publicacion',
        'numero_paginas',
        'descripcion',
        'stock_disponible',
    ];

    protected $casts = [
        'anio_publicacion' => 'integer',
        'numero_paginas' => 'integer',
        'stock_disponible' => 'integer',
    ];

    public function autores(): BelongsToMany
    {
        return $this->belongsToMany(Autor::class, 'autor_libro', 'libro_id', 'autor_id')
            ->using(AutorLibro::class)
            ->withPivot('orden_autor')
            ->withTimestamps()
            ->orderBy('autor_libro.orden_autor');
    }

    public function prestamos(): HasMany
    {
        return $this->hasMany(Prestamo::class, 'libro_id');
    }

    public function scopeDisponibles(Builder $query): Builder
    {
        return $query->where('stock_disponible', '>', 0);
    }

    public function scopePorAnio(Builder $query, int $anio): Builder
    {
        return $query->where('anio_publicacion', $anio);
    }

    public function scopePorAutor(Builder $query, int $autorId): Builder
    {
        return $query->whereHas('autores', function (Builder $q) use ($autorId) {
            $q->where('autores.id', $autorId);
        });
    }

    public function scopePorTitulo(Builder $query, string $titulo): Builder
    {
        return $query->where('titulo', 'ILIKE', "%{$titulo}%");
    }
}
