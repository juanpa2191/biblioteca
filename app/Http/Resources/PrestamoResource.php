<?php

namespace App\Http\Resources;

use Illuminate\Http\Resources\Json\JsonResource;

class PrestamoResource extends JsonResource
{
    public function toArray($request)
    {
        return [
            'id' => $this->id,
            'usuario_id' => $this->usuario_id,
            'libro_id' => $this->libro_id,
            'fecha_prestamo' => $this->fecha_prestamo?->format('Y-m-d'),
            'fecha_devolucion_estimada' => $this->fecha_devolucion_estimada?->format('Y-m-d'),
            'fecha_devolucion_real' => $this->fecha_devolucion_real?->format('Y-m-d'),
            'estado' => $this->estado,
            'usuario' => new UsuarioResource($this->whenLoaded('usuario')),
            'libro' => new LibroResource($this->whenLoaded('libro')),
        ];
    }
}
