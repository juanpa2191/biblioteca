<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StorePrestamoRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'usuario_id' => ['required', 'integer', 'exists:usuarios,id'],
            'libro_id' => ['required', 'integer', 'exists:libros,id'],
            'fecha_prestamo' => ['nullable', 'date'],
        ];
    }

    public function messages()
    {
        return [
            'usuario_id.exists' => 'El usuario indicado no existe.',
            'libro_id.exists' => 'El libro indicado no existe.',
        ];
    }
}
