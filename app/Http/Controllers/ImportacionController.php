<?php

namespace App\Http\Controllers;

use App\Imports\DocentesHorariosCsvImport;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class ImportacionController extends Controller
{
    public function index()
    {
        return view('admin.importacion.index');
    }

    public function importar(Request $request)
    {
        $request->validate([
            'archivo' => 'required|file|mimes:csv,txt|max:10240' // 10MB
        ]);

        try {
            DB::beginTransaction();

            // Guardar archivo temporalmente
            $filePath = $request->file('archivo')->getRealPath();
            
            $import = new DocentesHorariosCsvImport();
            $import->import($filePath);

            DB::commit();

            $resultado = [
                'success' => true,
                'importados' => $import->getImportedCount(),
                'omitidos' => $import->getSkippedCount(),
                'errores' => $import->getErrors()
            ];

            return redirect()->route('admin.importacion.index')
                ->with('resultado', $resultado);

        } catch (\Exception $e) {
            DB::rollBack();
            
            return redirect()->route('admin.importacion.index')
                ->with('error', 'Error durante la importación: ' . $e->getMessage());
        }
    }

    public function descargarPlantilla()
    {
        $filePath = storage_path('app/plantillas/plantilla_docentes_horarios.csv');
        
        if (!file_exists($filePath)) {
            $this->crearPlantillaCsv();
        }

        return response()->download($filePath, 'plantilla_docentes_horarios.csv');
    }

    private function crearPlantillaCsv()
    {
        // Crear directorio si no existe
        $directory = storage_path('app/plantillas');
        if (!file_exists($directory)) {
            mkdir($directory, 0755, true);
        }

        $filePath = $directory . '/plantilla_docentes_horarios.csv';
        
        $headers = [
            'nombre_docente', 'email', 'password', 'codigo_docente', 'profesion',
            'codigo_grupo', 'sigla_grupo', 'sigla_materia', 'nombre_materia',
            'nivel', 'tipo_materia', 'horas_semanales', 'nombre_aula', 'tipo_aula',
            'piso_aula', 'capacidad_aula', 'dia', 'hora_inicio', 'hora_fin'
        ];

        $sampleData = [
            // Headers
            $headers,
            // Datos de ejemplo 1
            [
                'Juan Pérez', 'juan.perez@email.com', '123456', 'DOC001', 'Lic. Matemáticas',
                'GRP-INF-1A', 'INF-1A', 'MAT101', 'Cálculo I',
                '1', 'truncal', '6', 'A101', 'teorica',
                '1', '40', 'Lunes', '08:00', '10:00'
            ],
            // Datos de ejemplo 2
            [
                'Juan Pérez', 'juan.perez@email.com', '123456', 'DOC001', 'Lic. Matemáticas',
                'GRP-INF-1A', 'INF-1A', 'MAT101', 'Cálculo I',
                '1', 'truncal', '6', 'A101', 'teorica',
                '1', '40', 'Miércoles', '08:00', '10:00'
            ],
            // Datos de ejemplo 3
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
        }
    }

    public function verEjemplo()
    {
        $ejemplo = [
            'headers' => [
                'nombre_docente', 'email', 'password', 'codigo_docente', 'profesion',
                'codigo_grupo', 'sigla_grupo', 'sigla_materia', 'nombre_materia',
                'nivel', 'tipo_materia', 'horas_semanales', 'nombre_aula', 'tipo_aula',
                'piso_aula', 'capacidad_aula', 'dia', 'hora_inicio', 'hora_fin'
            ],
            'ejemplos' => [
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
            ]
        ];

        return view('admin.importacion.ejemplo', compact('ejemplo'));
    }
}