@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <div class="max-w-4xl mx-auto">
        <!-- Header -->
        <div class="mb-8">
            <div class="flex justify-between items-center">
                <div>
                    <h1 class="text-3xl font-bold text-gray-800">Detalle de Asistencia</h1>
                    <p class="text-gray-600">Registro del {{ $asistencia->fecha->format('d/m/Y') }}</p>
                </div>
                <a href="{{ route('docente.asistencia.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    ← Volver
                </a>
            </div>
        </div>

        <div class="grid grid-cols-1 lg:grid-cols-2 gap-6">
            <!-- Información de la Asistencia -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-semibold mb-4 text-gray-800">Información del Registro</h3>
                
                <div class="space-y-4">
                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Materia</label>
                            <p class="mt-1 text-gray-900">{{ $asistencia->horario->grupoMateria->materia->sigla }} - {{ $asistencia->horario->grupoMateria->materia->nombre }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Grupo</label>
                            <p class="mt-1 text-gray-900">{{ $asistencia->horario->grupoMateria->grupo->sigla_grupo }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Aula</label>
                            <p class="mt-1 text-gray-900">{{ $asistencia->horario->aula->nombre }}</p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Horario Clase</label>
                            <p class="mt-1 text-gray-900">{{ $asistencia->horario->hora_inicio->format('H:i') }} - {{ $asistencia->horario->hora_fin->format('H:i') }}</p>
                        </div>
                    </div>

                    <div class="grid grid-cols-2 gap-4">
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Estado</label>
                            <p class="mt-1">
                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                    {{ $asistencia->estado == 'presente' ? 'bg-green-100 text-green-800' : 'bg-orange-100 text-orange-800' }}">
                                    {{ $asistencia->estado == 'presente' ? 'Presente' : 'Tardanza' }}
                                </span>
                            </p>
                        </div>
                        <div>
                            <label class="block text-sm font-medium text-gray-700">Hora Registro</label>
                            <p class="mt-1 text-gray-900">{{ $asistencia->hora_registro }}</p>
                        </div>
                    </div>

                    @if($asistencia->observaciones)
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Observaciones</label>
                        <p class="mt-1 text-gray-900 bg-gray-50 p-3 rounded border">{{ $asistencia->observaciones }}</p>
                    </div>
                    @endif
                </div>
            </div>

            <!-- Evidencia Fotográfica -->
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-semibold mb-4 text-gray-800">Evidencia Fotográfica</h3>
                
                @if($asistencia->foto_evidencia)
                <div class="text-center">
                    <img src="{{ Storage::url('asistencias/' . $asistencia->foto_evidencia) }}" 
                         alt="Evidencia de asistencia" 
                         class="rounded-lg shadow-md mx-auto max-w-full h-auto max-h-96">
                    <p class="text-sm text-gray-500 mt-2">Foto tomada al momento del registro</p>
                    
                    <div class="mt-4">
                        <a href="{{ Storage::url('asistencias/' . $asistencia->foto_evidencia) }}" 
                           target="_blank" 
                           class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-lg inline-flex items-center">
                            <span class="mr-2">🔍</span> Ver en tamaño completo
                        </a>
                    </div>
                </div>
                @else
                <div class="text-center py-8">
                    <div class="text-gray-400 text-4xl mb-4">📷</div>
                    <p class="text-gray-500">No hay evidencia fotográfica disponible</p>
                </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection