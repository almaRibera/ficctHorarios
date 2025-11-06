@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Aula: {{ $aula->nombre }}</h1>
                <p class="text-gray-600">Información detallada del aula</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.aulas.edit', $aula) }}" 
                   class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center">
                    ✏️ Editar
                </a>
                <a href="{{ route('admin.aulas.index') }}" 
                   class="bg-gray-500 hover:bg-gray-600 text-white px-4 py-2 rounded-lg">
                    ← Volver
                </a>
            </div>
        </div>
    </div>

    <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
        <!-- Información Principal -->
        <div class="lg:col-span-2">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-semibold mb-4 text-gray-800">Información General</h3>
                
                <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre del Aula</label>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $aula->nombre }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Piso</label>
                        <p class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Piso {{ $aula->piso }}
                            </span>
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tipo</label>
                        <p class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $aula->tipo == 'teorica' ? 'bg-blue-100 text-blue-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ $aula->tipo_completo }}
                            </span>
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Estado</label>
                        <p class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $aula->estado == 'disponible' ? 'bg-green-100 text-green-800' : 
                                   ($aula->estado == 'ocupada' ? 'bg-red-100 text-red-800' : 'bg-yellow-100 text-yellow-800') }}">
                                {{ $aula->estado_texto }}
                            </span>
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Capacidad</label>
                        <p class="mt-1 text-gray-900">{{ $aula->capacidad ? $aula->capacidad . ' personas' : 'No especificada' }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fecha de Registro</label>
                        <p class="mt-1 text-gray-900">{{ $aula->created_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>

                <!-- Equipamiento -->
                @if($aula->equipamiento)
                <div class="mt-6">
                    <label class="block text-sm font-medium text-gray-700">Equipamiento</label>
                    <p class="mt-1 text-gray-900 bg-gray-50 p-3 rounded border">{{ $aula->equipamiento }}</p>
                </div>
                @endif

                <!-- Observaciones -->
                @if($aula->observaciones)
                <div class="mt-4">
                    <label class="block text-sm font-medium text-gray-700">Observaciones</label>
                    <p class="mt-1 text-gray-900 bg-gray-50 p-3 rounded border">{{ $aula->observaciones }}</p>
                </div>
                @endif
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-semibold mb-4 text-gray-800">Acciones Rápidas</h3>
                
                <!-- Cambiar Estado -->
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Cambiar Estado</label>
                    <form action="{{ route('admin.aulas.cambiar-estado', $aula) }}" method="POST" class="flex gap-2">
                        @csrf
                        <select name="estado" class="flex-1 border border-gray-300 rounded-lg px-3 py-2 text-sm">
                            <option value="disponible" {{ $aula->estado == 'disponible' ? 'selected' : '' }}>Disponible</option>
                            <option value="ocupada" {{ $aula->estado == 'ocupada' ? 'selected' : '' }}>Ocupada</option>
                            <option value="mantenimiento" {{ $aula->estado == 'mantenimiento' ? 'selected' : '' }}>Mantenimiento</option>
                        </select>
                        <button type="submit" class="bg-blue-500 hover:bg-blue-600 text-white px-3 py-2 rounded-lg text-sm">
                            ✅
                        </button>
                    </form>
                </div>

                <!-- Información Adicional -->
                <div class="space-y-3">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Última Actualización</label>
                        <p class="text-sm text-gray-900">{{ $aula->updated_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <!-- Eliminar Aula -->
                    <div class="pt-4 border-t border-gray-200">
                        <form action="{{ route('admin.aulas.destroy', $aula) }}" method="POST">
                            @csrf
                            @method('DELETE')
                            <button type="submit" 
                                    class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm flex items-center justify-center"
                                    onclick="return confirm('¿Está seguro de eliminar esta aula?')">
                                🗑️ Eliminar Aula
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection