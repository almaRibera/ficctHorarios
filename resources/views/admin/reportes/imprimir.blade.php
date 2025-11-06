<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>{{ $titulo }}</title>
    <script src="https://cdn.tailwindcss.com"></script>
    <style>
        @media print {
            @page {
                margin: 0.5in;
                size: letter;
            }
            body {
                font-family: Arial, sans-serif;
                font-size: 12px;
                line-height: 1.4;
            }
            .no-print {
                display: none !important;
            }
            .break-before {
                page-break-before: always;
            }
            .break-after {
                page-break-after: always;
            }
            .break-inside-avoid {
                page-break-inside: avoid;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                font-size: 10px;
            }
            th, td {
                padding: 4px 6px;
                border: 1px solid #ddd;
            }
            th {
                background-color: #f8f9fa;
                font-weight: bold;
            }
        }
    </style>
</head>
<body class="bg-white text-gray-800">
    <!-- Encabezado del Reporte -->
    <div class="border-b-2 border-gray-300 pb-4 mb-6 break-inside-avoid">
        <div class="flex justify-between items-start">
            <div>
                <h1 class="text-2xl font-bold text-gray-800">{{ $titulo }}</h1>
                <p class="text-gray-600 text-sm">Generado el: {{ now()->format('d/m/Y H:i') }}</p>
                <p class="text-gray-600 text-sm">Generado por: {{ auth()->user()->name }}</p>
            </div>
            <div class="text-right">
                <div class="text-lg font-semibold">Sistema FICCT</div>
                <div class="text-sm text-gray-600">Facultad de Ingeniería en Ciencias de la Computación y Telecomunicaciones</div>
            </div>
        </div>
    </div>

    <!-- Contenido del Reporte (similar a resultado.blade.php pero optimizado para impresión) -->
    @if(isset($datos) && $datos->count() > 0)
        <!-- Asistencias -->
        @if($request->tipo_reporte === 'asistencias')
        <table class="break-inside-avoid">
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
        <!-- Materias -->
        @elseif($request->tipo_reporte === 'materias')
        <table class="break-inside-avoid">
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
        <!-- Bitácora -->
        @elseif($request->tipo_reporte === 'bitacora')
        <table class="break-inside-avoid">
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

        <!-- Otros reportes similares... -->
        @endif
    @else
    <div class="text-center py-8">
        <p class="text-gray-500">No hay datos para mostrar</p>
    </div>
    @endif

    <!-- Pie de página -->
    <div class="mt-8 pt-4 border-t border-gray-300 text-center text-xs text-gray-500 break-before">
        <p>Página 1 de 1 • Sistema de Gestión Académica FICCT</p>
        <p>Documento generado automáticamente</p>
    </div>

    <!-- Botón de impresión (solo visible en navegador) -->
    <div class="no-print fixed bottom-4 right-4">
        <button onclick="window.print()" 
                class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg shadow-lg">
            🖨️ Imprimir
        </button>
        <button onclick="window.close()" 
                class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg shadow-lg ml-2">
            ❌ Cerrar
        </button>
    </div>

    <script>
        // Auto-imprimir al cargar (opcional)
        // window.addEventListener('load', function() {
        //     window.print();
        // });
    </script>
</body>
</html>