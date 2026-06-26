<?php

namespace Database\Seeders;

use App\Models\Autor;
use App\Models\Libro;
use Illuminate\Database\Seeder;

class LibroSeeder extends Seeder
{
    public function run()
    {
        $autores = Autor::all();

        if ($autores->isEmpty()) {
            $this->command->warn('LibroSeeder: no hay autores. Corre AutorSeeder primero.');
            return;
        }

        Libro::factory()->count(20)->create()->each(function (Libro $libro) use ($autores) {
            $cantidadAutores = rand(1, min(3, $autores->count()));
            $seleccionados = $autores->random($cantidadAutores)->values();

            $sincronizar = [];
            foreach ($seleccionados as $orden => $autor) {
                $sincronizar[$autor->id] = ['orden_autor' => $orden + 1];
            }

            $libro->autores()->sync($sincronizar);
        });
    }
}
