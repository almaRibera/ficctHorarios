@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">{{ $titulo }}</h1>
                <p class="text-gray-600">Generado el: {{ now()->format('d/m/Y H:i') }}</p>
            </div>
            <div class="flex gap-2">
                <button onclick="window.print()" 
                        class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center">
                    🖨️ Imprimir
                </button>
                <a href="{{ route('admin.reportes.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    ↩️ Volver
                </a>
            </div>
        </div>
    </div>

    <!-- Estadísticas Rápidas -->
    @if($datos->count() > 0)
    <div class="bg-white rounded-lg shadow p-6 mb-6 print:hidden">
        <h3 class="text-lg font-semibold mb-4 text-gray-800">Resumen del Reporte</h3>
        <div class="grid grid-cols-2 md:grid-cols-4 gap-4">
            <div class="text-center">
                <div class="text-2xl font-bold text-blue-600">{{ $datos->count() }}</div>
                <div class="text-sm text-gray-600">Total Registros</div>
            </div>
            
            @if($tipoReporte === 'asistencias')
            <div class="text-center">
                <div class="text-2xl font-bold text-green-600">{{ $datos->where('estado', 'presente')->count() }}</div>
                <div class="text-sm text-gray-600">Presentes</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-orange-600">{{ $datos->where('estado', 'tardanza')->count() }}</div>
                <div class="text-sm text-gray-600">Tardanzas</div>
            </div>
            <div class="text-center">
                <div class="text-2xl font-bold text-gray-600">{{ $datos->unique('docente_id')->count() }}</div>
                <div class="text-sm text-gray-600">Docentes</div>
            </div>
            @endif
        </div>
    </div>
    @endif

    <!-- Contenido del Reporte -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        @if($datos->count() > 0)
            <!-- Asistencias -->
            @if($tipoReporte === 'asistencias')
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Docente</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Materia/Grupo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aula</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Horario</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registro</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($datos as $asistencia)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $asistencia->fecha->format('d/m/Y') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $asistencia->docente->name }}</div>
                                <div class="text-sm text-gray-500">{{ $asistencia->docente->email }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">
                                    {{ $asistencia->horario->grupoMateria->materia->sigla }}
                                </div>
                                <div class="text-sm text-gray-500">
                                    {{ $asistencia->horario->grupoMateria->grupo->sigla_grupo }}
                                </div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $asistencia->horario->aula->nombre }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $asistencia->horario->hora_inicio->format('H:i') }} - {{ $asistencia->horario->hora_fin->format('H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $asistencia->hora_registro }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $asistencia->estado == 'presente' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                    {{ $asistencia->estado == 'presente' ? 'Presente' : 'Tardanza' }}
                                </span>
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Bitácora -->
            @elseif($tipoReporte === 'bitacora')
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha/Hora</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Usuario</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acción Realizada</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($datos as $bitacora)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $bitacora->fecha_y_hora->format('d/m/Y H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $bitacora->user->name }}</div>
                                <div class="text-sm text-gray-500">{{ $bitacora->user->email }}</div>
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $bitacora->accion_realizada }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Materias -->
            @elseif($tipoReporte === 'materias')
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sigla</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nivel</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grupos</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($datos as $materia)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $materia->sigla }}
                            </td>
                            <td class="px-6 py-4 text-sm text-gray-900">
                                {{ $materia->nombre }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                Nivel {{ $materia->nivel }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $materia->tipo == 'truncal' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ $materia->tipo_completo }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $materia->grupos_materia_count }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Aulas -->
            @elseif($tipoReporte === 'aulas')
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Piso</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tipo</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Capacidad</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($datos as $aula)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $aula->nombre }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                Piso {{ $aula->piso }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $aula->tipo == 'teorica' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                    {{ $aula->tipo_completo }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $aula->estado == 'disponible' ? 'bg-green-100 text-green-800' : 
                                       ($aula->estado == 'ocupada' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                    {{ $aula->estado_texto }}
                                </span>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $aula->capacidad ?? 'N/A' }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Docentes -->
            @elseif($tipoReporte === 'docentes')
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Email</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Materias Asignadas</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Horarios</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registro</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($datos as $docente)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $docente->name }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $docente->email }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $docente->materias_asignadas_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $docente->horarios_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $docente->created_at->format('d/m/Y') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <!-- Grupos -->
            @elseif($tipoReporte === 'grupos')
            <div class="overflow-x-auto">
                <table class="min-w-full divide-y divide-gray-200">
                    <thead class="bg-gray-50">
                        <tr>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Sigla</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Código</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Materias</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Docentes</th>
                            <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Registro</th>
                        </tr>
                    </thead>
                    <tbody class="bg-white divide-y divide-gray-200">
                        @foreach($datos as $grupo)
                        <tr>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                {{ $grupo->sigla_grupo }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $grupo->codigo_grupo }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $grupo->materias_asignadas_count }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $grupo->materiasAsignadas->unique('docente_id')->count() }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $grupo->created_at->format('d/m/Y') }}
                            </td>
                        </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            @endif
        @else
        <div class="text-center py-12">
            <div class="text-gray-400 text-4xl mb-4">📊</div>
            <h3 class="text-lg font-semibold text-gray-600 mb-2">No hay datos para mostrar</h3>
            <p class="text-gray-500">No se encontraron registros con los criterios seleccionados.</p>
        </div>
        @endif
    </div>

    <!-- Información del Pie -->
    @if($datos->count() > 0)
    <div class="mt-4 text-center text-sm text-gray-500 print:mt-8">
        <p>Reporte generado por {{ auth()->user()->name }} el {{ now()->format('d/m/Y \a \l\a\s H:i') }}</p>
        <p>Sistema de Gestión Académica - FICCT</p>
    </div>
    @endif
</div>

<style>
@media print {
    .print\:hidden {
        display: none !important;
    }
    .print\:mt-8 {
        margin-top: 2rem !important;
    }
    body {
        font-size: 12px;
    }
    table {
        font-size: 10px;
    }
}
</style>
@endsection