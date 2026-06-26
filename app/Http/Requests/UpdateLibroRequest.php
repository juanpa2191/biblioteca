<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;
use Illuminate\Validation\Rule;

class UpdateLibroRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        $libroParam = $this->route('libro');
        $libroId = is_object($libroParam) ? $libroParam->id : $libroParam;

        return [
            'titulo' => ['sometimes', 'required', 'string', 'max:255'],
            'isbn' => [
                'sometimes', 'required', 'string', 'max:20',
                Rule::unique('libros', 'isbn')->ignore($libroId),
            ],
            'anio_publicacion' => ['sometimes', 'required', 'integer', 'between:1450,2100'],
            'numero_paginas' => ['sometimes', 'nullable', 'integer', 'min:1'],
            'descripcion' => ['sometimes', 'nullable', 'string'],
            'stock_disponible' => ['sometimes', 'required', 'integer', 'min:0'],
            'autor_ids' => ['sometimes', 'array', 'min:1'],
            'autor_ids.*' => ['integer', 'distinct', 'exists:autores,id'],
        ];
    }
}
