@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <h1 class="text-3xl font-bold text-gray-800">Registro de Asistencia</h1>
        <p class="text-gray-600">Hoy: {{ $hoy->format('d/m/Y') }}</p>
    </div>

    @if($horariosHoy->count() > 0)
    <!-- Horarios del Día -->
    <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
        @foreach($horariosHoy as $horario)
        <div class="bg-white rounded-lg shadow p-6 border-l-4 
            {{ $horario->ya_registrado ? 'border-green-500' : 'border-blue-500' }}">
            <div class="flex justify-between items-start mb-3">
                <div>
                    <h3 class="text-lg font-semibold text-gray-800">{{ $horario->grupoMateria->materia->sigla }}</h3>
                    <p class="text-gray-600 text-sm">{{ $horario->grupoMateria->materia->nombre }}</p>
                    <p class="text-gray-500 text-sm">Grupo: {{ $horario->grupoMateria->grupo->sigla_grupo }}</p>
                </div>
                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                    {{ $horario->ya_registrado ? 'bg-green-100 text-green-800' : 'bg-yellow-100 text-yellow-800' }}">
                    {{ $horario->ya_registrado ? 'Registrado' : 'Pendiente' }}
                </span>
            </div>
            
            <div class="space-y-2 mb-4">
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Horario:</span>
                    <span class="font-medium">{{ $horario->hora_inicio->format('H:i') }} - {{ $horario->hora_fin->format('H:i') }}</span>
                </div>
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Aula:</span>
                    <span class="font-medium">{{ $horario->aula->nombre }}</span>
                </div>
                @if($horario->asistencia_hoy)
                <div class="flex justify-between text-sm">
                    <span class="text-gray-600">Registrado a las:</span>
                    <span class="font-medium {{ $horario->asistencia_hoy->estado == 'tardanza' ? 'text-orange-600' : 'text-green-600' }}">
                        {{ $horario->asistencia_hoy->hora_registro }}
                        @if($horario->asistencia_hoy->estado == 'tardanza')
                        <span class="text-xs">(Tardanza)</span>
                        @endif
                    </span>
                </div>
                @endif
            </div>

            @php
                $horaActual = now();
                $horaInicio = \Carbon\Carbon::parse($horario->hora_inicio);
                $horaFin = \Carbon\Carbon::parse($horario->hora_fin);
                $minutosAntes = $horaInicio->copy()->subMinutes(15);
                $minutosDespues = $horaFin->copy()->addMinutes(15);
                $enHorarioPermitido = $horaActual->between($minutosAntes, $minutosDespues);
            @endphp

            @if($horario->ya_registrado)
            <button class="w-full bg-green-500 text-white px-4 py-2 rounded-lg text-sm flex items-center justify-center cursor-not-allowed" disabled>
                ✅ Asistencia Registrada
            </button>
            <a href="{{ route('docente.asistencia.show', $horario->asistencia_hoy) }}" 
               class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm flex items-center justify-center mt-2">
                👁️ Ver Detalle
            </a>
            @elseif($enHorarioPermitido)
            <a href="{{ route('docente.asistencia.create', $horario) }}" 
               class="w-full bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg text-sm flex items-center justify-center">
                📷 Registrar Asistencia
            </a>
            @else
            <button class="w-full bg-gray-400 text-white px-4 py-2 rounded-lg text-sm flex items-center justify-center cursor-not-allowed" disabled>
                ⏰ Fuera de Horario
            </button>
            <p class="text-xs text-gray-500 text-center mt-2">
                Disponible 15 min antes y después de la clase
            </p>
            @endif
        </div>
        @endforeach
    </div>
    @else
    <!-- Sin horarios hoy -->
    <div class="bg-yellow-50 border border-yellow-200 rounded-lg p-6 text-center">
        <div class="text-yellow-600 text-4xl mb-4">📅</div>
        <h3 class="text-xl font-semibold text-yellow-800 mb-2">No tiene clases hoy</h3>
        <p class="text-yellow-700">No hay horarios programados para el día de hoy.</p>
    </div>
    @endif
</div>
@endsection