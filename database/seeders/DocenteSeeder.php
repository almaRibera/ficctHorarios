<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\User;
use App\Models\Docente;
use Illuminate\Support\Facades\Hash;

class DocenteSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $docentes = [
            [
                'name' => 'Dr. Carlos Mendoza',
                'email' => 'docente1@gmail.com',
                'codigo_docente' => 'DOC-001',
                'profesion' => 'Ingeniero de Sistemas'
            ],
            [
                'name' => 'MSc. Ana García',
                'email' => 'docente2@gmail.com',
                'codigo_docente' => 'DOC-002',
                'profesion' => 'Magister en Informática'
            ],
            [
                'name' => 'Lic. Roberto Silva',
                'email' => 'docente3@gmail.com',
                'codigo_docente' => 'DOC-003',
                'profesion' => 'Licenciado en Informática'
            ],
            [
                'name' => 'Dra. María López',
                'email' => 'docente4@gmail.com',
                'codigo_docente' => 'DOC-004',
                'profesion' => 'Doctora en Ciencias de la Computación'
            ],
            [
                'name' => 'Ing. Jorge Pérez',
                'email' => 'docente5@gmail.com',
                'codigo_docente' => 'DOC-005',
                'profesion' => 'Ingeniero en Sistemas'
            ],
            [
                'name' => 'MSc. Laura Torres',
                'email' => 'docente6@gmail.com',
                'codigo_docente' => 'DOC-006',
                'profesion' => 'Magister en Redes'
            ],
            [
                'name' => 'Lic. Diego Rojas',
                'email' => 'docente7@gmail.com',
                'codigo_docente' => 'DOC-007',
                'profesion' => 'Licenciado en Base de Datos'
            ],
            [
                'name' => 'Ing. Sofia Castro',
                'email' => 'docente8@gmail.com',
                'codigo_docente' => 'DOC-008',
                'profesion' => 'Ingeniera de Software'
            ],
            [
                'name' => 'MSc. Pablo Ruiz',
                'email' => 'docente9@gmail.com',
                'codigo_docente' => 'DOC-009',
                'profesion' => 'Magister en IA'
            ],
            [
                'name' => 'Dr. Elena Vargas',
                'email' => 'docente10@gmail.com',
                'codigo_docente' => 'DOC-010',
                'profesion' => 'Doctora en Seguridad Informática'
            ]
        ];

        foreach ($docentes as $docenteData) {
            // Crear usuario
            $user = User::create([
                'name' => $docenteData['name'],
                'email' => $docenteData['email'],
                'password' => Hash::make('12345678'),
                'rol' => 'docente'
            ]);

            // Crear docente
            Docente::create([
                'user_id' => $user->id,
                'codigo_docente' => $docenteData['codigo_docente'],
                'profesion' => $docenteData['profesion']
            ]);
        }
    }
}