<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use Illuminate\Support\Facades\Hash;

class AdminUserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Crear usuario administrador principal
        User::create([
            'name' => 'Karol Diaz',
            'email' => 'karol.jesusdiaz@gmail.com',
            'password' => Hash::make('Kjdl1989*'),
            'email_verified_at' => now(),
        ]);

        echo "Usuario administrador creado exitosamente:\n";
        echo "Email: karol.jesusdiaz@gmail.com\n";
        echo "Contraseña: Kjdl1989*\n";
    }
}
