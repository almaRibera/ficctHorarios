@extends('layouts.app')

@section('content')
<div class="container mx-auto px-4 py-6">
    <!-- Header -->
    <div class="mb-8">
        <div class="flex justify-between items-center">
            <div>
                <h1 class="text-3xl font-bold text-gray-800">Materia: {{ $materia->sigla }}</h1>
                <p class="text-gray-600">Información detallada de la materia</p>
            </div>
            <div class="flex gap-2">
                <a href="{{ route('admin.materias.edit', $materia) }}" 
                   class="bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg flex items-center">
                    ✏️ Editar
                </a>
                <a href="{{ route('admin.materias.index') }}" 
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
                        <label class="block text-sm font-medium text-gray-700">Sigla</label>
                        <p class="mt-1 text-lg font-semibold text-gray-900">{{ $materia->sigla }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nombre</label>
                        <p class="mt-1 text-lg text-gray-900">{{ $materia->nombre }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Nivel</label>
                        <p class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                Nivel {{ $materia->nivel }}
                            </span>
                        </p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Tipo</label>
                        <p class="mt-1">
                            <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium 
                                {{ $materia->tipo == 'truncal' ? 'bg-green-100 text-green-800' : 'bg-purple-100 text-purple-800' }}">
                                {{ $materia->tipo_completo }}
                            </span>
                        </p>
                    </div>
                </div>

                <!-- Fechas -->
                <div class="mt-6 grid grid-cols-2 gap-4">
                    <div>
                        <label class="block text-sm font-medium text-gray-700">Fecha de Registro</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $materia->created_at->format('d/m/Y H:i') }}</p>
                    </div>

                    <div>
                        <label class="block text-sm font-medium text-gray-700">Última Actualización</label>
                        <p class="mt-1 text-sm text-gray-900">{{ $materia->updated_at->format('d/m/Y H:i') }}</p>
                    </div>
                </div>
            </div>
        </div>

        <!-- Acciones Rápidas -->
        <div class="lg:col-span-1">
            <div class="bg-white rounded-lg shadow p-6">
                <h3 class="text-xl font-semibold mb-4 text-gray-800">Acciones</h3>
                
                <div class="space-y-3">
                    <a href="{{ route('admin.materias.edit', $materia) }}" 
                       class="w-full bg-green-500 hover:bg-green-600 text-white px-4 py-2 rounded-lg text-sm flex items-center justify-center">
                        ✏️ Editar Materia
                    </a>
                    
                    <form action="{{ route('admin.materias.destroy', $materia) }}" method="POST">
                        @csrf
                        @method('DELETE')
                        <button type="submit" 
                                class="w-full bg-red-500 hover:bg-red-600 text-white px-4 py-2 rounded-lg text-sm flex items-center justify-center"
                                onclick="return confirm('¿Está seguro de eliminar esta materia?')">
                            🗑️ Eliminar Materia
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection