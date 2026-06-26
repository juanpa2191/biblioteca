<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class AutorResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'nombre' => $this->nombre,
            'apellido' => $this->apellido,
            'nombre_completo' => $this->nombre_completo,
            'fecha_nacimiento' => $this->fecha_nacimiento?->format('Y-m-d'),
            'nacionalidad' => $this->nacionalidad,
            'biografia' => $this->biografia,
            'orden_autor' => $this->whenPivotLoaded('autor_libro', fn () => $this->pivot->orden_autor),
        ];
    }
}
