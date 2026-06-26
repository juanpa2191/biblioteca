<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class StoreAutorRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nombre' => ['required', 'string', 'max:100'],
            'apellido' => ['required', 'string', 'max:100'],
            'fecha_nacimiento' => ['nullable', 'date', 'before:today'],
            'nacionalidad' => ['nullable', 'string', 'max:80'],
            'biografia' => ['nullable', 'string'],
        ];
    }
}
