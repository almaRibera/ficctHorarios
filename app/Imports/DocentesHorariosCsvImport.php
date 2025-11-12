<?php

namespace App\Imports;

use App\Models\User;
use App\Models\Docente;
use App\Models\Grupo;
use App\Models\Materia;
use App\Models\Aula;
use App\Models\GrupoMateria;
use App\Models\HorarioDocente;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;
use Illuminate\Validation\Rule;

class DocentesHorariosCsvImport
{
    private $errors = [];
    private $importedCount = 0;
    private $skippedCount = 0;

    public function import($filePath)
    {
        if (!file_exists($filePath)) {
            throw new \Exception("El archivo no existe: " . $filePath);
        }

        $file = fopen($filePath, 'r');
        if (!$file) {
            throw new \Exception("No se pudo abrir el archivo: " . $filePath);
        }

        // Leer encabezados
        $headers = fgetcsv($file);
        if (!$headers) {
            fclose($file);
            throw new \Exception("El archivo CSV está vacío o no tiene formato correcto");
        }

        // Normalizar headers (remover BOM y espacios)
        $headers = array_map(function($header) {
            return trim($header, "\xEF\xBB\xBF \t\n\r\0\x0B");
        }, $headers);

        $lineNumber = 1;

        while (($row = fgetcsv($file)) !== FALSE) {
            $lineNumber++;

            // Saltar filas vacías
            if (empty(array_filter($row))) {
                continue;
            }

            // Combinar headers con datos
            if (count($headers) !== count($row)) {
                $this->errors[] = "Línea {$lineNumber}: Número de columnas no coincide con los encabezados";
                $this->skippedCount++;
                continue;
            }

            $rowData = array_combine($headers, $row);

            try {
                $this->processRow($rowData, $lineNumber);
            } catch (\Exception $e) {
                $this->errors[] = "Línea {$lineNumber}: " . $e->getMessage();
                $this->skippedCount++;
            }
        }

        fclose($file);
    }

    private function processRow($row, $lineNumber)
    {
        // Limpiar y validar datos
        $row = $this->cleanRowData($row);

        // Validar fila
        $validator = Validator::make($row, $this->rules(), $this->customValidationMessages());
        
        if ($validator->fails()) {
            throw new \Exception(implode(', ', $validator->errors()->all()));
        }

        // Procesar usuario/docente
        $user = $this->processUser($row);
        if (!$user) {
            throw new \Exception("No se pudo procesar el usuario");
        }

        // Procesar grupo, materia y horario
        $this->processGrupoMateriaHorario($row, $user);

        $this->importedCount++;
    }

    private function cleanRowData($row)
    {
        $cleaned = [];
        foreach ($row as $key => $value) {
            $cleaned[$key] = trim($value);
            
            // Convertir valores numéricos
            if (in_array($key, ['nivel', 'horas_semanales', 'capacidad_aula', 'piso_aula'])) {
                $cleaned[$key] = is_numeric($value) ? (int)$value : 0;
            }
            
            // Para modalidad, establecer presencial por defecto si está vacío
            if ($key === 'modalidad' && empty($value)) {
                $cleaned[$key] = 'presencial';
            }
        }
        return $cleaned;
    }

    private function processUser($row)
    {
        // Verificar si el usuario ya existe
        $user = User::where('email', $row['email'])->first();

        if (!$user) {
            // Crear nuevo usuario
            $user = User::create([
                'name' => $row['nombre_docente'],
                'email' => $row['email'],
                'password' => Hash::make($row['password']),
                'rol' => 'docente',
            ]);

            // Crear registro en docentes
            Docente::create([
                'user_id' => $user->id,
                'codigo_docente' => $row['codigo_docente'],
                'profesion' => $row['profesion'],
            ]);
        }

        return $user;
    }

  private function processGrupoMateriaHorario($row, $user)
{
    // Buscar o crear grupo
    $grupo = Grupo::firstOrCreate(
        ['codigo_grupo' => $row['codigo_grupo']],
        [
            'sigla_grupo' => $row['sigla_grupo'],
        ]
    );

    // Buscar o crear materia
    $materia = Materia::firstOrCreate(
        ['sigla' => $row['sigla_materia']],
        [
            'nombre' => $row['nombre_materia'],
            'nivel' => $row['nivel'],
            'tipo' => $row['tipo_materia'] ?? 'truncal',
        ]
    );

    // Buscar o crear aula (solo si es presencial y se proporciona aula)
    $aula = null;
    if ($row['modalidad'] === 'presencial' && !empty($row['nombre_aula'])) {
        $aula = Aula::firstOrCreate(
            ['nombre' => $row['nombre_aula']],
            [
                'piso' => $row['piso_aula'] ?? '1',
                'tipo' => $row['tipo_aula'] ?? 'teorica',
                'capacidad' => $row['capacidad_aula'] ?? 40,
                'estado' => 'disponible'
            ]
        );
    }

    // Crear o actualizar grupo_materia
    $grupoMateria = GrupoMateria::updateOrCreate(
        [
            'grupo_id' => $grupo->id,
            'materia_id' => $materia->id,
        ],
        [
            'docente_id' => $user->id,
            'horas_semanales' => $row['horas_semanales'] ?? 4,
        ]
    );

    // Verificar conflicto de horario antes de crear (solo para presencial con aula)
    if ($row['modalidad'] === 'presencial' && $aula) {
        if ($this->tieneConflictoHorario($aula->id, $row['dia'], $row['hora_inicio'], $row['hora_fin'])) {
            throw new \Exception("Conflicto de horario en el aula {$row['nombre_aula']} para el día {$row['dia']} de {$row['hora_inicio']} a {$row['hora_fin']}");
        }
    }

    // Verificar conflicto de docente
    if ($this->tieneConflictoDocente($user->id, $row['dia'], $row['hora_inicio'], $row['hora_fin'])) {
        throw new \Exception("El docente ya tiene una clase asignada en el día {$row['dia']} de {$row['hora_inicio']} a {$row['hora_fin']}");
    }

    // Preparar datos para crear el horario
    $horarioData = [
        'grupo_materia_id' => $grupoMateria->id,
        'dia' => $row['dia'],
        'hora_inicio' => $row['hora_inicio'],
        'hora_fin' => $row['hora_fin'],
        'modalidad' => $row['modalidad'],
    ];

    // Asignar aula_id solo si es presencial y existe aula
    if ($row['modalidad'] === 'presencial' && $aula) {
        $horarioData['aula_id'] = $aula->id;
    } else {
        $horarioData['aula_id'] = null; // Explícitamente null para virtual
    }

    // Agregar enlace virtual si es virtual y se proporcionó
    if ($row['modalidad'] === 'virtual' && !empty($row['enlace_virtual'])) {
        $horarioData['enlace_virtual'] = $row['enlace_virtual'];
    }

    // Crear horario docente
    HorarioDocente::create($horarioData);
}

