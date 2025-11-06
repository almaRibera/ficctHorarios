@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Reporte Mensual de Asistencias</h1>
        <p class="text-gray-600">Resumen de asistencias por docente</p>
    </div>

    <!-- Filtros -->
    <div class="bg-white rounded-lg shadow p-6 mb-6">
        <h3 class="text-lg font-semibold mb-4 text-gray-800">Seleccionar Mes y Año</h3>
        <form action="{{ route('admin.asistencias.reporte-mensual') }}" method="GET" class="grid grid-cols-1 md:grid-cols-3 gap-4">
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Mes</label>
                <select name="mes" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    @for($i = 1; $i <= 12; $i++)
                        <option value="{{ $i }}" {{ $mes == $i ? 'selected' : '' }}>
                            {{ DateTime::createFromFormat('!m', $i)->format('F') }}
                        </option>
                    @endfor
                </select>
            </div>
            <div>
                <label class="block text-sm font-medium text-gray-700 mb-1">Año</label>
                <select name="anio" class="w-full border border-gray-300 rounded-lg px-3 py-2">
                    @for($i = date('Y') - 1; $i <= date('Y') + 1; $i++)
                        <option value="{{ $i }}" {{ $anio == $i ? 'selected' : '' }}>
                            {{ $i }}
                        </option>
                    @endfor
                </select>
            </div>
            <div class="flex items-end">
                <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg w-full">
                    📊 Generar Reporte
                </button>
            </div>
        </form>
    </div>

    <!-- Reporte Mensual -->
    <div class="bg-white rounded-lg shadow overflow-hidden">
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Docente</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Total Clases</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Presentes</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tardanzas</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">% Asistencia</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @forelse($reporte as $item)
                    <tr class="hover:bg-gray-50">
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="text-sm font-medium text-gray-900">{{ $item['docente']->name }}</div>
                            <div class="text-sm text-gray-500">{{ $item['docente']->email }}</div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $item['total_clases'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $item['presentes'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                            {{ $item['tardanzas'] }}
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap">
                            <div class="flex items-center">
                                <div class="w-16 bg-gray-200 rounded-full h-2 mr-2">
                                    <div class="bg-green-600 h-2 rounded-full" 
                                         style="width: {{ min($item['porcentaje_asistencia'], 100) }}%"></div>
                                </div>
                                <span class="text-sm font-medium 
                                    {{ $item['porcentaje_asistencia'] >= 90 ? 'text-green-600' : 
                                       ($item['porcentaje_asistencia'] >= 70 ? 'text-yellow-600' : 'text-red-600') }}">
                                    {{ number_format($item['porcentaje_asistencia'], 1) }}%
                                </span>
                            </div>
                        </td>
                        <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                            <a href="{{ route('admin.asistencias.por-docente', $item['docente']) }}?fecha_inicio={{ $anio }}-{{ str_pad($mes, 2, '0', STR_PAD_LEFT) }}-01&fecha_fin={{ $anio }}-{{ str_pad($mes, 2, '0', STR_PAD_LEFT) }}-31" 
                               class="text-blue-600 hover:text-blue-900">
                                Ver Detalle
                            </a>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                            No hay datos de asistencia para el período seleccionado
                        </td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
    </div>

    <!-- Resumen General -->
    @if(count($reporte) > 0)
    <div class="mt-6 grid grid-cols-1 md:grid-cols-3 gap-4">
        <div class="bg-white rounded-lg shadow p-4">
            <h3 class="text-lg font-semibold text-gray-800 mb-2">Resumen General</h3>
            <div class="space-y-2 text-sm">
                <div class="flex justify-between">
                    <span class="text-gray-600">Total Docentes:</span>
                    <span class="font-medium">{{ count($reporte) }}</span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Promedio Asistencia:</span>
                    <span class="font-medium">
                        {{ number_format(collect($reporte)->avg('porcentaje_asistencia'), 1) }}%
                    </span>
                </div>
                <div class="flex justify-between">
                    <span class="text-gray-600">Mejor Asistencia:</span>
                    <span class="font-medium text-green-600">
                        {{ number_format(collect($reporte)->max('porcentaje_asistencia'), 1) }}%
                    </span>
                </div>
            </div>
        </div>
    </div>
    @endif
</div>
@endsection