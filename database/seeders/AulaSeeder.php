<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Aula;

class AulaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $aulas = [
            // Aulas Teóricas
            ['nombre' => 'A-101', 'piso' => '1', 'tipo' => 'teorica', 'estado' => 'disponible', 'capacidad' => 40],
            ['nombre' => 'A-102', 'piso' => '1', 'tipo' => 'teorica', 'estado' => 'disponible', 'capacidad' => 35],
            ['nombre' => 'A-201', 'piso' => '2', 'tipo' => 'teorica', 'estado' => 'disponible', 'capacidad' => 45],
            ['nombre' => 'A-202', 'piso' => '2', 'tipo' => 'teorica', 'estado' => 'disponible', 'capacidad' => 30],
            ['nombre' => 'A-301', 'piso' => '3', 'tipo' => 'teorica', 'estado' => 'disponible', 'capacidad' => 50],
            
            // Laboratorios
            ['nombre' => 'LAB-101', 'piso' => '1', 'tipo' => 'laboratorio', 'estado' => 'disponible', 'capacidad' => 25],
            ['nombre' => 'LAB-102', 'piso' => '1', 'tipo' => 'laboratorio', 'estado' => 'disponible', 'capacidad' => 20],
            ['nombre' => 'LAB-201', 'piso' => '2', 'tipo' => 'laboratorio', 'estado' => 'disponible', 'capacidad' => 30],
            ['nombre' => 'LAB-301', 'piso' => '3', 'tipo' => 'laboratorio', 'estado' => 'mantenimiento', 'capacidad' => 15],
            ['nombre' => 'LAB-302', 'piso' => '3', 'tipo' => 'laboratorio', 'estado' => 'disponible', 'capacidad' => 20],
        ];

        foreach ($aulas as $aula) {
            Aula::create($aula);
        }
    }
}