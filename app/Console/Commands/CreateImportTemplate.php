<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class CreateImportTemplate extends Command
{
    protected $signature = 'template:create-import';
    protected $description = 'Crear plantilla para importación de docentes y horarios';

    public function handle()
    {
        $directory = storage_path('app/plantillas');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
            $this->info("Directorio creado: {$directory}");
        }

        $filePath = $directory . '/plantilla_docentes_horarios.csv';
        
        $headers = [
            'nombre_docente', 'email', 'password', 'codigo_docente', 'profesion',
            'codigo_grupo', 'sigla_grupo', 'sigla_materia', 'nombre_materia',
            'nivel', 'tipo_materia', 'horas_semanales', 'nombre_aula', 'tipo_aula',
            'piso_aula', 'capacidad_aula', 'dia', 'hora_inicio', 'hora_fin'
        ];

        $sampleData = [
            $headers,
            [
                'Juan Pérez', 'juan.perez@email.com', '123456', 'DOC001', 'Lic. Matemáticas',
                'GRP-INF-1A', 'INF-1A', 'MAT101', 'Cálculo I',
                '1', 'truncal', '6', 'A101', 'teorica',
                '1', '40', 'Lunes', '08:00', '10:00'
            ],
            [
                'María García', 'maria.garcia@email.com', '123456', 'DOC002', 'Ing. Sistemas',
                'GRP-INF-2B', 'INF-2B', 'SIS201', 'Base de Datos',
                '2', 'truncal', '6', 'LAB-201', 'laboratorio',
                '2', '25', 'Martes', '10:00', '12:00'
            ]
        ];

        $file = fopen($filePath, 'w');
        if ($file) {
            foreach ($sampleData as $row) {
                fputcsv($file, $row);
            }
            fclose($file);
            $this->info("Plantilla creada en: {$filePath}");
        } else {
            $this->error("No se pudo crear la plantilla");
        }
    }
}