    private function tieneConflictoHorario($aulaId, $dia, $horaInicio, $horaFin, $excluirId = null)
    {
        $query = HorarioDocente::where('aula_id', $aulaId)
            ->where('dia', $dia)
            ->where(function($q) use ($horaInicio, $horaFin) {
                $q->where(function($q2) use ($horaInicio, $horaFin) {
                    $q2->where('hora_inicio', '<=', $horaInicio)
                       ->where('hora_fin', '>', $horaInicio);
                })->orWhere(function($q2) use ($horaInicio, $horaFin) {
                    $q2->where('hora_inicio', '<', $horaFin)
                       ->where('hora_fin', '>=', $horaFin);
                })->orWhere(function($q2) use ($horaInicio, $horaFin) {
                    $q2->where('hora_inicio', '>=', $horaInicio)
                       ->where('hora_fin', '<=', $horaFin);
                });
            });

        if ($excluirId) {
            $query->where('id', '!=', $excluirId);
        }

        return $query->exists();
    }

    private function tieneConflictoDocente($docenteId, $dia, $horaInicio, $horaFin, $excluirId = null)
    {
        $query = HorarioDocente::whereHas('grupoMateria', function($q) use ($docenteId) {
                $q->where('docente_id', $docenteId);
            })
            ->where('dia', $dia)
            ->where(function($q) use ($horaInicio, $horaFin) {
                $q->where(function($q2) use ($horaInicio, $horaFin) {
                    $q2->where('hora_inicio', '<=', $horaInicio)
                       ->where('hora_fin', '>', $horaInicio);
                })->orWhere(function($q2) use ($horaInicio, $horaFin) {
                    $q2->where('hora_inicio', '<', $horaFin)
                       ->where('hora_fin', '>=', $horaFin);
                });
            });

        if ($excluirId) {
            $query->where('id', '!=', $excluirId);
        }

        return $query->exists();
    }

    public function rules(): array
    {
        return [
            'nombre_docente' => 'required|string|max:255',
            'email' => 'required|email|max:255',
            'password' => 'required|string|min:6',
            'codigo_docente' => 'required|string|max:50',
            'profesion' => 'required|string|max:255',
            'codigo_grupo' => 'required|string|max:50',
            'sigla_grupo' => 'required|string|max:20',
            'sigla_materia' => 'required|string|max:20',
            'nombre_materia' => 'required|string|max:255',
            'nivel' => 'required|integer|min:1|max:10',
            'tipo_materia' => ['nullable', Rule::in(['truncal', 'electiva'])],
            'horas_semanales' => 'nullable|integer|min:1|max:20',
            'nombre_aula' => 'nullable|string|max:50',
            'piso_aula' => ['nullable', Rule::in(['1', '2', '3', '4'])],
            'tipo_aula' => ['nullable', Rule::in(['teorica', 'laboratorio'])],
            'modalidad' => ['required', Rule::in(['presencial', 'virtual'])],
            'enlace_virtual' => 'nullable|required_if:modalidad,virtual|url',
            'dia' => ['required', Rule::in(['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'])],
            'hora_inicio' => 'required|date_format:H:i',
            'hora_fin' => 'required|date_format:H:i|after:hora_inicio',
        ];
    }

    public function customValidationMessages()
    {
        return [
            'hora_fin.after' => 'La hora fin debe ser posterior a la hora inicio',
            'dia.in' => 'El día debe ser: Lunes, Martes, Miércoles, Jueves, Viernes o Sábado',
            'modalidad.in' => 'La modalidad debe ser: presencial o virtual',
            'tipo_materia.in' => 'El tipo de materia debe ser: truncal o electiva',
            'tipo_aula.in' => 'El tipo de aula debe ser: teorica o laboratorio',
            'piso_aula.in' => 'El piso debe ser: 1, 2, 3 o 4',
            'email.email' => 'El correo debe tener un formato válido',
            'enlace_virtual.required_if' => 'El enlace virtual es requerido cuando la modalidad es virtual',
            'enlace_virtual.url' => 'El enlace virtual debe ser una URL válida',
            '*.required' => 'El campo :attribute es obligatorio',
        ];
    }

    public function getErrors()
    {
        return $this->errors;
    }

    public function getImportedCount()
    {
        return $this->importedCount;
    }

    public function getSkippedCount()
    {
        return $this->skippedCount;
    }
}