@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Mis Horarios</h1>
        <p class="text-gray-600">Gestione los horarios de sus materias asignadas</p>
    </div>

    @if($materiasAsignadas->count() > 0)
    <!-- Materias Asignadas -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        @foreach($materiasAsignadas as $materiaAsignada)
        <div class="bg-white rounded-lg shadow p-6 border-l-4 
            {{ $materiaAsignada->horasPendientes() <= 0 ? 'border-green-500' : 'border-blue-500' }}">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">{{ $materiaAsignada->materia->sigla }}</h3>
                    <p class="text-gray-600 text-sm">{{ $materiaAsignada->materia->nombre }}</p>
                    <p class="text-gray-500 text-sm">Grupo: {{ $materiaAsignada->grupo->sigla_grupo }}</p>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                    {{ $materiaAsignada->horasPendientes() <= 0 ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ $materiaAsignada->horasAsignadas() }}/{{ $materiaAsignada->horas_semanales }}h
                </span>
            </div>
            
            <div class="mb-4">
                <div class="text-sm text-gray-600 mb-2">
                    <strong>Horas pendientes:</strong> {{ $materiaAsignada->horasPendientes() }} horas
                </div>
                <div class="w-full bg-gray-200 rounded-full h-2">
                    <div class="bg-blue-600 h-2 rounded-full" 
                         style="width: {{ ($materiaAsignada->horasAsignadas() / $materiaAsignada->horas_semanales) * 100 }}%"></div>
                </div>
            </div>

            <a href="{{ route('docente.horarios.create', $materiaAsignada) }}" 
               class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm flex items-center justify-center">
                📅 Asignar Horarios
            </a>
        </div>
        @endforeach
    </div>

    <!-- Horario Semanal Consolidado -->
    <div class="bg-white rounded-lg shadow p-6">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">Mi Horario Semanal</h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full border-collapse border border-gray-300">
                <thead>
                    <tr>
                        <th class="border border-gray-300 px-4 py-2 bg-gray-100">Hora</th>
                        <th class="border border-gray-300 px-4 py-2 bg-gray-100">Lunes</th>
                        <th class="border border-gray-300 px-4 py-2 bg-gray-100">Martes</th>
                        <th class="border border-gray-300 px-4 py-2 bg-gray-100">Miércoles</th>
                        <th class="border border-gray-300 px-4 py-2 bg-gray-100">Jueves</th>
                        <th class="border border-gray-300 px-4 py-2 bg-gray-100">Viernes</th>
                        <th class="border border-gray-300 px-4 py-2 bg-gray-100">Sábado</th>
                    </tr>
                </thead>
                <tbody>
                    @php
                        // Definir bloques horarios más flexibles
                        $bloquesHorarios = [
                            ['inicio' => '07:00', 'fin' => '08:30'],
                            ['inicio' => '08:30', 'fin' => '10:00'],
                            ['inicio' => '10:00', 'fin' => '11:30'],
                            ['inicio' => '11:30', 'fin' => '13:00'],
                            ['inicio' => '14:00', 'fin' => '15:30'],
                            ['inicio' => '15:30', 'fin' => '17:00'],
                            ['inicio' => '17:00', 'fin' => '18:30'],
                            ['inicio' => '18:30', 'fin' => '20:00'],
                        ];
                        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
                    @endphp
                    
                    @foreach($bloquesHorarios as $bloque)
                    <tr>
                        <td class="border border-gray-300 px-4 py-2 bg-gray-50 font-medium">
                            {{ $bloque['inicio'] }} - {{ $bloque['fin'] }}
                        </td>
                        @foreach($dias as $dia)
                        <td class="border border-gray-300 px-4 py-2 align-top" style="min-height: 80px;">
                            @php
                                $horariosEnCelda = [];
                                foreach($materiasAsignadas as $materiaAsignada) {
                                    foreach($materiaAsignada->horarios as $horario) {
                                        if ($horario->dia == $dia) {
                                            // Verificar si el horario cae dentro de este bloque
                                            $horaInicio = $horario->hora_inicio->format('H:i');
                                            $horaFin = $horario->hora_fin->format('H:i');
                                            
                                            // Si el horario se superpone con el bloque actual
                                            if ($horaInicio < $bloque['fin'] && $horaFin > $bloque['inicio']) {
                                                $horariosEnCelda[] = [
                                                    'horario' => $horario,
                                                    'materiaAsignada' => $materiaAsignada,
                                                    'horaInicio' => $horaInicio,
                                                    'horaFin' => $horaFin
                                                ];
                                            }
                                        }
                                    }
                                }
                            @endphp
                            
                            @foreach($horariosEnCelda as $item)
                                <div class="text-xs p-2 rounded bg-blue-100 border border-blue-200 mb-1">
                                    <div class="font-semibold">{{ $item['materiaAsignada']->materia->sigla }}</div>
                                    <div class="text-gray-600">{{ $item['materiaAsignada']->grupo->sigla_grupo }}</div>
                                    <div class="text-blue-600">{{ $item['horario']->aula->nombre }}</div>
                                    <div class="text-gray-500 text-xs">
                                        {{ $item['horaInicio'] }} - {{ $item['horaFin'] }}
                                    </div>
                                    <form action="{{ route('docente.horarios.destroy', $item['horario']) }}" method="POST" class="mt-1">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" 
                                                class="text-red-500 hover:text-red-700 text-xs"
                                                onclick="return confirm('¿Está seguro de eliminar este horario?')">
                                            🗑️ Eliminar
                                        </button>
                                    </form>
                                </div>
                            @endforeach
                        </td>
                        @endforeach
                    </tr>
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>

    <!-- Lista Detallada de Horarios -->
    <div class="bg-white rounded-lg shadow p-6 mt-6">
        <h3 class="text-xl font-semibold mb-4 text-gray-800">Lista Detallada de Horarios</h3>
        
        <div class="overflow-x-auto">
            <table class="min-w-full divide-y divide-gray-200">
                <thead class="bg-gray-50">
                    <tr>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Materia</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Grupo</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Día</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Horario</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Aula</th>
                        <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                    </tr>
                </thead>
                <tbody class="bg-white divide-y divide-gray-200">
                    @foreach($materiasAsignadas as $materiaAsignada)
                        @foreach($materiaAsignada->horarios as $horario)
                        <tr class="hover:bg-gray-50">
                            <td class="px-6 py-4 whitespace-nowrap">
                                <div class="text-sm font-medium text-gray-900">{{ $materiaAsignada->materia->sigla }}</div>
                                <div class="text-sm text-gray-500">{{ $materiaAsignada->materia->nombre }}</div>
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $materiaAsignada->grupo->sigla_grupo }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $horario->dia }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $horario->hora_inicio->format('H:i') }} - {{ $horario->hora_fin->format('H:i') }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-900">
                                {{ $horario->aula->nombre }}
                            </td>
                            <td class="px-6 py-4 whitespace-nowrap text-sm font-medium">
                                <form action="{{ route('docente.horarios.destroy', $horario) }}" method="POST">
                                    @csrf
                                    @method('DELETE')
                                    <button type="submit" 
                                            class="text-red-600 hover:text-red-900"
                                            onclick="return confirm('¿Está seguro de eliminar este horario?')">
                                        🗑️ Eliminar
                                    </button>
                                </form>
                            </td>
                        </tr>
                        @endforeach
                    @endforeach
                </tbody>
            </table>
        </div>
    </div>
    @else
    <!-- Sin materias asignadas -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
        <div class="text-yellow-600 text-4xl mb-4">📚</div>
        <h3 class="text-xl font-semibold text-yellow-800 mb-2">No tiene materias asignadas</h3>
        <p class="text-yellow-700">Contacte al administrador para que le asigne materias.</p>
    </div>
    @endif
</div>

<style>
/* Asegurar que las celdas tengan suficiente altura para mostrar múltiples horarios */
.border-gray-300 {
    min-height: 80px;
    vertical-align: top;
}
</style>
@endsection