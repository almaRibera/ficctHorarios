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
                        $horas = [
                            '07:00-08:30', '08:30-10:00', '10:00-11:30', 
                            '11:30-13:00', '14:00-15:30', '15:30-17:00', 
                            '17:00-18:30', '18:30-20:00'
                        ];
                        $dias = ['Lunes', 'Martes', 'Miércoles', 'Jueves', 'Viernes', 'Sábado'];
                    @endphp
                    
                    @foreach($horas as $hora)
                    <tr>
                        <td class="border border-gray-300 px-4 py-2 bg-gray-50 font-medium">{{ $hora }}</td>
                        @foreach($dias as $dia)
                        <td class="border border-gray-300 px-4 py-2">
                            @foreach($materiasAsignadas as $materiaAsignada)
                                @foreach($materiaAsignada->horarios as $horario)
                                    @if($horario->dia == $dia && 
                                        $horario->hora_inicio->format('H:i') == explode('-', $hora)[0])
                                        <div class="text-xs p-2 rounded bg-blue-100 border border-blue-200 mb-1">
                                            <div class="font-semibold">{{ $materiaAsignada->materia->sigla }}</div>
                                            <div>{{ $materiaAsignada->grupo->sigla_grupo }}</div>
                                            <div class="text-blue-600">{{ $horario->aula->nombre }}</div>
                                            <form action="{{ route('docente.horarios.destroy', $horario) }}" method="POST" class="mt-1">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="text-red-500 hover:text-red-700 text-xs">
                                                    🗑️
                                                </button>
                                            </form>
                                        </div>
                                    @endif
                                @endforeach
                            @endforeach
                        </td>
                        @endforeach
                    </tr>
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
@endsection