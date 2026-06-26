<?php

namespace App\Models\Pivots;

use Illuminate\Database\Eloquent\Relations\Pivot;

class AutorLibro extends Pivot
{
    protected $table = 'autor_libro';

    public $incrementing = true;

    protected $fillable = [
        'autor_id',
        'libro_id',
        'orden_autor',
    ];

    protected $casts = [
        'orden_autor' => 'integer',
    ];
}
