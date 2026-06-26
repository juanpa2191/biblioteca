<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    public function run()
    {
        $this->call([
            UserSeeder::class,
            AutorSeeder::class,
            LibroSeeder::class,
            UsuarioSeeder::class,
            PrestamoSeeder::class,
        ]);
    }
}
