<?php

namespace Database\Seeders;

use App\Models\Rol;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        //Crea todos los roles que deben existir en el sistema
        Rol::insert([
            ['id' =>'1', 'nombre' => 'Super Administrador'],
            ['id' =>'2', 'nombre' => 'Administrador'],
            ['id' =>'3', 'nombre' => 'Cliente'],
            ['id' =>'4', 'nombre' => 'Vendedor']
        ]);

        //Crea el usuario puer Administrador que nucan puede ser borrado ni cambiado
        User::factory()->create([
            'id' => '1',
            'nombre' => 'Super Administrador',
            'email' => 'SA@kombitec.com.mx',
            'password' => Hash::make('12345678'), // siempre encriptar
            'activo' => true,
            'rol_id' => '1',
            'max_sessions' => '2'
        ]);
    }
}
