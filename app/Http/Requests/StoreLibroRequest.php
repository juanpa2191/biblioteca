<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreLibroRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'titulo' => ['required', 'string', 'max:255'],
            'isbn' => ['required', 'string', 'max:20', 'unique:libros,isbn'],
            'anio_publicacion' => ['required', 'integer', 'between:1450,2100'],
            'numero_paginas' => ['nullable', 'integer', 'min:1'],
            'descripcion' => ['nullable', 'string'],
            'stock_disponible' => ['required', 'integer', 'min:0'],
            'autor_ids' => ['required', 'array', 'min:1'],
            'autor_ids.*' => ['integer', 'distinct', 'exists:autores,id'],
        ];
    }

    public function messages()
    {
        return [
            'autor_ids.required' => 'Debe asociar al menos un autor al libro.',
            'autor_ids.*.exists' => 'Uno de los autores enviados no existe.',
            'isbn.unique' => 'Ya existe un libro con ese ISBN.',
        ];
    }
}
