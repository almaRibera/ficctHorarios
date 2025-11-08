<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>{{ $titulo }}</title>
    <style>
        body { font-family: Arial, sans-serif; font-size: 12px; line-height: 1.4; }
        table { width: 100%; border-collapse: collapse; margin: 10px 0; }
        th, td { border: 1px solid #ddd; padding: 6px; text-align: left; }
        th { background-color: #f8f9fa; font-weight: bold; }
        h1 { color: #333; text-align: center; margin-bottom: 5px; }
        .header-info { text-align: center; margin-bottom: 20px; padding-bottom: 10px; border-bottom: 2px solid #333; }
        .footer { margin-top: 20px; padding-top: 10px; border-top: 1px solid #ddd; text-align: center; font-size: 10px; color: #666; }
        .page-break { page-break-after: always; }
    </style>
</head>
<body>
    <div class="header-info">
        <h1>{{ $titulo }}</h1>
        <p><strong>Generado el:</strong> {{ now()->format('d/m/Y H:i') }}</p>
        <p><strong>Generado por:</strong> {{ auth()->user()->name }}</p>
        <p><strong>Sistema FICCT - Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones</strong></p>
    </div>

    @if($datos->count() > 0)
        <!-- Asistencias -->
        @if($tipoReporte === 'asistencias')
        <table>
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Docente</th>
                    <th>Materia</th>
                    <th>Grupo</th>
                    <th>Aula</th>
                    <th>Horario</th>
                    <th>Registro</th>
                    <th>Estado</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datos as $asistencia)
                <tr>
                    <td>{{ $asistencia->fecha->format('d/m/Y') }}</td>
                    <td>{{ $asistencia->docente->name }}</td>
                    <td>{{ $asistencia->horario->grupoMateria->materia->sigla }}</td>
                    <td>{{ $asistencia->horario->grupoMateria->grupo->sigla_grupo }}</td>
                    <td>{{ $asistencia->horario->aula->nombre }}</td>
                    <td>{{ $asistencia->horario->hora_inicio->format('H:i') }}-{{ $asistencia->horario->hora_fin->format('H:i') }}</td>
                    <td>{{ $asistencia->hora_registro }}</td>
                    <td>{{ $asistencia->estado == 'presente' ? 'Presente' : 'Tardanza' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Bitácora -->
        @elseif($tipoReporte === 'bitacora')
        <table>
            <thead>
                <tr>
                    <th>Fecha/Hora</th>
                    <th>Usuario</th>
                    <th>Acción Realizada</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datos as $bitacora)
                <tr>
                    <td>{{ $bitacora->fecha_y_hora->format('d/m/Y H:i') }}</td>
                    <td>{{ $bitacora->user->name }}</td>
                    <td>{{ $bitacora->accion_realizada }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Materias -->
        @elseif($tipoReporte === 'materias')
        <table>
            <thead>
                <tr>
                    <th>Sigla</th>
                    <th>Nombre</th>
                    <th>Nivel</th>
                    <th>Tipo</th>
                    <th>Grupos</th>
                    <th>Docentes</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datos as $materia)
                <tr>
                    <td>{{ $materia->sigla }}</td>
                    <td>{{ $materia->nombre }}</td>
                    <td>Nivel {{ $materia->nivel }}</td>
                    <td>{{ $materia->tipo_completo }}</td>
                    <td>{{ $materia->grupos_materia_count }}</td>
                    <td>{{ $materia->gruposMateria->unique('docente_id')->count() }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Aulas -->
        @elseif($tipoReporte === 'aulas')
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Piso</th>
                    <th>Tipo</th>
                    <th>Estado</th>
                    <th>Capacidad</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datos as $aula)
                <tr>
                    <td>{{ $aula->nombre }}</td>
                    <td>Piso {{ $aula->piso }}</td>
                    <td>{{ $aula->tipo_completo }}</td>
                    <td>{{ $aula->estado_texto }}</td>
                    <td>{{ $aula->capacidad ?? 'N/A' }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Docentes -->
        @elseif($tipoReporte === 'docentes')
        <table>
            <thead>
                <tr>
                    <th>Nombre</th>
                    <th>Email</th>
                    <th>Materias Asignadas</th>
                    <th>Horarios</th>
                    <th>Registro</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datos as $docente)
                <tr>
                    <td>{{ $docente->name }}</td>
                    <td>{{ $docente->email }}</td>
                    <td>{{ $docente->materias_asignadas_count }}</td>
                    <td>{{ $docente->horarios_count ?? 0 }}</td>
                    <td>{{ $docente->created_at->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>

        <!-- Grupos -->
        @elseif($tipoReporte === 'grupos')
        <table>
            <thead>
                <tr>
                    <th>Sigla</th>
                    <th>Código</th>
                    <th>Materias</th>
                    <th>Docentes</th>
                    <th>Registro</th>
                </tr>
            </thead>
            <tbody>
                @foreach($datos as $grupo)
                <tr>
                    <td>{{ $grupo->sigla_grupo }}</td>
                    <td>{{ $grupo->codigo_grupo }}</td>
                    <td>{{ $grupo->materias_asignadas_count }}</td>
                    <td>{{ $grupo->materiasAsignadas->unique('docente_id')->count() }}</td>
                    <td>{{ $grupo->created_at->format('d/m/Y') }}</td>
                </tr>
                @endforeach
            </tbody>
        </table>
        @endif

        <div class="footer">
            <p>Página 1 de 1 • Sistema de Gestión Académica FICCT</p>
            <p>Documento generado automáticamente</p>
        </div>
    @else
        <p style="text-align: center; color: #666; font-style: italic;">No hay datos para mostrar</p>
    @endif
</body>
</html>