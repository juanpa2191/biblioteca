<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class UpdateAutorRequest extends FormRequest
{
    public function authorize()
    {
        return true;
    }

    public function rules()
    {
        return [
            'nombre' => ['sometimes', 'required', 'string', 'max:100'],
            'apellido' => ['sometimes', 'required', 'string', 'max:100'],
            'fecha_nacimiento' => ['sometimes', 'nullable', 'date', 'before:today'],
            'nacionalidad' => ['sometimes', 'nullable', 'string', 'max:80'],
            'biografia' => ['sometimes', 'nullable', 'string'],
        ];
    }
}
