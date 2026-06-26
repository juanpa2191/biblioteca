<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    public function run()
    {
        User::updateOrCreate(
            ['email' => 'admin@biblioteca.test'],
            [
                'name' => 'Bibliotecario Admin',
                'password' => Hash::make('password123'),
            ]
        );
    }
}
