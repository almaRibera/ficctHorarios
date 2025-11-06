<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use App\Models\Materia;

class MateriaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $materias = [
            // Materias Troncales - Nivel 1
            ['sigla' => 'INF-110', 'nombre' => 'Introducción a la Informática', 'nivel' => 1, 'tipo' => 'truncal'],
            ['sigla' => 'MAT-115', 'nombre' => 'Matemática Discreta', 'nivel' => 1, 'tipo' => 'truncal'],
            ['sigla' => 'FIS-120', 'nombre' => 'Física General', 'nivel' => 1, 'tipo' => 'truncal'],
            
            // Materias Troncales - Nivel 2
            ['sigla' => 'INF-111', 'nombre' => 'Programación I', 'nivel' => 2, 'tipo' => 'truncal'],
            ['sigla' => 'MAT-116', 'nombre' => 'Álgebra Lineal', 'nivel' => 2, 'tipo' => 'truncal'],
            ['sigla' => 'INF-112', 'nombre' => 'Estructuras de Datos', 'nivel' => 2, 'tipo' => 'truncal'],
            
            // Materias Electivas
            ['sigla' => 'INF-210', 'nombre' => 'Programación Web', 'nivel' => 3, 'tipo' => 'electiva'],
            ['sigla' => 'INF-211', 'nombre' => 'Bases de Datos', 'nivel' => 3, 'tipo' => 'electiva'],
            ['sigla' => 'INF-212', 'nombre' => 'Redes de Computadoras', 'nivel' => 4, 'tipo' => 'electiva'],
            ['sigla' => 'INF-213', 'nombre' => 'Inteligencia Artificial', 'nivel' => 4, 'tipo' => 'electiva'],
        ];

        foreach ($materias as $materia) {
            Materia::create($materia);
        }
    }
}