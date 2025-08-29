<?php

namespace Database\Seeders;

use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        User::factory()->create([
            'id' => '1',
            'nombre' => 'Super Administrador',
            'email' => 'SA@kombitec.com.mx',
            'password' => Hash::make('12345678'), // siempre encriptar
            'activo' => true,
            'rol_id' => '1'
        ]);
    }
}
