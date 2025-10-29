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
        // Crear usuario Administrador
        User::create([
            'name' => 'Administrador',
            'email' => 'admin@gmail.com',
            'password' => Hash::make('12345678'),
            'rol' => 'admin',
            'email_verified_at' => now(),
        ]);

        // Opcional: Crear un usuario docente Ejemplo
        User::create([
            'name' => 'Docente Ejemplo',
            'email' => 'docente@gmail.com',
            'password' => Hash::make('12345678'),
            'rol' => 'docente',
            'email_verified_at' => now(),
        ]);

        $this->command->info('Usuarios de prueba creados exitosamente!');
        $this->command->info('Admin: admin@gmail.com / 12345678');
        $this->command->info('Docente: docente@gmail.com / 12345678');
    }
}
