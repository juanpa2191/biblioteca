<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class LibroResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'titulo' => $this->titulo,
            'isbn' => $this->isbn,
            'anio_publicacion' => $this->anio_publicacion,
            'numero_paginas' => $this->numero_paginas,
            'descripcion' => $this->descripcion,
            'stock_disponible' => $this->stock_disponible,
            'disponible' => $this->stock_disponible > 0,
            'autores' => AutorResource::collection($this->whenLoaded('autores')),
            'created_at' => $this->created_at?->toIso8601String(),
            'updated_at' => $this->updated_at?->toIso8601String(),
        ];
    }
}
