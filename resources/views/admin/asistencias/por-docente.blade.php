@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Asistencias por Docente</h1>
        <p class="text-gray-600">Reporte detallado de asistencias por docente</p>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4 text-gray-800">Filtrar Reporte</h3>
        <form action="{{ route('admin.asistencias.por-docente') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Docente -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Docente</label>
                <select name="docente_id" class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
                    @foreach($docentes as $d)
                        <option value="{{ $d->id }}" {{ $docente && $docente->id == $d->id ? 'selected' : '' }}>
                            {{ $d->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Fecha Inicio -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Inicio</label>
                <input type="date" name="fecha_inicio" value="{{ $fechaInicio }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Fecha Fin -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha Fin</label>
                <input type="date" name="fecha_fin" value="{{ $fechaFin }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500">
            </div>

            <!-- Botones -->
            <div class="flex items-end gap-2">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg flex-1">
                    🔍 Generar Reporte
                </button>
            </div>
        </form>
    </div>

    @if($docente)
    <!-- Información del Docente -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <div class="flex items-center justify-between">
            <div>
                <h2 class="text-2xl font-bold text-gray-800">{{ $docente->name }}</h2>
                <p class="text-gray-600">{{ $docente->email }}</p>
            </div>
            <div class="text-right">
                <div class="text-3xl font-bold text-blue-600">{{ number_format($porcentajeAsistencia, 1) }}%</div>
                <div class="text-sm text-gray-600">Porcentaje de Asistencia</div>
            </div>
        </div>
    </div>

    <!-- Estadísticas del Docente -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    📚
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Total Clases</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $totalClases }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-2 bg-green-100 rounded-lg">
                    ✅
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Presentes</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $presentes }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-2 bg-yellow-100 rounded-lg">
                    ⏰
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Tardanzas</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $tardanzas }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-2 bg-red-100 rounded-lg">
                    ❌
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Faltas</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $faltas }}</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Asistencias del Docente -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Materia/Grupo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Hora Registro</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aula</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($asistencias as $asistencia)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $asistencia->horario->grupoMateria->materia->sigla }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $asistencia->horario->grupoMateria->grupo->sigla_grupo }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $asistencia->fecha_clase->format('d/m/Y') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $asistencia->hora_registro->format('H:i') }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $asistencia->estado == 'presente' ? 'bg-green-100 text-green-800' : 
                                   ($asistencia->estado == 'tardanza' ? 'bg-yellow-100 text-yellow-800' : 'bg-red-100 text-red-800') }}">
                                {{ ucfirst($asistencia->estado) }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $asistencia->horario->aula->nombre }}
                        </td>
                      
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            No se encontraron registros de asistencia para este docente en el período seleccionado
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>

        <!-- Paginación -->
        @if($asistencias->hasPages())
        <div class="bg-white px-4 py-3 border-t border-gray-200 sm:px-6">
            {{ $asistencias->links() }}
        </div>
        @endif
    </div>
    @endif
</div>
@endsection