@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Reporte de Asistencias</h1>
        <p class="text-gray-600">Control de asistencias docentes</p>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4 text-gray-800">Filtrar Asistencias</h3>
        <form action="{{ route('admin.asistencias.index') }}" method="GET" class="grid grid-cols-1 md:grid-cols-4 gap-4">
            <!-- Docente -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Docente</label>
                <select name="docente_id" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">Todos los docentes</option>
                    @foreach($docentes as $docente)
                        <option value="{{ $docente->id }}" {{ request('docente_id') == $docente->id ? 'selected' : '' }}>
                            {{ $docente->name }}
                        </option>
                    @endforeach
                </select>
            </div>

            <!-- Fecha -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Fecha</label>
                <input type="date" name="fecha" value="{{ request('fecha', today()->format('Y-m-d')) }}"
                       class="w-full border border-gray-300 rounded-lg px-3 py-2">
            </div>

            <!-- Estado -->
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Estado</label>
                <select name="estado" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    <option value="">Todos los estados</option>
                    <option value="presente" {{ request('estado') == 'presente' ? 'selected' : '' }}>Presente</option>
                    <option value="tardanza" {{ request('estado') == 'tardanza' ? 'selected' : '' }}>Tardanza</option>
                </select>
            </div>

            <!-- Botones -->
            <div class="flex items-end">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg w-full">
                    🔍 Filtrar
                </button>
            </div>
        </form>
    </div>

    <!-- Estadísticas -->
    <div class="grid grid-cols-1 md:grid-cols-4 gap-4 mb-6">
        <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-2 bg-blue-100 rounded-lg">
                    📊
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Total Registros</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $asistencias->total() }}</p>
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
                    <p class="text-xl font-semibold text-gray-900">{{ $asistencias->where('estado', 'presente')->count() }}</p>
                </div>
            </div>
        </div>

        <div class="bg-white p-4 rounded-lg shadow">
            <div class="flex items-center">
                <div class="p-2 bg-orange-100 rounded-lg">
                    ⏰
                </div>
                <div class="ml-3">
                    <p class="text-sm font-medium text-gray-600">Tardanzas</p>
                    <p class="text-xl font-semibold text-gray-900">{{ $asistencias->where('estado', 'tardanza')->count() }}</p>
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
                    <p class="text-xl font-semibold text-gray-900">-</p>
                </div>
            </div>
        </div>
    </div>

    <!-- Tabla de Asistencias -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Docente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Materia/Grupo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha/Hora</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Estado</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Evidencia</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($asistencias as $asistencia)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $asistencia->docente->name }}</div>
                            <div class="text-sm text-gray-500">{{ $asistencia->docente->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">
                                {{ $asistencia->horario->grupoMateria->materia->sigla }}
                            </div>
                            <div class="text-sm text-gray-500">
                                {{ $asistencia->horario->grupoMateria->grupo->sigla_grupo }} | 
                                {{ $asistencia->horario->aula->nombre }}
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm text-gray-900">{{ $asistencia->fecha->format('d/m/Y') }}</div>
                            <div class="text-sm text-gray-500">{{ $asistencia->hora_registro }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $asistencia->estado == 'presente' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                {{ $asistencia->estado == 'presente' ? 'Presente' : 'Tardanza' }}
                            </span>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            @if($asistencia->foto_evidencia)
                            <a href="{{ Storage::url('asistencias/' . $asistencia->foto_evidencia) }}" 
                               target="_blank" class="text-blue-600 hover:text-blue-900 text-sm">
                                👁️ Ver Foto
                            </a>
                            @else
                            <span class="text-gray-400 text-sm">Sin evidencia</span>
                            @endif
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.asistencias.show', $asistencia) }}" 
                               class="text-blue-600 hover:text-blue-900 mr-3">Ver Detalle</a>
                            <a href="{{ route('admin.asistencias.por-docente', $asistencia->docente) }}" 
                               class="text-green-600 hover:text-green-900">Ver Reporte</a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            No se encontraron registros de asistencia
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

    <!-- Acciones Adicionales -->
    <div class="mt-6 flex gap-4">
        <a href="{{ route('admin.asistencias.reporte-mensual') }}" 
           class="bg-purple-500 hover:bg-purple-600 text-white px-4 py-2 rounded-lg">
            📊 Reporte Mensual
        </a>
    </div>
</div>
@endsection